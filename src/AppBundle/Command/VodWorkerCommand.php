<?php

namespace AppBundle\Command;

use Extend\Util\UtilTool;
use Pheanstalk\Exception;
use Pheanstalk\Pheanstalk;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

class VodWorkerCommand extends ContainerAwareCommand
{
    private $host ;
    private $port ;
    private $timeout ;
    private $tube_name ;
    private $task_id ;
    private $task_count ;
    public $logger ;
    public $kernel ;
    public $handle_map=[] ;
    public $cur_job = NULL ;
    public $orm ;
    const  TASK_OK = 0;//挂起
    const  TASK_SUSPEND = 1;//挂起
    const  TASK_RETRY=2;//重试
    private $msg_suffer = "vod:worker---";
    protected function configure()
    {
        $this->setName('vod:worker')
            ->setDescription('消费者')
            ->addArgument('tube_name', InputArgument::REQUIRED, 'tube name')
            ->addArgument('task_id', InputArgument::REQUIRED, 'task id')
            ->addArgument('task_count', InputArgument::OPTIONAL, 'Concurrency level',1);
    }
    public function connectBeanstalk()
    {

        try {
            $pheanstalk = new Pheanstalk($this->host, $this->port, $this->timeout);
            $pheanstalk->useTube($this->tube_name);//使用该频道,用来添加任务
            $pheanstalk->watch($this->tube_name);//监控该管道,用来获取管道任务
        } catch (Exception $e) {
            $this->logger->error($this->msg_suffer.'Pheanstalk START Failed: ' . $e->getMessage());
            return false;
        }

        return $pheanstalk;
    }
    public function registerHandle($handle, $fp) {
        $this->handle_map[ $handle ] = $fp;
    }
    public function pushTask($handle, $data, $delay = 0, $pri = 1024, $ttr = 600, $validity = 0) {
        if ($validity)
            $msg = json_encode(array('handle' => $handle, 'data' => $data, 'validity' => time() + $validity));
        else
            $msg = json_encode(array('handle' => $handle, 'data' => $data));

        return $this->pheanstalk->put($msg, $pri, $delay, $ttr);
    }

