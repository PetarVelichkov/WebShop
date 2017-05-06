<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function listCategoryAction()
    {


        return $this->render('', array('name' => $name));
    }
}
