<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class VodShowController extends Controller
{
    public function showVodListAction()
    {
        return  $this->render('@App/VodShow/list.html.twig',array(
            'page'=>array("title"=>'首页')
        ));
    }
    public function showVodItemAction()
    {
        return $this->render('@App/VodShow/item.html.twig',array('page'=>array("title"=>'首页')));
    }
}
