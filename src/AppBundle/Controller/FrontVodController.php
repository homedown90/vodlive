<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontVodController extends Controller
{
    public function showVodListAction()
    {
        return  $this->render('@App/FrontVod/vodlist.html.twig',array(
            'page'=>array("title"=>'首页')
        ));
    }
    public function showVodItemAction()
    {
        return $this->render('@App/FrontVod/voditem.html.twig',array('page'=>array("title"=>'首页')));
    }
}
