<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{id}", name="category_id")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewCategoryAction($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        /** @var Product[] $products */
        $products = $category->getProducts()->toArray();

        return $this->render('webshop/viewCategory.html.twig', ['products' => $products]);
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

            return $this->redirectToRoute('products');
        }

        return $this->render('webshop/addCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    //TODO edit category
}
