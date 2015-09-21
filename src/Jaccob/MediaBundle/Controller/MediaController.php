<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;

use PommProject\Foundation\Where;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MediaController extends AbstractUserAwareController
{
    use MediaModelAware;

    public function downloadAction($mediaId)
    {
        // @todo Get the full image URL (with hash) and redirect
    }

    public function viewAction($albumId, $mediaId, Request $request)
    {
        // @todo View media in its own page
        // @todo Check rights with albumId
        // @todo Request for sorting and filtering

        $device   = null;
        $owner    = null;
        $album    = null;
        $media    = null;
        $previous = null;
        $next     = null;
        $metadata = [];

        return $this->render('JaccobMediaBundle:Media:view.html.twig', [
            'metadata'  => $metadata,
            'device'    => $device,
            'owner'     => $owner,
            'album'     => $album,
            'media'     => $media,
            'previous'  => $previous,
            'next'      => $next,
        ]);
    }
}
