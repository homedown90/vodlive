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
use Symfony\Component\Console\Output\OutputInterface;

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
    const  TASK_SUSPEND = 1;//挂起
    const  TASK_RETRY=2;//重试
    protected function configure()
    {
        $this
            ->setName('vod:worker')
            ->setDescription('消费者')
            ->addArgument('tube_name', InputArgument::REQUIRED, 'tube name')
            ->addArgument('task_id', InputArgument::REQUIRED, 'task id')
            ->addArgument('task_count', InputArgument::OPTIONAL, 'Concurrency level',1);
    }
    private function connectBeanstalk()
    {

        try {
            $pheanstalk = new Pheanstalk($this->host, $this->port, $this->timeout);
            $pheanstalk->useTube($this->tube_name);
            $pheanstalk->watch($this->tube_name);
        } catch (Exception $e) {
            $this->logger->error('Pheanstalk START Failed: ' . $e->getMessage());
            return false;
    }

    return $pheanstalk;
    }
    public function registerHandle($handle, $fp) {
        $this->handle_map[ $handle ] = $fp;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->tube_name  = $input->getArgument('tube_name');
        $this->task_id    = $input->getArgument('task_id');
        $this->orm = $this->getContainer()->get('doctrine');
        $this->task_count = $input->getArgument('task_count');
        $this->host       = $this->getContainer()->getParameter('beanstalk_host');
        $this->port       = $this->getContainer()->getParameter('beanstalk_port');
        $this->timeout    = $this->getContainer()->getParameter('beanstalk_timeout');

        $this->logger     = $this->getContainer()->get('logger');
        $this->kernel     = $this->getContainer()->get('kernel');
        $this->pheanstalk       = null;
        if (!($this->pheanstalk = $this->connectBeanstalk())) {
                exit();
        }
        if ($this->task_count > 1) {
            $task_count = $this->task_count - 1;
            $kicked = $this->pheanstalk->kick(1000000);
            /*添加推送任务*/
            $tube_manager = $this->getContainer()->get($this->tube_name);
            $tube_manager->main($this);
            if ($kicked > 0) {
                $cmd = new Application($this->kernel);
                for ($i = 0; $i < $task_count; $i++) {
                    $input  = new ArrayInput(array(
                        'command'   => 'vod:worker',
                        'tube_name' => $this->tube_name,
                        'task_id'   => $this->task_id,
                    ));
                    $output = new NullOutput();
                    $cmd->run($input, $output);
                }
            }
        }
        $task_statistic      = 0;
        $begin_time          = time();
        while (true) {
            try {
                $this->cur_job = $this->pheanstalk->reserve();
            } catch (Exception $e) {
                $this->logger->error('Pheanstalk reserve Failed: ' . $e->getMessage(), '', 'error');
                while (!$this->connectBeanstalk())
                {
                    sleep(1);
                }
                continue;
            }

            $data = $this->cur_job->getData();
            $obj  = UtilTool::json_decode_safe($data);

            if (isset($obj->validity) && $obj->validity) {
                if (time() > $obj->validity) {
                    try {
                        $this->pheanstalk->delete($this->cur_job);
                        $g_cur_job = NULL;
                    } catch (Exception $e) {
                        $this->logger->error('Pheanstalk delete Failed: ' . $e->getMessage(), '', 'error');
                        $this->logger->error("Task_data: $data");
                    }
                    continue;
                }
            }

            if (isset($obj->handle) && isset($g_handle_map[$obj->handle])) {
                ++$task_statistic;
                $tube_manager = $this->getContainer()->get($this->handle_map[$obj->handle][0]);
                $result = call_user_func(array($tube_manager,$this->handle_map[$obj->handle][1]),$obj->data);
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
                        $this->logger->error('Pheanstalk release Failed: ' . $e->getMessage(), '', 'error');
                        $this->logger->error("Task_data: $data");
                    }
                    continue;
                }
            }else if (isset($obj->handle) && empty($obj->handle) && isset($obj->data->before) && @$obj->data->task_id == $this->task_id )
            {
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
                    $this->logger->error('Pheanstalk release Failed: ' . $e->getMessage(),'','error');
                     $this->logger->error("Task_data: $data");
                }
            } else if (isset($obj->handle)) {
                if ($obj->handle != 'nothing') {
                     $this->logger->error("Handle {$obj->handle} not found!");
                     $this->logger->error("Task_data: $data");
                }
            } else {
                 $this->logger->error("InvalidTask: $data",'','error');
            }

            try {
                if ($this->cur_job) {
                    $this->pheanstalk->delete($this->cur_job);
                    $this->cur_job = NULL;
                }
            } catch (Exception $e) {
                $this->logger->error('Pheanstalk delete Failed: ' . $e->getMessage(), '', 'error');
                $this->logger->error("Task_data: $data");
            }
        }

        $g_task_begin_time = time() - $begin_time;
        echo "QUIT $task_statistic $g_task_begin_time\n";
    }
    private function pushTask($handle, $data, $delay = 0, $pri = 1024, $ttr = 600, $validity = 0) {
        if ($validity)
            $msg = json_encode(array('handle' => $handle, 'data' => $data, 'validity' => time() + $validity));
        else
            $msg = json_encode(array('handle' => $handle, 'data' => $data));

        return $this->pheanstalk->put($msg, $pri, $delay, $ttr);
    }

    private function deleteJob() {
        if ($this->cur_job) {
            try {
                $this->pheanstalk->delete($this->cur_job);
                $this->cur_job = NULL;
                return TRUE;
            } catch (Exception $e) {
                $this->logger->error('deleteJob Failed: ' . $e->getMessage(),'','error');
            }
        }
        return FALSE;
    }
}

