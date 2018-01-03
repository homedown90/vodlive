<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function showIndexAction()
    {
        return $this->render("@App/Admin/index.html.twig");
    }
}
