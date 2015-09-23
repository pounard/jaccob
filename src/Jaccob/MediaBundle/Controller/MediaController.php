<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MediaController extends AbstractUserAwareController
{
    use MediaModelAware;
    use AccountModelAware;

    public function viewAction($mediaId)
    {
        // @todo View media in its own page
        // @todo Check rights with albumId

        $media    = $this->findMediaOr404($mediaId);
        $device   = $this->getDeviceModel()->findByPK(['id' => $media->id_device]);
        $owner    = $this->getAccountModel()->findByPK(['id' => $media->id_account]);
        $album    = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);
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
            'size'      => 'w' . $this->getParameter('jaccob_media.size.default'),
        ]);
    }

    public function viewFullscreenAction($mediaId)
    {
        // @todo View media in its own page
        // @todo Check rights with albumId

        $media    = $this->findMediaOr404($mediaId);
        $device   = $this->getDeviceModel()->findByPK(['id' => $media->id_device]);
        $owner    = $this->getAccountModel()->findByPK(['id' => $media->id_account]);
        $album    = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);
        $previous = null;
        $next     = null;
        $metadata = [];

        return $this->render('JaccobMediaBundle:Media:viewFullscreen.html.twig', [
            'metadata'  => $metadata,
            'device'    => $device,
            'owner'     => $owner,
            'album'     => $album,
            'media'     => $media,
            'previous'  => $previous,
            'next'      => $next,
            'size'      => 'w' . $this->getParameter('jaccob_media.size.fullscreen'),
        ]);
    }
}
