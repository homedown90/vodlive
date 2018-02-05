<?php

namespace AppBundle\Controller;

use AppBundle\Entity\VodFile;
use AppBundle\Entity\VodList;
use Extend\Symfony\FilterRequest;
use Extend\Util\UpLoad;
use Extend\Util\UtilTool;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

class VodManageController extends Controller
{
    /**
     * 点播列表的展示界面
     *
     * @return Response
     */
    public function showAction()
    {
        $kernel = $this->get('kernel');
        $cmd = new Application($kernel);
        $input = new ArrayInput(array(
            'command' => 'vod:watcher',
            'tube_name' => 'task.check_upload',
            'task_count' => 2,
        ));
        $output = new NullOutput();
        $cmd->run($input, $output);

        return $this->render('@App/VodManage/show.ajax.twig', array(
            'table'=>array(
                'outer_div'=>'vod'
            )
        ));
    }

    /**
     * 获取点播信息
     *
     * @return Response
     */
    public function getDataAction(Request $request)
    {
        $data = array('total'=>0,'rows'=>array());
        try{
            //关闭缓存
            $logger = $this->get('logger');
            $post = $request->request->all();
            $get = $request->query->all();
            $logger->error('--getDataAction  -post-'.print_r($post,true));
            $logger->error('--getDataAction  -get-'.print_r($get,true));
            $ry = $this->getDoctrine()->getRepository('AppBundle:VodList');
            if(empty($post['search']))
            {
                $post['search'] = array();
            }
            $data = $ry->searchVod($post['search'],$post['offset'],$post['limit'],'v.id',$post['order']);


        }catch (\Exception $e){
            $logger = $this->get('logger');
            $logger->error('--getDataAction  --'.$e->getMessage());
        }
        return new Response(  json_encode($data), 200, array('Content-Type' => 'application/json') );
    }

