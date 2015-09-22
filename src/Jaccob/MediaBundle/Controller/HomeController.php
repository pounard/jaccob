<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractUserAwareController
{
    use MediaModelAware;

    public function homeAction(Request $request)
    {
        $currentAccount = $this->getCurrentUserAccount();

        // @todo List all seeable albums (paginated, sorted).
        // @todo Request for sorting and filtering

        $albumList = $this
            ->getAlbumModel()
            ->findVisibleFor($currentAccount->getId())
        ;

        return $this->render('JaccobMediaBundle:Home:home.html.twig', [
            'albums'  => $albumList,
        ]);
    }
}
