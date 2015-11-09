<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\AccountModelAware;
use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MediaController extends AbstractUserAwareController
{
    use MediaModelAware;
    use AccountModelAware;

    public function viewAction($mediaId)
    {
        $media    = $this->findMediaOr404($mediaId);
        $album    = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);

        $this->denyAccessUnlessGranted('view', $album);

        $device   = $this->getDeviceModel()->findByPK(['id' => $media->id_device]);
        $owner    = $this->getAccountModel()->findByPK(['id' => $media->id_account]);
        $previous = $this->getMediaModel()->findPreviousInAlbum($media->id_album, $media->id);
        $next     = $this->getMediaModel()->findNextInAlbum($media->id_album, $media->id);
        $metadata = [];

        return $this->render('JaccobMediaBundle:Media:view.html.twig', [
            'canEdit'   => $this->isGranted('edit', $album),
            'metadata'  => $metadata,
            'device'    => $device,
            'owner'     => $owner,
            'album'     => $album,
            'media'     => $media,
            'previous'  => $previous,
            'next'      => $next,
            'size'      => $this->getParameter('jaccob_media.size.default'),
        ]);
    }

    public function viewFullscreenAction($mediaId)
    {
        $media    = $this->findMediaOr404($mediaId);
        $album    = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);

        $this->denyAccessUnlessGranted('view', $album);

        $device   = $this->getDeviceModel()->findByPK(['id' => $media->id_device]);
        $owner    = $this->getAccountModel()->findByPK(['id' => $media->id_account]);
        $previous = $this->getMediaModel()->findPreviousInAlbum($media->id_album, $media->id);
        $next     = $this->getMediaModel()->findNextInAlbum($media->id_album, $media->id);
        $metadata = [];

        return $this->render('JaccobMediaBundle:Media:viewFullscreen.html.twig', [
            'canEdit'   => $this->isGranted('edit', $album),
            'metadata'  => $metadata,
            'device'    => $device,
            'owner'     => $owner,
            'album'     => $album,
            'media'     => $media,
            'previous'  => $previous,
            'next'      => $next,
            'size'      => $this->getParameter('jaccob_media.size.fullscreen'),
        ]);
    }
}
