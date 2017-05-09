<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewCartAction()
    {
        $carts = $this->getDoctrine()->getRepository(Cart::class)->findBy([
            'owner' => $this->getUser()
        ]);


        return $this->render('webshop/cart.html.twig', ['carts' => $carts]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCartAction($id)
    {
        $cart = new Cart();
        $em = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $cart->setProduct($product);
        $cart->setOwner($this->getUser());

        $em->persist($cart);
        $em->flush();

        return $this->redirectToRoute('cart');
    }
}
