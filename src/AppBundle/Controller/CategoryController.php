<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function listAction()
    {
//        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
//
//        return $this->render('webshop/category.html.twig', ['categories' => $categories]);
    }
}
