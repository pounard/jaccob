<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Util\FileSystem;

use PommProject\Foundation\Where;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * This controller only builds media thumbnails then write them onto disk with
 * the exact same URL. Once written the web server will be able to deliver it
 * directly without waking up PHP again. This may cause security issues since
 * all generated images will be publicly available, that's why a first level
 * of security by offuscation (believe me, I know that this is very wrong) is
 * done by computing media's hash and use it for file system paths.
 */
class ThumbnailController extends Controller
{
    use MediaModelAware;

    /**
     * Create thumbnail action
     */
    public function createAction($path, Request $request)
    {
        $path = explode('/', $path);

        if (empty($path)) {
            throw $this->createNotFoundException();
        }

        // Prefix.
        $sizeId = array_shift($path);

        // Check size is valid
        if ('h' === $sizeId[0]) {
            $modifier = 'h';
            $size = substr($sizeId, 1);
        } else if ('w' === $sizeId[0]) {
            $modifier = 'w';
            $size = substr($sizeId, 1);
        } else if ('m' === $sizeId[0]) {
            $modifier = 'm';
            $size = substr($sizeId, 1);
        } else if ('s' === $sizeId[0]) {
            $modifier = 's'; // Square
            $size = substr($sizeId, 1);
        } else {
            $modifier = 's';
            $size = (int)$sizeId;
        }

        // Ensure size is valid
        $allowedSizes = $this->getParameter('jaccob_media.size.list');

        if (!in_array($size, $allowedSizes)) {
            throw $this->createNotFoundException();
        }

        // Rebuild the media real path from URL
        $name = array_pop($path);
        $name = urldecode($name);
        $path = FileSystem::pathJoin($path);
        $path = urldecode($path);

        // Load media, we need to remove the file extension, because it depends
        // on how it has been converted, and find the media by the lowest common
        // denominator, i.e. the physical_path
        if ($pos = strrpos($name, '.')) {
            $name = substr($name, 0, $pos);
        }
        $mediaList = $this
            ->getMediaModel()
            ->findWhere(
                (new Where())
                    ->andWhere('physical_path = $*', [$path])
                    ->andWhere('name LIKE $*', [$name . '%'])
            )
        ;

        $media = null;
        foreach ($mediaList as $item) { // FIXME: reset() not working on Iterator?
            $media = $item;
            break;
        }

        if (!$media) {
            throw $this->createNotFoundException();
        }

        $album = $this->getAlbumModel()->findByPK(['id' => $media->id_album]);
        $this->denyAccessUnlessGranted('view', $album);

        /* @var $mediaHelper \Jaccob\MediaBundle\Util\MediaHelper */
        $mediaHelper = $this->get('jaccob_media.media_helper');

        $thumbnailPath = $mediaHelper->createThumbnail($media, $size, $modifier);
        if (!$thumbnailPath) {
            // @todo Handle default image...
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($thumbnailPath);
    }
}
