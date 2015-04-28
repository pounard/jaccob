<?php

namespace Jaccob\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('JaccobAccountBundle:Default:index.html.twig');
    }
}
