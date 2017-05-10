<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ProductsOrder;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrdersAdminController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/orders", name="admin_list_orders")
     */
    public function listOrdersAction(Request $request)
    {
        $pager = $this->get('knp_paginator');
        $repository = $this->getDoctrine()->getRepository(ProductsOrder::class);

        /**
         * @var ProductsOrder[]|ArrayCollection $orders
         */
        $orders = $pager->paginate(
            $repository
                ->createQueryBuilder('ord')
                ->select('ord')
                ->orderBy('ord.date', 'desc')
                ->getQuery()
                ->getResult(),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render("webshop/Admin/orders_list.html.twig", [
            "orders" => $orders
        ]);
    }


    /**
     * @param $id
     * @return Response
     * @internal param ProductsOrder $order
     * @Route("/orders/complete/{id}", name="admin_complete_order")
     */
    public function completeOrder($id)
    {
        $order = $this->getDoctrine()->getRepository(ProductsOrder::class)->find($id);

        if ($order->getVerified() == true) {
            $this->addFlash("danger", "This order is already verified.");

            return $this->redirectToRoute("admin_list_orders");
        }

        $order->setVerified(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $this->addFlash("success", "Order was verified successfully.");

        return $this->redirectToRoute("admin_list_orders");
    }
}
