<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{

    /**
     * @Route("/products", name="products")
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProductsAction()
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);

        $query = $repository->createQueryBuilder('pr')
            ->where('pr.quantity > 0')
            ->getQuery();

        $products = $query->getResult();

        return $this->render('webshop/viewProducts.html.twig', ['products' => $products]);
    }

    /**
     * @param $id
     *
     * @Route("/product/{id}", name="product_view")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProduct($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        return $this->render('webshop/product.html.twig', ['product' => $product]);
    }

}
