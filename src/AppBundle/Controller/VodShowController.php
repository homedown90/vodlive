<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VodShowController extends Controller
{
    public function showVodListAction(Request $request)
    {
        $get = $request->query->all();
        $parentId = $get['parentId'];
        $page = 9;
        $vod_class_rsy = $this->getDoctrine()->getRepository('AppBundle:VodClass');
        $arr_childs = $vod_class_rsy->getClassIdsByParentId($parentId);
        $str_childs = implode(',',array_column($arr_childs,'id'));
        $vod_rsy =  $this->getDoctrine()->getRepository('AppBundle:VodList');
        $arr_vod_list = $vod_rsy->getVodByClassIdAndLimit($str_childs,9);
        $count_vod = $vod_rsy->getVodCountByClassId($str_childs);


        return  $this->render('@App/VodShow/list3.html.twig',array(
            'page'=>array("title"=>'首页'),
            'vodList'=>$arr_vod_list,
            'pageSize'=>$page,
            'count'=>$count_vod,
            'classId'=>$parentId
        ));
    }
    public function showVodItemAction()
    {
        return $this->render('@App/VodShow/item.html.twig',array('page'=>array("title"=>'首页')));
    }
    public function showVodByClassAction(Request $request)
    {
        $get = $request->query->all();
        $id = $get['id'];
        $vod_rsy =  $this->getDoctrine()->getRepository('AppBundle:VodList');
        $node_vods = $vod_rsy->getVodByClassIdAndLimit($id,6);
        return $this->render('@App/VodShow/vod_index_list.ajax.twig',array(
            'page'=>array("title"=>'首页'),
            'vod_list'=>$node_vods
        ));
    }
    public function showVodCartPage(Request $request)
    {

        $post = $request->request->all();
        $search = $post['search'];
        $page = $post['page'];
        $limit = $post['pageSize'];
        $offset = ($page-1)*$limit;
        $vod_rsy =  $this->getDoctrine()->getRepository('AppBundle:VodList');
        $node_vods = $vod_rsy->searchVod($search,$offset,$limit);
        $node_vods['pageSize'] = $limit;
        $node_vods['page'] = $page;
        return $this->render('@App/VodShow/vod_class_list.ajax.twig',array(
            'vod_list'=>$node_vods
        ));
    }
}
