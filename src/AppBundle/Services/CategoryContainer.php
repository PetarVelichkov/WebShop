<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class CategoryContainer
{
    private $em;

    private $categories;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

// For more info - http://symfony.com/doc/current/doctrine.html

    public function getCategories()
    {
        $repository = $this->em->getRepository('AppBundle:Category');

        $query = $repository->createQueryBuilder('cat')
            ->select('cat')
            ->orderBy('cat.id', 'ASC')
            ->getQuery();

        $categories = $query->getResult();

        return $this->categories = $categories;
    }
}