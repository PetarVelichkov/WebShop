<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Form\CategoryType;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function viewCategoryAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);

        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        $pager = $this->get('knp_paginator');
        /** @var ArrayCollection|Product[] $products */
        $products = $pager->paginate(
                $repository
                ->createQueryBuilder('pr')
                ->select('pr')
                ->where('pr.category = ?1')
                ->andWhere('pr.quantity > 0')
                ->setParameter(1, $category)
                ->orderBy('pr.id', 'desc')
                ->getQuery()
                ->getResult(),
            $request->query->getInt('page', 1),
            6
        );


        return $this->render('webshop/viewCategory.html.twig', ['products' => $products]);
    }

}
