<?php

namespace AppBundle\Controller;

use AppBundle\Entity\VodClass;
use Extend\Symfony\FilterRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
            $node['is_leaf'] = $request->request->get('isParent');
            $node['path'] = '';
            if ($node['parent_id'] === 0) {
                $node['path'] = "0,".$node['parent_id'];
            }else{
                $parent = $this->getDoctrine()->getRepository('AppBundle:VodClass')->find($node['parent_id']);
                $node['path'] = $parent['path'].','.$parent['id'];
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
            $logger->error('--getDataAction--');
            $class_list = $repository->findAllOrderByIdAsc();
            $logger->error('--getDataAction--'.print_r($class_list,true));
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
		$data = array(array('nodes' => array(), "parentId" => -1, "nodeId" => 0, "isParent" => true, "text" => "全部"));
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
		$data = array(array('nodes' => array(), "parentId" => -1, "nodeId" => 0, "isParent" => true, "text" => "全部"));
		return new Response(
			json_encode($data),
			200,
			array('Content-Type' => 'application/json')
		);
	}
    /**
     * @param unknown $sourceArr   需要转换的数组
     * @param unknown $key         主键
     * @param unknown $parentKey   指向的父主键
     * @param unknown $childrenKey 生成的子类主键
     * @return Ambigous <multitype:, multitype:unknown > 多维嵌套 array
     */
    private  function array2tree($sourceArr) {
        $tempSrcArr = array(0=>array('nodes' => array(), "parentId" => -1, "nodeId" => 0, "isParent" => true, "text" => "全部"));
        foreach ($sourceArr as $v) {
            $tempSrcArr [$v ['id']] = array('nodes' => array(), "parentId" => $v['parent_id'], "nodeId" => $v['id'], "isParent" => $v['is_leaf'], "text" => $v['name']);
        }
        $i     = 0;
        $count = count($sourceArr);
        for ($i = ($count - 1); $i >= 0; $i--) {
            if (isset ($tempSrcArr [$sourceArr [$i] ['parentId']])) {
                $tArr                                        = array_pop($tempSrcArr);
                $tempSrcArr[$tArr['parentId']]['nodes'] = (isset($tempSrcArr[$tArr['parentId']]['nodes']) && is_array($tempSrcArr[$tArr['parentId']]['nodes'])) ? $tempSrcArr[$tArr['parentId']]['nodes'] : array();
                array_unshift($tempSrcArr [$tArr ['parentId']] ['nodes'], $tArr);
            }
        }
        return $tempSrcArr;
    }
}