    /**
     * 点播添加界面
     *
     */
    public function showAddAction()
    {
//        $repository = $this->getDoctrine()->getRepository('AppBundle:VodClass');
//        $class_list = $repository->findAllClassToSelect();
//        $class_json = json_encode($class_list);
        return $this->render('@App/VodManage/add.ajax.twig');
    }
    /**
     * 编辑添加界面
     *
     */
    public function showEditAction(Request $request)
    {
        $post = $request->query->all();
        $edit_id = $post['id'];
        $repository =  $this->getDoctrine()->getRepository('AppBundle:VodList');
        $vod = $repository->getVodAndFileById($edit_id);
        return $this->render('@App/VodManage/edit.ajax.twig',array('vod'=>$vod));
    }
    /**
     * 添加点播信息
     *
     */
    public function addVodAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{

            $request = FilterRequest::createFromGlobals();
            $vod_info = $request->request->get('vod');
            //------------判断是否存在该文件

            //------------保存该文件
            $vodObject = new VodList();
            $vodObject->setTitle($vod_info['title']);
            $vodObject->setDescription($vod_info['description']);
            $vodObject->setVideoId($vod_info['fileId']);
            $vodObject->setClassId($vod_info['classId']);
            $vodObject->setCreator(UtilTool::getUserId());
            $em = $this->getDoctrine()->getManager();
            // 告诉Doctrine你希望（最终）存储Product对象（还没有语句执行）
            $em->persist($vodObject);
            $em->flush();
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--addVodAction--'.$e->getMessage());
        }

        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     *
     * 编辑视频信息
     *
     * @return  Response
     */
    public function editVodAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{
            $request = FilterRequest::createFromGlobals();
            $vod_info = $request->request->get('vod');
            $obj_vod = $this->getDoctrine()->getRepository('AppBundle:VodList')->find( $vod_info['id']);
            $obj_vod->setTitle($vod_info['title']);
            $obj_vod->setDescription($vod_info['description']);
            $obj_vod->setClassId($vod_info['classId']);
            if(!empty($vod_info['fileId']))
            {
                $obj_vod->setVideoId($vod_info['fileId']);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--editVodAction--'.$e->getMessage());
        }
        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     *
     * 删除视频信息
     *
     * @return Response
     */
    public function deleteVodAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{

            $request = FilterRequest::createFromGlobals();
            $vod_id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $vod_info = $em->getRepository('AppBundle:VodList')->find( $vod_id);
            $em->remove($vod_info);
            $em->flush();

        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--deleteVodAction--'.$e->getMessage());
        }

        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }
    public function hlsVodAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{

        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--addVodAction--'.$e->getMessage());
        }

        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     *上传文件
     */
    public function upLoadAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{
//            sleep(10);
            $logger = $this->get('logger');
            set_time_limit (0);
            $response = new Response();
            //关闭缓存
            $response->setExpires(new \DateTime("1997-7-26 05:00:00"));
            $response->setLastModified(new \DateTime() );
            $response->headers->set("Cache-Control","no-store, no-cache, must-revalidate");
            $response->headers->set("Cache-Control","post-check=0, pre-check=0",false);
            $response->headers->set("Pragma"," no-cache");
            //获取post信息
            $request = FilterRequest::createFromGlobals();
            $post = $request->request->all();
            $files = $request->files->all();
            $upload_url = $this->getParameter('brochures_directory'); // 这里得到的是app目录的绝对路

            //用于断点续传，验证指定分块是否已经存在，避免重复上传
             $uploader =  new UpLoad($upload_url,$post,$files['file']);
             $logger->error('--upLoadAction--post  --'.print_r($post,true));
             $logger->error('--upLoadAction--file  --'.print_r($files,true));
             if($uploader->uploadChunk() !== false)
             {

             }else{
                 $data['status'] = 1;
                 $data['msg'] = $uploader->getErrorMsg();
             }

        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--upLoadAction  --'.$e->getMessage());
        }
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * 合并分块文件
     *
     *
     */
    function chunkCheckAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{
            $logger = $this->get('logger');
            $response = new Response();
            $request = FilterRequest::createFromGlobals();
            $post = $request->request->all();
            $upload_url = $this->getParameter('brochures_directory'); // 这里得到的是app目录的绝对路

            if (file_exists(rtrim($upload_url,'/')."/chunk/{$post['upload_path']}/{$post['chunk_index']}") ) {
                $data['is_exist'] = 1;
                $data['msg'] = 'chunk已存在';
            }else{
                $data['is_exist'] = 0;
            }
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger->error('--chunkCheckAction  --'.$e->getMessage());
        }
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function uploadFinishedCheckAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{
            $response = new Response();
            //关闭缓存
            $request = FilterRequest::createFromGlobals();
            $post = $request->request->all();
            $files = $request->files->all();
//            $upload_url = $this->get('kernel')->getProjectDir()."/web/upload"; // 这里得到的是app目录的绝对路
            $upload_url = $this->getParameter('brochures_directory'); // 这里得到的是app目录的绝对路
            $file_rsy = $this->getDoctrine()->getRepository("AppBundle:VodFile");
            $obj_file = $file_rsy->findOneById($post['file_id']);

            if(UpLoad::checkUploadFinished($upload_url,$obj_file))
            {
                $obj_file->setIsUpload(true);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }else{
                throw new \Exception("上传信息不完整,请重新上传");
            }
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--chunksMergeAction  --'.$e->getMessage());
        }
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     *
     * 校验上传的文件是否存在，如果不存在则返回随机的文件名
     *
     * @param $post
     * @param UpLoad $upload
     * @return array
     */
    function md5CheckAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{
            $response = new Response();
            //关闭缓存
            $upload_url = $this->getParameter('brochures_directory'); // 这里得到的是app目录的绝对路
            $request = FilterRequest::createFromGlobals();
            $post = $request->request->all();
            $repository =  $this->getDoctrine()->getRepository('AppBundle:VodFile');
            $md5_file = $repository->findByJsMd5($post['md5']);
            if(!empty($md5_file)){
                $file_name = $md5_file[0]->getSaveName();
                $file_url = rtrim($upload_url,"/").'/'.Upload::$chunk_file.'/'.$file_name;
                UpLoad::checkForCreat($file_url);
                if($md5_file[0]->getIsUpload() == false)
                {
                    $data['is_finish'] = 0;
                }else{
                    $data['is_finish'] = 1;
                }
                $data['upload_path'] = $md5_file[0]->getSaveName();
                $data['file_id'] = $md5_file[0]->getId();
            }else{
                $file_name = UpLoad::createRandFileName();
                $file_url = rtrim($upload_url,"/").'/'.Upload::$chunk_file.'/'.$file_name;
                UpLoad::checkForCreat($file_url);
                $file_map = new VodFile();
                $file_map->setJsMd5($post['md5']);
                $file_map->setSaveName($file_name);
                $file_map->setSize($post['size']);
                $file_map->setType($post['ext']);
                $file_map->setName($post['name']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($file_map);
                $em->flush();
                $data['is_finish'] = 0;
                $data['file_id'] = $file_map->getId();
                $data['upload_path'] = $file_map->getSaveName();
            }

        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--md5CheckAction  --'.$e->getMessage());
        }

        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
//        $response->setStatusCode(Response::HTTP_REQUEST_TIMEOUT);
        return $response;
    }
}
