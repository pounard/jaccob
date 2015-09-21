<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;

use PommProject\Foundation\Where;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AlbumController extends AbstractUserAwareController
{
    use MediaModelAware;

    public function listAction($albumId, Request $request)
    {
        // @todo List (paginated) all medias with thumbnails
        // @todo Request for sorting and filtering

        $owner      = null;
        $album      = null;
        $mediaList  = [];

        return $this->render('JaccobMediaBundle:Album:list.html.twig', [
            'owner'     => $owner,
            'album'     => $album,
            'mediaList' => $mediaList,
        ]);
    }
}
