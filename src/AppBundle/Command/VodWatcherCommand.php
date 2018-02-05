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
        $this
            ->setName('vod:watcher')
            ->setDescription('监控任务队列')
            ->addArgument('tube_name', InputArgument::REQUIRED, 'tube name')
            ->addArgument('task_count', inputArgument::REQUIRED, 'Concurrency Level')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->tube_name  = $argument = $input->getArgument('tube_name');
        $this->task_count = $input->getArgument('task_count');
        $this->host       = $this->getContainer()->getParameter('beanstalk_host');
        $this->port       = $this->getContainer()->getParameter('beanstalk_port');
        $this->timeout    = $this->getContainer()->getParameter('beanstalk_timeout');
        $this->logger     = $this->getContainer()->get('logger');
        $this->kernel     = $this->getContainer()->get('kernel');
        $web_path          = $this->kernel ->getProjectDir();
        $pheanstalk       = null;
        try
        {

            $pheanstalk = new Pheanstalk($this->host, $this->port, $this->timeout);
            $pheanstalk->useTube($this->tube_name);
            $output->writeln('sfs');
            //加入一条空消息
            $msg = array('handle'=>'nothing', 'data'=>'');
            $pheanstalk->put(json_encode($msg));
            $this->task_id = mt_rand();
            //启动主任务
           /* if(!defined('PHP_BINARY'))
                $cmd = "php " . $web_path . " bin/console vod:worker $tube $task_id $task_count";
            else
                $cmd = PHP_BINARY . " " . $web_path . " bin/console vod:worker $tube $task_id $task_count";*/
            $cmd = new Application($this->kernel);
            $input = new ArrayInput(array(
                'command' => 'vod:worker',
                'tube_name' => $this->tube_name,
                'task_id' => $this->task_id,
                'task_count' => $this->task_count,
            ));
            $output = new NullOutput();
            $cmd->run($input, $output);
            $output->writeln('sf11s');
            $step = 0;
            $start_time = time();
            $output->writeln('sf2223s');
            while(true)
            {
                $result = $pheanstalk->statsTube($this->tube_name);

                $total_jobs = $result->offsetGet('total-jobs') - $result->offsetGet('cmd-delete') - $result->offsetGet('current-jobs-buried');
                $output->writeln('sf333s');
                if($total_jobs == 0 && $step < 2)
                {
                    $current_watching = $result->offsetGet('current-watching');
                    //为了循环关闭并发的任务队列
                    $msg = array('handle'=>'', 'data'=>array('consumer'=>$current_watching, 'inc'=>0, 'tube'=>$this->tube_name, 'before'=>($step==0?true:false), 'task_id'=>$this->task_id));
                    $pheanstalk->put(json_encode($msg), 4096);
                    $step++;
                }
                else if($result->offsetGet('current-using')==1 && $result->offsetGet('current-watching')==0 && $result->offsetGet('current-waiting')==0)
                {
                    if($step == 2 || time() - $start_time > 5)
                    {
                        if($total_jobs > 0)
                            $this->logger->error("queue_status: " . print_r($result, true));
                        break;
                    }
                }
                sleep(1);
            }
        }catch(ServerException $e)
        {
            $error_msg = 'Pheanstalk Error in watcher: ' . $e->getMessage();
            $this->logger->error($error_msg,'','error');
            echo $error_msg, "\n";
            exit();
        }catch(Exception $e)
        {
            $error_msg = 'Pheanstalk Error in watcher: ' . $e->getMessage();
            $this->logger->error($error_msg,'','error');
            echo $error_msg, "\n";
            exit();
        }
    }

}
