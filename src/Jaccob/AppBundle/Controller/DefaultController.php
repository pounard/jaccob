<?php

namespace Jaccob\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * Index action.
     */
    public function indexAction()
    {
        return $this->render('JaccobAppBundle:Default:index.html.twig');
    }

    /**
     * 404 Not Found error action.
     */
    public function notFoundError()
    {
        return $this->render('JaccobAppBundle:Default:notFoundError.html.twig');
    }

    /**
     * 403 Not Authorized error action.
     */
    public function notAuthorizedError()
    {
        return $this->render('JaccobAppBundle:Default:notAuthorizedError.html.twig');
    }
}
