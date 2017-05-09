<?php


namespace AppBundle\Services;


use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class CartService
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * @param User $user
     * @param Product $product
     *
     */
    public function removeProductFromCart(User $user, Product $product)
    {
        if (!$user->getProducts()->removeElement($product)) {
            return;
        }

        $this->em->persist($user);
        $this->em->flush();
    }
}