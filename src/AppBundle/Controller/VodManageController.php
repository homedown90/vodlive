<?php

namespace AppBundle\Controller;

use AppBundle\Entity\VodMd5File;
use Extend\Symfony\FilterRequest;
use Extend\Util\UpLoad;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class VodManageController extends Controller
{
    /**
     * 点播列表的展示界面
     *
     * @return Response
     */
    public function showAction()
    {
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
    public function getDataAction()
    {
        /*{"total":800,"rows":[{"id":10,"name":"Item 10","price":"$10"},{"id":11,"name":"Item 11","price":"$11"},{"id":12,"name":"Item 12","price":"$12"},{"id":13,"name":"Item 13","price":"$13"},{"id":14,"name":"Item 14","price":"$14"},{"id":15,"name":"Item 15","price":"$15"},{"id":16,"name":"Item 16","price":"$16"},{"id":17,"name":"Item 17","price":"$17"},{"id":18,"name":"Item 18","price":"$18"},{"id":19,"name":"Item 19","price":"$19"}]}
         * */
//        $data = array('total'=>38,'rows'=>array(array('id'=>10,"name"=>"item 10","price"=>"10"),array('id'=>10,"name"=>"item 10","price"=>"10"),array('id'=>10,"name"=>"item 10","price"=>"10")));
        $data = array('total'=>0,'rows'=>array());
        return new Response(  json_encode($data), 200, array('Content-Type' => 'application/json') );
    }

    /**
     * 点播添加界面
     *
     */
    public function showAddAction()
    {
        return $this->render('@App/VodManage/add.ajax.twig');
    }
    /**
     * 添加点播信息
     *
     */
    public function addVodAction()
    {
        var_dump($_FILES);
        var_dump($_POST);
        var_dump($_GET);
        die;
    }
    /**
     *上传文件
     */
    public function upLoadAction()
    {
        $data = array('status' => 0, 'msg'=>'成功');
        try{
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
//            $upload_url = $this->get('kernel')->getProjectDir()."/web/upload"; // 这里得到的是app目录的绝对路
            $upload_url = $this->getParameter('brochures_directory'); // 这里得到的是app目录的绝对路
            $uploader =  new UpLoad($upload_url);

            //用于断点续传，验证指定分块是否已经存在，避免重复上传
            if(isset($post['status'])){
                switch ($post['status'])
                {
                    case 'chunkCheck':
                    {
                        $re = $this->chunkMergeAction($post,$files,$uploader);
                        $data = array_merge($data,$re);
                        break;
                    }
                    case 'md5Check':
                    {

                        $re = $this->md5CheckAction($post,$uploader);
                        $data = array_merge($data,$re);
                        break;
                    }
                    case 'chunksMerge':
                    {
                       /* if($path = $uploader->chunksMerge($_POST['name'], $_POST['chunks'], $_POST['ext'])){
                            //todo 把md5签名存入持久层，供未来的秒传验证
                            session('video_path', $save_path.'/'.$path);
                            die('{"status":1, "path": "'.$save_path.'/'.$path.'"}');
                        }
                        die('{"status":0}');*/
                        break;
                    }
                    default:
                        throw new  Exception("没有相关的状态值");
                }
            }else{
                $logger->error('--upLoadAction--post  --'.print_r($post,true));
                $logger->error('--upLoadAction--file  --'.print_r($files,true));
                if(($path = $uploader->upload($post,$files['file'])) !== false)
                {
                }else{
                    $re['status'] = 0;
                }

            }
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--upLoadAction  --'.$e->getMessage());
        }

        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }
    /**
     * 合并分块文件
     *
     *
     */
    function chunkMergeAction($post,$upload)
    {
        $re = array();
        $re['is_exist'] = $upload->findChunk($post);
        return $re;
    }

    function chunksMergeAction($post,$file,$upload)
    {
        $re = array();
       /* if($path = $uploader->chunksMerge($post, $file)){
            //todo 把md5签名存入持久层，供未来的秒传验证
            session('video_path', $save_path.'/'.$path);
            die('{"status":1, "path": "'.$save_path.'/'.$path.'"}');
        }
        die('{"status":0}');*/
        return $re;
    }

    /**
     *
     * 校验上传的文件是否存在，如果不存在则返回随机的文件名
     *
     * @param $post
     * @param UpLoad $upload
     * @return array
     */
    function md5CheckAction($post,UpLoad $upload)
    {
        $re = array();
        $repository =  $this->getDoctrine()->getRepository('AppBundle:VodMd5File');
        $md5_file = $repository->findByJsMd5($post['md5']);
        if($md5_file){
            $re['is_exist'] = 1;
            $re['upload_path'] = $md5_file->getFileName();//保存chunk的文件夹名称和合并后的文件夹名称
        }else{
            $re['is_exist'] = 0;
            $re['upload_path'] = $upload->createRandFileName();
            $file_map = new VodMd5File();
            $file_map->setJsMd5($post['md5']);
            $file_map->setFileName($re['file_name']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($file_map);
            $em->flush();
        }
        return $re;
    }
}
