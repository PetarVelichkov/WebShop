<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProductAdminController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/add_product", name="add_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addProduct(Request $request)
    {
        if(!$this->getUser()->isEditor() && !$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('products');
        }

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $product->setOwner($this->getUser());

            /** @var UploadedFile $file */
            $file = $product->getPicture();

            $fileName = $this->get('app.picture_uploader')->upload($file);

            $product->setPicture('uploadImage/' . $fileName);

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('products');
        }

        return $this->render('webshop/newProduct.html.twig', ['form' => $form->createView()]);
    }

}
