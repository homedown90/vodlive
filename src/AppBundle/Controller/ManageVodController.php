<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ManageVodController extends Controller
{
    public function showListAction()
    {
        return $this->render('AppBundle:ManageVod:show_list.html.twig', array(
            // ...
        ));
    }

}
