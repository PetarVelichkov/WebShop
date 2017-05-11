<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\SellProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserProductsController extends Controller
{
    /**
     * @Route("/sell/{id}", name="sell_product")
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sellAction(Request $request, $id)
    {

        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $lastName = $product->getName();
        $lastDescription = $product->getDescription();
        $lastPrice = $product->getPrice();

        $form = $this->createForm(SellProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newProduct = new Product();
            $newProduct->setName($product->getName());
            $newProduct->setCategory($product->getCategory());
            $newProduct->setDescription($product->getDescription());
            $newProduct->setPicture($product->getPicture());
            $newProduct->setQuantity(1);
            $newProduct->setPrice($product->getPrice());
            $newProduct->setOwner($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($newProduct);

            $em->flush();

            $product->setName($lastName);
            $product->setDescription($lastDescription);
            $product->setPrice($lastPrice);
            $em->persist($product);
            $em->flush();

            $this->addFlash("success", "Product added for sale successfully!");

            return $this->redirectToRoute('user_orders');
        }

        return $this->render('webshop/sell_product.html.twig', [
            'add_form' => $form->createView()
        ]);
    }
}
