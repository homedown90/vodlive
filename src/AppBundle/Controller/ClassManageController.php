<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ClassManageController extends Controller
{
    public function showAction()
    {
        return $this->render('@App/ClassManage/show.ajax.twig', array(
            // ...
        ));
    }
    public function addAction()
    {
        $data = array('status'=>1);
        return new Response(
            json_encode($data),
            200,
            array('Content-Type' => 'application/json')
        );
    }

}
