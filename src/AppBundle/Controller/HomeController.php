<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{

    public function indexAction()
    {
        try{

            $vod_rsy =  $this->getDoctrine()->getRepository('AppBundle:VodList');
            $arr_vod_class = $vod_rsy->getVodClassJoinClassByPublish();
            $class_tree = array();
            foreach ($arr_vod_class as $vod_class)
            {
                if(!isset($class_tree["{$vod_class['parentId']}"]))
                {
                    $class_tree["{$vod_class['parentId']}"] = array('children'=>array());
                }
                array_push($class_tree["{$vod_class['parentId']}"]['children'],$vod_class);
            }
            $parent_class_ids = array_keys($class_tree);
            $vod_class_rsy = $this->getDoctrine()->getRepository('AppBundle:VodClass');
            $arr_parent_class = $vod_class_rsy->getClassByIds($parent_class_ids);
            foreach ($arr_parent_class as $parent)
            {
                $class_tree["{$parent['id']}"]['name'] = $parent['name'];
                $class_tree["{$parent['id']}"]['id'] = $parent['id'];
            }
            foreach ($class_tree as $key => $node)
            {
                $node_vods = $vod_rsy->getVodByClassIdAndLimit($class_tree["{$key}"]['children'][0]['classId'],6);
                $class_tree["{$key}"]['children'][0]['list'] = $node_vods;
            }

        }catch (\Exception $e){
            $logger = $this->get('logger');
            $logger->error('--indexAction  --'.$e->getMessage());
        }
        return $this->render('@App/Home/index.html.twig',array(
            'page'=>array("title"=>'首页'),
            'class'=>$class_tree
        ));
    }
}
