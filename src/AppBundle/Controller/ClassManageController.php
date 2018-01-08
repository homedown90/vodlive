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
        return Response();
    }

}
