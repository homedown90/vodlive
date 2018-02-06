<?php
/**
 * Created by PhpStorm.
 * User: changtao
 * Date: 2018/2/1
 * Time: 16:42
 */
namespace AppBundle\Service\Task;

use Extend\Util\UpLoad;

class MergeChunk
{
    private $server_container;
    public function __construct($container)
    {
        $this->server_container = $container;
    }

    private $msg = "mergerChunk--";
    public function main($worker)
    {
        $file_rsy = $this->server_container->get('doctrine')->getRepository('AppBundle:VodFile');
        $logger =$this->server_container->get('logger');
        $file_list = $file_rsy->getNotMergedVod(true);
        if(empty($file_rsy))
        {
            $logger->error("{$this->msg}.不存在未合并的视频");
            return false;
        }
        $has_task = false;
        foreach($file_list as $key => $obj_file)
        {

            $worker->pushTask('merge_chunk', $obj_file->getId(), 0, 1024, 600, 300);
            //pushTask($handle, $data, $delay=0, $pri=1024, $ttr = 600, $validity=0)
            $has_task = true;
        }
        return $has_task;
    }

    //自动调用的时候应该首先调用该方法,注册类内部的方法
    public static function register($worker) {
        $worker->registerHandle('merge_chunk', array('task.merge_chunk','mergeChunk'));
    }
    public function mergeChunk($id)
    {
        $upload_url = $this->server_container->getParameter('brochures_directory'); // 这里得到的是app目录的绝对路
        $file_rsy = $this->server_container->get('doctrine')->getRepository("AppBundle:VodFile");
        $obj_file = $file_rsy->findOneById($id);

        if(UpLoad::chunksMerge($upload_url,$obj_file))
        {
            $obj_file->setIsMerge(true);
            $em = $this->$this->server_container->get('doctrine')->getManager();
            $em->flush();
            return VodWorkerCommand::TASK_OK;
        }else{
            return VodWorkerCommand::TASK_RETRY;
        }
    }
}