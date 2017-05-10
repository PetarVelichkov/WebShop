<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class OrdersController extends Controller
{
    public function viewOrdersAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $date = new \DateTime();



        return $this->render('', array('name' => $name));
    }
}
