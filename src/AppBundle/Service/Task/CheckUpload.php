<?php
/**
 * Created by PhpStorm.
 * User: changtao
 * Date: 2018/2/1
 * Time: 16:42
 */
namespace AppBundle\Service\Task;


class CheckUpload
{
    private $server_container;
    public function __construct($container)
    {
        $this->server_container = $container;
    }

    private $msg = "checkUpload--";
    public function main($worker)
    {
        $file_rsy = $worker->orm->getRepository('AppBundle:VodFile');
        $logger = $worker->logger;
        $file_list = $file_rsy->findByIsUpload(false);
        if(empty($file_rsy))
        {
            $logger->error("{$this->msg}.不存在未上传的完成的视频");
            return false;
        }
        $has_task = false;
        foreach($file_list as $key => $obj_file)
        {

            $worker->pushTask('check_upload', $obj_file->getId(), 0, 1024, 600, 300);
                //pushTask($handle, $data, $delay=0, $pri=1024, $ttr = 600, $validity=0)
            $has_task = true;
        }
        return $has_task;
    }

    //自动调用的时候应该首先调用该方法,注册类内部的方法
    static function register($worker) {
        $worker->registerHandle('check_upload', array('task.check_upload','checkUpload'));
    }
    public function checkUpload()
    {

    }
}