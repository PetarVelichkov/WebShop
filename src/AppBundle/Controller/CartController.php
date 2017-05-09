<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

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


    /**
     * @Route("/delete/{id}", name="cart_delete_product")
     *
     * @param $id
     * @return RedirectResponse
     */
    public function deleteProductFromCartAction($id)
    {
        $cart = $this->getDoctrine()->getRepository(Cart::class)->find($id);

        if ($cart !== null) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($cart);
                $em->flush();

                $this->addFlash('success', 'Product removed!');

            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error', 'Cannot delete this category!');
            }
        }



        return $this->redirectToRoute("cart");
    }
}
