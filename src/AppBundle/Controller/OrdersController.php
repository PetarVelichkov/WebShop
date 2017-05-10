<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductsOrder;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class OrdersController extends Controller
{

    /**
     * @Route("/profile/orders", name="user_orders")
     * @Security(expression="is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewOrdersAction(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $pager = $this->get('knp_paginator');
        $repository = $this->getDoctrine()->getRepository(ProductsOrder::class);

        /**
         * @var ProductsOrder[]|ArrayCollection $orders
         */
        $orders = $pager->paginate(
            $repository
            ->createQueryBuilder('ord')
            ->where('ord.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ord.date', 'desc')
            ->getQuery()
            ->getResult(),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('webshop/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/profile/orders/{id}", name="view_order")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewOrderAction(Request $request,$id)
    {
        /**
         * @var ProductsOrder $order
         */
        $order = $this->getDoctrine()->getRepository(ProductsOrder::class)->find($id);
        $productsIds = $order->getProducts();

        $repository = $this->getDoctrine()->getRepository(Product::class);


        /** @var ArrayCollection|Product[] $products */
        $products = [];
        foreach ($productsIds as $productId) {

        $product = $repository->createQueryBuilder('pr')
            ->where('pr.id = ?1')
            ->setParameter(1, $productId)
            ->getQuery()
                ->getOneOrNullResult();

        $products[] = $product;
        }

        $total = array_sum(
            array_map(function (Product $p) {
                return $p->getPrice();
            }, $products)
        );


        return $this->render('webshop/viewOrder.html.twig',[
            'order' => $order,
            'products' => $products,
            'total' => $total
        ]);
    }
}
