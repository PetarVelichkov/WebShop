<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Product;
use AppBundle\Form\AdminEditProductType;
use AppBundle\Form\EditorEditProductType;
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
     * @Route("/product/add", name="add_product")
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

        return $this->render('webshop/Admin/addProduct.html.twig', ['form' => $form->createView()]);
    }

//TODO moje da imaproblem s route - /admin
    /**
     * @Route("/product/admin/edit/{id}", name="admin_edit_product")
     *
     *@Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function adminEditProduct($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product === null) {
            return $this->redirectToRoute('products');
        }

        $form = $this->createForm(AdminEditProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        return $this->render('webshop/Admin/adminEditProduct.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/product/editor/edit/{id}", name="editor_edit_product")
     *
     *@Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editorEditProduct($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product === null) {
            return $this->redirectToRoute('products');
        }

        $form = $this->createForm(EditorEditProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        return $this->render('webshop/Admin/editorEditProduct.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/product/delete/{id}", name="delete_product")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteProduct($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product === null) {
            return $this->redirectToRoute('products');
        }


        return $this->render('webshop/Admin/deleteProduct.html.twig', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/product/confirm/{id}", name="confirm_delete")
     *
     *@Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function confirmDelete($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product !== null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('products');
    }
}
