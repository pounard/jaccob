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

    public function viewAction($mediaId, $size = null)
    {
        // @todo View media in its own page
        // @todo Check rights with albumId
        // @todo Request for sorting and filtering

        if ($size) {
            $allowedSizes = $this->getParameter('jaccob_media.sizes');
            if (!in_array($size, $allowedSizes)) {
                throw $this->createNotFoundException();
            }
        } else {
            $size = $this->getParameter('jaccob_media.default_size');
        }

        $media    = $this->findMediaOr404($mediaId);
        $device   = $this->getDeviceModel()->findByPK(['id' => $media->id_device]);
        $owner    = $this->getAccountModel()->findByPK(['id' => $media->id_account]);
        $album    = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);
        $previous = null;
        $next     = null;
        $metadata = [];

        if (!$size) {
            
        }

        return $this->render('JaccobMediaBundle:Media:view.html.twig', [
            'metadata'  => $metadata,
            'device'    => $device,
            'owner'     => $owner,
            'album'     => $album,
            'media'     => $media,
            'previous'  => $previous,
            'next'      => $next,
            'size'      => $size,
        ]);
    }
}
