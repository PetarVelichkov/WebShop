<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="fullName", type="string", length=255)
     */
    private $fullName;

    /**
     *
     * @ORM\Column(name="money", type="decimal", precision=10, scale=0)
     */
    private $money = 45000;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="users_role", joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}, inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")})
     */
    private $roles;


    /**
     * @var Product[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product", inversedBy="users")
     * @ORM\JoinTable(name="cart")
     */
    private $products;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductsOrder", mappedBy="user")
     * @ORM\OrderBy({"date":"desc"})
     *
     * @var ProductsOrder[]|ArrayCollection $orders
     */
    private $orders;


    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function isEditor()
    {
        return in_array('ROLE_EDITOR', $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set money
     *
     * @param string $money
     *
     * @return User
     */
    public function setMoney($money)
    {
        $this->money = $money;

        return $this;
    }


    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function addMoney($quantity)
    {
        $this->money += $quantity;

        return $this;
    }


    public function getRoles()
    {
        return $this->roles->toArray();
    }


    public function addRole(Role $role)
    {
        $this->roles[] = $role;

    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        return null;
    }

    function __toString()
    {
        return $this->fullName;
    }


    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product $product
     * @return User
     */
    public function addProducts(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function setOrders($orders)
    {
        $this->orders = $orders;

        return $this;
    }

}
