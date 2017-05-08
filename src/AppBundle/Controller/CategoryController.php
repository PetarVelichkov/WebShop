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

}