    public function deleteJob() {
        if ($this->cur_job) {
            try {
                $this->pheanstalk->delete($this->cur_job);
                $this->cur_job = NULL;
                return TRUE;
            } catch (Exception $e) {
                $this->logger->error($this->msg_suffer.'deleteJob Failed: ' . $e->getMessage());
            }
        }
        return FALSE;
    }
    public function resetAlarm($secs = 120) {
        $disable_alarm = $this->getContainer()->get('disable_alarm');
        if ($disable_alarm)
        {
            return;
        }else{
            pcntl_alarm($secs);
        }
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->tube_name  = $input->getArgument('tube_name');                       //获取管道名称
        $this->task_id    = $input->getArgument('task_id');                         //获取任务id
        $this->task_count = $input->getArgument('task_count');                      //任务并发量
        $this->orm        = $this->getContainer()->get('doctrine');                     //获取orm容器
        $this->host       = $this->getContainer()->getParameter('beanstalk_host');  //获取beanstalk的主机地址
        $this->port       = $this->getContainer()->getParameter('beanstalk_port');  //获取beanstalk的端口地址
        $this->timeout    = $this->getContainer()->getParameter('beanstalk_timeout');//获取beanstalk的链接超时时间
        $this->task       = $this->getContainer()->getParameter('task');              //获取管道和服务之间的映射关系
        $this->logger     = $this->getContainer()->get('logger');                        //获取日志容器
        $this->kernel     = $this->getContainer()->get('kernel');                        //获取kernel内核容器
        $this->pheanstalk       = null;
        $web_root =  $this->kernel->getProjectDir();                                          //获取站点根目录
        //----------获取beanstalk链接,使用并监控管道
        if (!($this->pheanstalk = $this->connectBeanstalk())) {
                exit();
        }
        //----------获取管道映射的服务
        $tube_manager = $this->getContainer()->get($this->task["{$this->tube_name}"]);
        //----------注册handler,注册每个服务中的handler用来处理相关的业务
        $tube_manager::register($this);
        //----------添加并发任务
        if ($this->task_count > 1) {
            $task_count = $this->task_count - 1;
            //---------激活之前被挂起的任务
            $kicked = $this->pheanstalk->kick(1000000);
            /*添加推送任务*/
            $this->resetAlarm();
            $has_task = $tube_manager->main($this);
            $this->resetAlarm();
            if ($kicked > 0 || $has_task) {
                for ($i = 0; $i < $task_count; $i++) {
                    $cmd =  "php " .$web_root. "/bin/console vod:worker {$this->tube_name} {$this->task_id}";
                    exec("$cmd >/dev/null &");
                }
            }
        }
        //-----------统计当前任务处理的量
        $task_statistic      = 0;
        //-----------记录开始时间
        $begin_time          = time();
        while (true) {
            try {
                $this->resetAlarm();
                //------消费任务
                $this->cur_job = $this->pheanstalk->reserve();
                $this->resetAlarm();
            } catch (Exception $e) {
                $this->logger->error($this->msg_suffer.'Pheanstalk reserve Failed: ' . $e->getMessage());
                while (!$this->connectBeanstalk())
                {
                    sleep(1);
                }
                continue;
            }
            //--------获取任务数据
            $data = $this->cur_job->getData();
            $obj  = UtilTool::json_decode_safe($data);
            //-------------判断任务的失效时间
            if (isset($obj->validity) && $obj->validity)
            {
                if (time() > $obj->validity) {
                    try {
                        $this->pheanstalk->delete($this->cur_job);
                        $g_cur_job = NULL;
                    } catch (Exception $e) {
                        $this->logger->error($this->msg_suffer.'Pheanstalk delete Failed: ' . $e->getMessage());
                        $this->logger->error($this->msg_suffer."Task_data: $data");
                    }
                    continue;
                }
            }
            //-------------------------判断任务类型,回调任务处理逻辑
            if (isset($obj->handle) && isset($this->handle_map[$obj->handle])) {
                ++$task_statistic;
                $tube_manager = $this->getContainer()->get($this->handle_map[$obj->handle][0]);
                $result       = call_user_func(array($tube_manager, $this->handle_map[$obj->handle][1]), $obj->data);
                //判断cur_job是否过了执行时间?
                if (!$this->cur_job)
                    continue;

                if ($result > 0) {
                    try {
                        switch ($result) {
                            case slef::TASK_SUSPEND: {
                                $this->pheanstalk->bury($this->cur_job);
                                break;
                            }
                            case self::TASK_RETRY :{
                                sleep(1);
                                $this->pheanstalk->release($this->cur_job);
                                break;
                            }
                        }
                    } catch (Exception $e) {
                        $this->logger->error($this->msg_suffer.'Pheanstalk release Failed: ' . $e->getMessage());
                        $this->logger->error($this->msg_suffer."Task_data: $data");
                    }
                    continue;
                }
            }else if (isset($obj->handle) && empty($obj->handle) && isset($obj->data->before) && @$obj->data->task_id == $this->task_id )
            {//----------------------判断是否执行过一圈,则跳出当前使用的管道，并忽略对该管道的监控
                //data--> {consumer:n, inc: n, tube:t, before:before}
                $obj->data->inc = intval($obj->data->inc) + 1;
                $complete       = ($obj->data->inc >= $obj->data->consumer);
                $before         = $obj->data->before;
                try {
                    if (!$complete) {
                        $this->pheanstalk->put(json_encode($obj), 4096);
                    }
                    if (!$before) {
                        if ($this->cur_job){
                            $this->pheanstalk->delete($this->cur_job);
                        }
                        $this->pheanstalk->ignore($obj->data->tube);
                        break;
                    }
                } catch (Exception $e) {
                    $this->logger->error($this->msg_suffer.'Pheanstalk release Failed: ' . $e->getMessage());
                     $this->logger->error($this->msg_suffer."Task_data: $data");
                }
            } else if (isset($obj->handle)) {
                if ($obj->handle != 'nothing') {
                     $this->logger->error($this->msg_suffer."Handle {$obj->handle} not found!");
                     $this->logger->error($this->msg_suffer."Task_data: $data");
                }
            } else {
                 $this->logger->error($this->msg_suffer."InvalidTask: $data");
            }

            try {
                if ($this->cur_job) {
                    $this->pheanstalk->delete($this->cur_job);
                    $this->cur_job = NULL;
                }
            } catch (Exception $e) {
                $this->logger->error($this->msg_suffer.'Pheanstalk delete Failed: ' . $e->getMessage());
                $this->logger->error($this->msg_suffer."Task_data: $data");
            }
        }//end while

        $g_task_begin_time = time() - $begin_time;
        echo "QUIT $task_statistic $g_task_begin_time\n";
    }

}


