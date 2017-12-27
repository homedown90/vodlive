<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\MiUser;

class DefaultController extends Controller
{

    public function adminAction()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = new MiUser();
        $user->setAccount('admin');
        $user->setEmail('homedown_90@163.com');
        $user->setType(1);
        $user->setSalt(md5('520'));
        $user->setPassword(md5('admin888'.md5('520')));
        $user->setCreateTime(new \DateTime());
        $user->setModifiedTime(new \DateTime());
        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        // 告诉Doctrine你希望（最终）存储Product对象（还没有语句执行）

        $em->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        // 真正执行语句（如，INSERT 查询）
        $em->flush();
        return new Response('<html><body>Admin page!</body></html>');
        //return $this->render("@App/Hello/index.html.twig",array('name'=>'sfsd'));
        //return new Response('Saved new product with id '.$user->getId());
    }
}
