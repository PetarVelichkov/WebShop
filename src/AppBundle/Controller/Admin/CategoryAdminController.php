<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryAdminController extends Controller
{

    /**
     * @Route("/categories", name="view_categories")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listCategories()
    {
        return $this->render('webshop/Admin/listCategories.html.twig');
    }


    /**
     * @Route("add_category", name="add_category")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addCategory(Request $request)
    {
        if(!$this->getUser()->isEditor() && !$this->getUser()->isAdmin()) {
            return $this->redirectToRoute('products');
        }

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $exist = $this->getDoctrine()->getRepository(Category::class)->findBy([
                'name' => $form->get('name')->getData()
            ]);

            if ($exist) {
                return $this->redirectToRoute('products');
            }

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category was added successfully!');

            return $this->redirectToRoute('view_categories');
        }

        return $this->render('webshop/Admin/addCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/categories/edit/{id}", name="edit_category")
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editCategory(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if ($category === null) {
            return $this->redirectToRoute('view_categories');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $em = $this->getDoctrine()->getManager();
           $em->persist($category);
           $em->flush();

           return $this->redirectToRoute('view_categories', ['id' => $category->getId()]);
        }

        return $this->render('webshop/Admin/editCategory.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/categories/delete/{id}", name="delete_category")
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteProduct($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if ($category === null) {
            return $this->redirectToRoute('products');
        }


        return $this->render('webshop/Admin/deleteCategory.html.twig', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/categories/confirm/{id}", name="confirm_delete_category")
     *
     *@Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function confirmDelete($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        if ($category !== null) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($category);
                $em->flush();

                $this->addFlash('success', 'Category deleted!');

            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error', 'Cannot delete this category!');
            }
        }

        return $this->redirectToRoute('view_categories');
    }
}
