<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function showIndexAction()
    {
//        var_dump($_FILES);
//        die;
        return $this->render("@App/Admin/index.html.twig");
    }
}
