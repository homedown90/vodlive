<?php

namespace AppBundle\Controller;

use AppBundle\Entity\VodClass;
use Extend\Symfony\FilterRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ClassManageController extends Controller {
	/**
	 *界面渲染
	 *
	 * @return Response
	 */
	public function showAction() {
		return $this->render('@App/ClassManage/show.ajax.twig', array());
	}

	/**
	 * 添加节点信息
	 *
	 * @return {status:0-成功 1-失败,msg:提示消息,nodeId:节点id,text:节点文本}
	 */
	public function addAction() {

	    $data = array('status' => 0, 'msg'=>'成功');
	    try{
            $request = FilterRequest::createFromGlobals();
            $node = array();
            $node['name'] = $request->request->get('text');
            $node['parent_id'] = $request->request->get('parentId');
            $node['is_leaf'] = !$request->request->get('isParent');
            $node['path'] = '';
            if ($node['parent_id'] == 0) {
                $node['path'] = "0";
            }else{
                $parent = $this->getDoctrine()->getRepository('AppBundle:VodClass')->find($node['parent_id']);
                $node['path'] = $parent->getParentId().','.$parent->getId();
            }
            $vodClass = new VodClass();
            $vodClass->setName($node['name']);
            $vodClass->setParentId($node['parent_id']);
            $vodClass->setIsLeaf($node['is_leaf']);
            $vodClass->setPath($node['path']);

            $em = $this->getDoctrine()->getManager();

            // tells Doctrine you want to (eventually) save the Product (no queries yet)
            // 告诉Doctrine你希望（最终）存储Product对象（还没有语句执行）
            $em->persist($vodClass);

            // actually executes the queries (i.e. the INSERT query)
            // 真正执行语句（如，INSERT 查询）
            $em->flush();

            $data = array_merge($data,array('nodeId' => $vodClass->getId(), 'text' => $vodClass->getName()));
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--getDataAction--'.$e->getMessage());
        }

		return new Response(
			json_encode($data),
			200,
			array('Content-Type' => 'application/json')
		);
	}

	/**
	 * 获取分类json数据:[{nodes: [],parentId:-1,nodeId:0,isParent:true,text:"全部" }];
	 */
	public function getDataAction() {
        $logger = $this->get('logger');
        $data = array('status' => 0, 'msg'=>'成功');
        try{
            $repository = $this->getDoctrine()->getRepository('AppBundle:VodClass');
            $class_list = $repository->findAllOrderByIdAsc();
            $logger->error('--getDataAction-234234234-'.print_r($class_list[0]->getId(),true));
            $tree = $this->array2tree($class_list);
            $data = array_merge($data,array('data'=>$tree));
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;

            $logger->error('--getDataAction--'.$e->getMessage());
        }
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
	public function deleteAction() {
        $data = array('status' => 0, 'msg'=>'成功');
        try{
            $request = FilterRequest::createFromGlobals();
            $node = array();
            $node['id'] = $request->request->get('nodeId');

            /*
             * 需要添加校验信息:查看当前节点是否有正在使用的视频
             * */
            $em = $this->getDoctrine()->getManager();
            $vodClass = $em->getRepository('AppBundle:VodClass')->find( $node['id']);

            $em->remove($vodClass);
            $em->flush();
            $data = array_merge($data,array('nodeId' => $vodClass->getId(), 'text' => $vodClass->getName()));
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--deleteAction  --'.$e->getMessage());
        }

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
	public function editAction() {
        $data = array('status' => 0, 'msg'=>'成功');
        try{
            $request = FilterRequest::createFromGlobals();
            $node = array();
            $node['name'] = $request->request->get('text');
            $node['id'] = $request->request->get('nodeId');

            $vodClass = $this->getDoctrine()->getRepository('AppBundle:VodClass')->find( $node['id']);
            $vodClass->setName($node['name']);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $data = array_merge($data,array('nodeId' => $vodClass->getId(), 'text' => $vodClass->getName()));
        }catch (\Exception $e){
            $data['msg'] = "操作失败,请联系管理员";
            $data['status'] = 1;
            $logger = $this->get('logger');
            $logger->error('--editAction--'.$e->getMessage());
        }

        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
	}
    /**
     * @param unknown $sourceArr   需要转换的数组
     */
    private  function array2tree($sourceArr) {
        $tempSrcArr = array(0=>array('nodes' => array(), "parentId" => -1, "nodeId" => 0, "isParent" => true, "text" => "全部"));
        foreach ($sourceArr as $v) {
            $id = $v->getId();
            $tempSrcArr[$id] = array('nodes' => array(), "parentId" => $v->getParentId(), "nodeId" => $id, "isParent" => !$v->getIsLeaf(), "text" => $v->getName());
        }
        $i     = 0;
        $count = count($sourceArr);
        for ($i = ($count - 1); $i >= 0; $i--) {
            if (isset ($tempSrcArr [$sourceArr[$i]->getParentId()])) {
                $tArr                                        = array_pop($tempSrcArr);
                $tempSrcArr[$tArr['parentId']]['nodes'] = (isset($tempSrcArr[$tArr['parentId']]['nodes']) && is_array($tempSrcArr[$tArr['parentId']]['nodes'])) ? $tempSrcArr[$tArr['parentId']]['nodes'] : array();
                array_unshift($tempSrcArr [$tArr ['parentId']] ['nodes'], $tArr);
            }
        }
        return $tempSrcArr;
    }
}
