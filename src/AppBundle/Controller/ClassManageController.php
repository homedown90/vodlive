<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClassManageController extends Controller
{
    public function showAction()
    {
        return $this->render('@App/ClassManage/show.ajax.twig', array(
            // ...
        ));
    }

}
