<?php

namespace Jaccob\JaccobBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JaccobBundle:Default:index.html.twig', array('name' => $name));
    }
}
