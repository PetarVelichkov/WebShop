<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
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
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $products = $user->getProducts();

        $total = array_sum(
            array_map(function (Product $p) {
                return $p->getPrice();
            }, $products->toArray())
        );

        return $this->render('webshop/cart.html.twig', [
            'cart' => $user->getProducts(),
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Method("GET")
     *
     * @param $id
     * @return RedirectResponse
     */
    public function addToCartAction($id)
    {
        $user = $this->getUser();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($user->getProducts()->contains($product)) {
            $this->addFlash("danger", "Product already exists in your cart.");
            return $this->redirectToRoute('products');
        }
        $em = $this->getDoctrine()->getManager();

        $user->getProducts()->add($product);
        $em->persist($user);
        $em->flush();

        $this->addFlash("success", "Product added to cart.");

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
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $user->getProducts()->removeElement($product);

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Product was removed!');

        return $this->redirectToRoute("cart");
    }


    /**
     * @Route("/cart/checkout", name="checkout_cart")
     *
     * @return RedirectResponse
     */
    public function cardCheckout()
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $userMoney = $user->getMoney();
        $cartProducts = $user->getProducts();

        $cartTotalSum = array_sum(
            array_map(function (Product $p) {
                return $p->getPrice();
            }, $cartProducts->toArray())
        );

        foreach ($cartProducts as $product) {
            if ($product->getQuantity() <= 0) {
                $this->addFlash('danger', 'Some products are out of stock!');

                $this->redirectToRoute('cart');
            }
        }

        if ($cartTotalSum > $userMoney) {
            $this->addFlash('danger', 'You do not have enough money!');

            $this->redirectToRoute('cart');
        }

        foreach ($cartProducts as $product) {
            /**
             * @var Product $product
             */
            $product->setQuantity($product->getQuantity() - 1);
            $user->getProducts()->removeElement($product);
            //TODO productPlain text???

            /**
             * @var User $owner
             */
            $owner = $product->getOwner();
            if ($owner){
                $owner->addMoney($product->getPrice());
                $em->persist($owner);
            }
        }

        $user->setMoney($userMoney - $cartTotalSum);

        //TODO orders

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Checkout completed!');

        return $this->redirectToRoute('homepage');

    }
}
