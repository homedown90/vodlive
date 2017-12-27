<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelloController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();
        $name = $user->getUsername();

        return $this->render('@App/Hello/index.html.twig', array('name'=>$name));
       // return $this->redirectToRoute('homepage');
        //return new Response('<html><body>Hello '.$name.'</body></html>');
    }

}
