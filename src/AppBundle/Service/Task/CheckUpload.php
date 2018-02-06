<?php
/**
 * Created by PhpStorm.
 * User: changtao
 * Date: 2018/2/1
 * Time: 16:42
 */
namespace AppBundle\Service\Task;


use AppBundle\Command\VodWorkerCommand;
use Extend\Util\UpLoad;

class CheckUpload
{
    private $server_container;
    private $msg = "checkUpload--";
    public function __construct($container,$logger)
    {
        $this->server_container = $container;
        $this->logger           = $logger;
    }
    public function main($worker)
    {
        $file_rsy  = $this->server_container->get('doctrine')->getRepository('AppBundle:VodFile');
        $file_list = $file_rsy->findByIsUpload(false);
        if(empty($file_rsy))
        {
            $this->logger->error("{$this->msg}.不存在未上传的完成的视频");
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
    static function register($worker)
    {
        $worker->registerHandle('check_upload', array('task.check_upload','checkUploadFinished'));
    }
    public function checkUploadFinished($id)
    {
        $upload_url = $this->server_container->getParameter('brochures_directory');                 //获取项目根目录
        $file_rsy   = $this->server_container->get('doctrine')->getRepository("AppBundle:VodFile");//获取VodFile对象
        $obj_file   = $file_rsy->findOneById($id);                                                       //查找未上传成功的视频文件信息
        if(UpLoad::checkUploadFinished($upload_url,$obj_file))
        {
            $obj_file->setIsUpload(true);
            $em = $this->server_container->get('doctrine')->getManager();
            $em->flush();
            return VodWorkerCommand::TASK_OK;
        }else{
            return VodWorkerCommand::TASK_RETRY;
        }
    }
}