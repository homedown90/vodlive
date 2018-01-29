<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\ClassNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class VodShowController extends Controller
{
    public function showVodListAction(Request $request)
    {
        $get = $request->query->all();
        $classId = $get['classId'];
        $page = 9;
        $vod_class_rsy = $this->getDoctrine()->getRepository('AppBundle:VodClass');
        $vod_rsy =  $this->getDoctrine()->getRepository('AppBundle:VodList');
        if( $classId === 0 || $classId === '0')
        {
            $vod_info = $vod_rsy->searchVod(array(),0,9);
            $count_vod = $vod_info['total'];
            $arr_vod_list = $vod_info['rows'];

        }else{
            $obj_class = $vod_class_rsy->find($classId);

            if(empty($obj_class)){
                throw  $this->createNotFoundException("视频分类不存在");
            }
            if($obj_class->getIsLeaf())
            {
                $str_childs = $obj_class->getId();
            }else{
                $arr_class = $vod_class_rsy->getClassIdsByParentId($classId);
                $str_childs = implode(',',array_column($arr_class,'id'));
            }
            $arr_vod_list = $vod_rsy->getVodByClassIdAndLimit($str_childs,9);
            $count_vod = $vod_rsy->getVodCountByClassId($str_childs);
        }
        return  $this->render('@App/VodShow/list3.html.twig',array(
            'page'=>array("title"=>'点播列表'),
            'vodList'=>$arr_vod_list,
            'pageSize'=>$page,
            'count'=>$count_vod,
            'classId'=>$classId
        ));
    }
    public function showVodItemAction(Request $request)
    {
        $vod_id = $request->query->all('id');
        $vod_rsy = $this->getDoctrine()->getRepository("AppBundle:VodList");
        $obj_vod = $vod_rsy->find($vod_id);
        if (!$obj_vod) {
            throw $this->createNotFoundException(
                "查找的该视频不存在"
            );
        }
        $class_rsy = $this->getDoctrine()->getRepository('AppBundle:VodClass');
        $obj_class = $class_rsy->find($obj_vod->getClassId());
        $obj_parent_class = $class_rsy->find($obj_class->getParentId());


        return $this->render('@App/VodShow/item.html.twig',array(
            'page'=>array("title"=>$obj_vod->getTitle()),
            'vod'=>array('title'=>$obj_vod->getTitle(),'stream'=>$obj_vod->getStreams(),'playNum'=>$obj_vod->getPlayNum(),'time'=>"4"),
            'class'=>array('parentName'=>$obj_parent_class->getName(),'name'=>$obj_class->getName(),'parentId'=>$obj_parent_class->getId(),'id'=>$obj_class->getId())
        ));
    }
    public function showVodByClassAction(Request $request)
    {
        $get = $request->query->all();
        $id = $get['id'];
        $vod_rsy =  $this->getDoctrine()->getRepository('AppBundle:VodList');
        $node_vods = $vod_rsy->getVodByClassIdAndLimit($id,6);
        return $this->render('@App/VodShow/vod_class.ajax.twig',array(
            'page'=>array("title"=>'首页'),
            'vod_list'=>$node_vods
        ));
    }
    public function showVodCartPageAction(Request $request)
    {

        $post = $request->request->all();
        $search = $post['search'];
        $page = $post['page'];
        $pageSize = $post['pageSize'];
        $offset = ($page-1)*$pageSize;
        $limit = $pageSize;
        $vod_rsy =  $this->getDoctrine()->getRepository('AppBundle:VodList');
        $node_vods = $vod_rsy->searchVod($search,$offset,$limit);
        $node_vods['pageSize'] = $pageSize;
        $node_vods['page'] = $page;
        return $this->render('@App/VodShow/vod_class_list.ajax.twig',array(
            'vod_list'=>$node_vods
        ));
    }
}
