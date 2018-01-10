<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ClassManageController extends Controller
{
    /**
     *界面渲染
     *
     * @return Response
     */
    public function showAction()
    {
        return $this->render('@App/ClassManage/show.ajax.twig', array(
            // ...
        ));
    }

    /**
     * 添加节点信息
     *
     * @return {status:0-成功 1-失败,msg:提示消息,nodeId:节点id,text:节点文本}
     */
    public function addAction()
    {
        $data = array('status'=>1);
        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * 获取分类json数据:[{nodes: [],parentId:-1,nodeId:0,isParent:true,text:"全部" }];
     */
    public function getDataAction()
    {
        $data = array(array('nodes'=>array(),"parentId"=>-1,"nodeId"=>0,"isParent"=>true,"text"=>"全部"));
        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }
    /**
     * 删除分类节点
     *
     * @return {status:0-成功 1-失败,msg:提示消息,nodeId:节点id}
     */
    public function deleteAction()
    {
        $data = array(array('nodes'=>array(),"parentId"=>-1,"nodeId"=>0,"isParent"=>true,"text"=>"全部"));
        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }
    /**
     * 编辑分类节点
     *
     * @return {status:0-成功 1-失败,msg:提示消息,nodeId:节点id,text:节点文本}
     */
    public function editAction()
    {
        $data = array(array('nodes'=>array(),"parentId"=>-1,"nodeId"=>0,"isParent"=>true,"text"=>"全部"));
        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }

}
