<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{

    public function indexAction()
    {
        /**
         * @Route("/", name="homepage")
         */
        return $this->render('@App/Home/index.html.twig',array(
            'page'=>array("title"=>'首页')
        ));
    }
}
