<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 09.05.2017 г.
 * Time: 18:21
 */

namespace AppBundle\Services;


use Doctrine\ORM\EntityManager;

class CartContainer
{
    private $em;

    private $carts;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

// For more info - http://symfony.com/doc/current/doctrine.html

    public function getCarts()
    {
        $repository = $this->em->getRepository('AppBundle:Cart');

        $query = $repository->createQueryBuilder('cart')
            ->select('cart')
            ->getQuery();

        $carts = $query->getResult();

        return $this->carts = $carts;
    }
}