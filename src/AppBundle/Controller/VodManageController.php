<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
class VodManageController extends Controller
{
    /**
     * 点播列表的展示界面
     *
     * @return Response
     */
    public function showAction()
    {
        return $this->render('@App/VodManage/show.ajax.twig', array(
            'table'=>array(
                'outer_div'=>'vod'
            )
        ));
    }

    /**
     * 获取点播信息
     *
     * @return Response
     */
    public function getDataAction()
    {
        /*{"total":800,"rows":[{"id":10,"name":"Item 10","price":"$10"},{"id":11,"name":"Item 11","price":"$11"},{"id":12,"name":"Item 12","price":"$12"},{"id":13,"name":"Item 13","price":"$13"},{"id":14,"name":"Item 14","price":"$14"},{"id":15,"name":"Item 15","price":"$15"},{"id":16,"name":"Item 16","price":"$16"},{"id":17,"name":"Item 17","price":"$17"},{"id":18,"name":"Item 18","price":"$18"},{"id":19,"name":"Item 19","price":"$19"}]}
         * */
//        $data = array('total'=>38,'rows'=>array(array('id'=>10,"name"=>"item 10","price"=>"10"),array('id'=>10,"name"=>"item 10","price"=>"10"),array('id'=>10,"name"=>"item 10","price"=>"10")));
        $data = array('total'=>0,'rows'=>array());
        return new Response(  json_encode($data), 200, array('Content-Type' => 'application/json') );
    }

    /**
     * 点播添加界面
     *
     */
    public function showAddAction()
    {
        return $this->render('@App/VodManage/add.ajax.twig');
    }
    /**
     * 添加点播信息
     *
     */
    public function addVodAction()
    {
        var_dump($_FILES);
        var_dump($_POST);
        var_dump($_GET);
        die;
    }
}
