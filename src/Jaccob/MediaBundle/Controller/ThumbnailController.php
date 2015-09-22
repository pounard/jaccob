<?php

namespace Jaccob\MediaBundle\Controller;

use Jaccob\AccountBundle\Controller\AbstractUserAwareController;

use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Toolkit\ExternalImagickImageToolkit;
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
class ThumbnailController extends AbstractUserAwareController
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

        $sizeId = array_shift($path);

        // Check size is valid
        if ('h' === $sizeId[0]) {
            $mode = 'h';
            $size = substr($sizeId, 1);
        } else if ('w' === $sizeId[0]) {
            $mode = 'w';
            $size = substr($sizeId, 1);
        } else if ('m' === $sizeId[0]) {
            $mode = 'm';
            $size = substr($sizeId, 1);
        } else {
            $mode = 's'; // Square
            $size = substr($sizeId, 1);
        }

        // Ensure size is valid
        // @todo Configuration would be better here
        if (!in_array($size, array("100", "200", "230", "300", "600", "900", "1200", "full"))) {
            throw $this->createNotFoundException();
        }

        // Rebuild the media real path from URL
        $hash = FileSystem::pathJoin($path);

        // Load media
        $mediaList = $this
            ->getMediaModel()
            ->findWhere(
                (new Where())
                    ->andWhere('physical_path = $*', [$hash])
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

        // Ensure the destination directory
        $publicDirectory = $this->getParameter('jaccob_media.directory.public');

        $inFile = FileSystem::pathJoin($publicDirectory, 'full', $hash);
        $outFile = FileSystem::pathJoin($publicDirectory, $sizeId, $hash);

        $toolkit = new ExternalImagickImageToolkit();

        switch ($mode) {

            case 'm':
                $toolkit->scaleTo($inFile, $outFile, $size, $size, true);
                break;

            case 'h':
                $toolkit->scaleTo($inFile, $outFile, null, $size, true);
                break;

            case 'w':
                $toolkit->scaleTo($inFile, $outFile, $size, null, true);
                break;

            case 's':
                $toolkit->scaleAndCrop($inFile, $outFile, $size, $size);
                break;
        }

        return new BinaryFileResponse($outFile);
    }
}
