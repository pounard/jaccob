<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;

use PommProject\Foundation\Where;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractUserAwareController
{
    use MediaModelAware;

    public function homeAction(Request $request)
    {
        // @todo List all seeable albums (paginated, sorted).
        // @todo Request for sorting and filtering

        $albumList  = [];
        $owner      = null;

        return $this->render('JaccobMediaBundle:Home:home.html.twig', [
            'albums'  => $albumList,
            'owner'   => $owner,
        ]);
    }
}
