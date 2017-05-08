<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
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
//        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

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

            $this->addFlash('success', "Category was added successfully!");


            return $this->redirectToRoute('products');
        }

        return $this->render('webshop/addCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }


//    /**
//     * @Route("/edit_category", name="edit_category")
//     *
//     * @param Request $request
//     * @param Category $category
//     */
//    public function editCategory(Request $request, Category $category)
//    {
//
//    }
}
