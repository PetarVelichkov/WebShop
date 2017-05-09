<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->createQueryBuilder('pr')
            ->where('pr.quantity > 0')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();



        return $this->render('webshop/index.html.twig',[
            "products" => $products
        ]);
    }
}
