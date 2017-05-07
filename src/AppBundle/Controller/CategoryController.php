<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{id}", name="category_id")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewCategoryAction($id)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->find($id);

        return $this->render('webshop/viewCategory.html.twig', ['products' => $products]);
    }

    //TODO add category
}
