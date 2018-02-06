<?php

namespace AppBundle\Command;

use Pheanstalk\Exception;
use Pheanstalk\Exception\ServerException;
use Pheanstalk\Pheanstalk;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class VodWatcherCommand extends ContainerAwareCommand
{
    private $host ;
    private $port ;
    private $timeout ;
    private $tube_name ;
    private $task_id ;
    private $task_count ;
    private $logger ;
    private $kernel ;
    protected function configure()
    {
        $this->setName('vod:watcher')
            ->setDescription('监控任务队列')
            ->addArgument('tube_name', InputArgument::REQUIRED, 'tube name')
            ->addArgument('task_count', inputArgument::REQUIRED, 'Concurrency Level');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->tube_name  = $argument = $input->getArgument('tube_name');               //获取任务名称
        $this->task_count = $input->getArgument('task_count');                          //获取任务并发数量
        $this->host       = $this->getContainer()->getParameter('beanstalk_host');      //获取beanstalk链接的地址
        $this->port       = $this->getContainer()->getParameter('beanstalk_port');      //获取beanstalk端口
        $this->timeout    = $this->getContainer()->getParameter('beanstalk_timeout');  //获取beanstalk链接超时配置
        $this->logger     = $this->getContainer()->get('logger');                           //获取日志组件
        $this->kernel     = $this->getContainer()->get('kernel');                           //获取kernel组件
        $web_path          = $this->kernel ->getProjectDir();                                    //获取项目根目录
        $pheanstalk       = null;
        $msg_suffer = "Vod:Watcher----";
        try
        {

            $pheanstalk = new Pheanstalk($this->host, $this->port, $this->timeout);
            $pheanstalk->useTube($this->tube_name);

            //加入一条空任务,防止watcher执行过快而过早的关闭
            $msg = array('handle'=>'nothing', 'data'=>'');
            $pheanstalk->put(json_encode($msg));
            $this->task_id = mt_rand();                                                         //这是任务id,随机数
            //启动主任务
            $cmd =  "php " .$web_path. "/bin/console vod:worker {$this->tube_name} {$this->task_id} {$this->task_count}";
            exec("$cmd >/dev/null &");
            $step = 0;
            $start_time = time();
            while(true)
            {
                $result = $pheanstalk->statsTube($this->tube_name);
                /*
                 * total-jobs: 从管道开始使用开始到现在总共的任务数量
                 * cmd-delete: 删除的管道中的任务的数量
                 * current-jobs-buried: 当前管道中挂起的任务数量
                 */
                $total_jobs = $result->offsetGet('total-jobs') - $result->offsetGet('cmd-delete') - $result->offsetGet('current-jobs-buried');
                //添加任务结束前标志和任务结束时的处理标志
                /*
                 * 因为有并发,所以会有A,B两个任务存在,关键点是可以判断任务是否执行一周
                 * 当before=true的时候,说明所有的任务已经轮转一圈,所以需要任务收尾工作的处理,
                 * 当before=false的时候,说明收尾工作处理完毕可以执行关闭该任务的工作了
                 */
                if($total_jobs == 0 && $step < 2)
                {
                    $current_watching = $result->offsetGet('current-watching');
                    //为了循环关闭并发的任务队列
                    $msg = array('handle'=>'', 'data'=>array('consumer'=>$current_watching, 'inc'=>0, 'tube'=>$this->tube_name, 'before'=>($step==0?true:false), 'task_id'=>$this->task_id));
                    $pheanstalk->put(json_encode($msg), 4096);
                    $step++;
                }else if($result->offsetGet('current-using')==1 && $result->offsetGet('current-watching')==0 && $result->offsetGet('current-waiting')==0)
                {
                    /*
                     * 这个应该判断的是只有watcher命令正在工作的时候,这个时候就需要关闭该watcher,说明已经没有需要处理的任务了
                     *
                     * current-using:->useTube()功能函数,这个时候监控的所有正在使用该链接的任务数量,如果为1,则只有watcher正在处理工作
                     * current-watching: 指的是 $pheanstalk->watch($this->tube_name);的链接数量
                     * current-waiting:指的是那些正在等待处理的链接数$pheanstalk->reserve();使用此tube打开连接并且等待响应的连接数
                     */
                    if($step == 2 || time() - $start_time > 5)
                    {
                        if($total_jobs > 0)
                            $this->logger->error($msg_suffer."queue_status: " . print_r($result, true));
                        break;
                    }
                }
                sleep(1);
            }
        }catch(ServerException $e)
        {
            $error_msg = $msg_suffer.'Pheanstalk Error in watcher: ' . $e->getMessage();
            $this->logger->error($error_msg);
            echo $error_msg, "\n";
            exit();
        }catch(Exception $e)
        {
            $error_msg =$msg_suffer.'Pheanstalk Error in watcher: ' . $e->getMessage();
            $this->logger->error($error_msg);
            echo $error_msg, "\n";
            exit();
        }
    }

}
