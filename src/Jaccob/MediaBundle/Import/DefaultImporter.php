<?php

namespace Jaccob\MediaBundle\Import;

use Jaccob\AccountBundle\Model\Account;
use Jaccob\AccountBundle\Security\Crypt;

use Jaccob\MediaBundle\Event\MediaEvent;
use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Model\Album;
use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Util\FileSystem;
use Jaccob\MediaBundle\Util\MediaHelperAwareTrait;
use Jaccob\MediaBundle\Util\PathBuilderAwareTrait;

use PommProject\Foundation\Where;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default importer implementat that must be used by any other
 */
class DefaultImporter extends ContainerAware
{
    use MediaHelperAwareTrait;
    use MediaModelAware;
    use PathBuilderAwareTrait;

    /**
     * Root working directory
     *
     * @var string
     */
    private $workingDirectory;

    /**
     * @var Jaccob\AccountBundle\Model\Account
     */
    private $owner;

    /**
     * @var TypeFactory
     */
    private $typeFactory;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        if (!$container) {
            return;
        }

        // Ensure the working directory
        $path = $container->getParameter('jaccob_media.directory.upload');
        FileSystem::ensureDirectory($path, true, true);
        $this->workingDirectory = $path;
    }

    /**
     * Get working directory
     *
     * @return string
     *   Root working directory
     */
    final public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * Set owner account
     *
     * @param \Jaccob\AccountBundle\Model\Account $account
     */
    final public function setOwner(Account $account)
    {
        $this->owner = $account;

        return $this;
    }

    /**
     * Get owner account
     *
     * @return \Jaccob\AccountBundle\Model\Account
     */
    final public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get file mime type
     *
     * @param string $filename
     *   Full file physical path
     */
    public function findMimeType($filename)
    {
        if (function_exists('finfo_open')) {
            $res = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($res, $filename);
            finfo_close($res);
        } else if (function_exists('mime_content_type')) {
            $mimetype = mime_content_type($filename);
        } else {
            $mimetype = 'application/octet-stream';
        }

        return $mimetype;
    }

    /**
     * Create instance from file
     *
     * @param \Jaccob\MediaBundle\Model\Album $album
     *   Target album
     * @param string $filename
     *   Full file physical path
     * @param string $workingDirectory
     *   Optional path prefix to strip from the file name to store
     *
     * @return \Jaccob\MediaBundle\Model\Media
     *   Entity will not be persisted
     */
    public function createEntityFromFile(Album $album, $filename)
    {
        if (!is_file($filename)) {
            throw new \RuntimeException("File does not exists or is not a regular file");
        }
        if (!is_readable($filename)) {
            throw new \RuntimeException("File is not readable");
        }

        $workingDirectory = $this->getWorkingDirectory();
        if ($workingDirectory && 0 === strpos($filename, $workingDirectory)) {
            $relativePath = substr($filename, strlen($workingDirectory) + 1);
        } else {
            $relativePath = $filename;
        }

        // @todo Handle other attributes here or where?
        $media = $this->getMediaModel()->createEntity([
            'name'          => basename($relativePath),
            'user_name'     => basename($relativePath),
            'path'          => dirname($relativePath),
            //'size'          => filesize($filename),
            'mimetype'      => $this->findMimeType($filename),
            'ts_added'      => new \DateTime(),
            'md5_hash'      => md5_file($filename),
        ]);

        $media->physical_path = $this->pathBuilder->buildPath($album, $media);

        return $media;
    }

    /**
     * Create media path relative to public files directory
     *
     * @param string $path
     *
     * @return string
     */
    final protected function createRealPath($path)
    {
        // Keep the file name ext
        if ($pos = strrpos($path, '.')) {
            $ext = substr($path, $pos);
        } else {
            $ext = '';
        }

        $path = Crypt::getSimpleHash($path, $this->owner->getSalt());

        return trim(preg_replace('/[^a-zA-Z0-9]{1,}/', '/', $path), "/") . $ext;
    }

    /**
     * Find or create album for the given media
     *
     * @param string $filename
     *
     * @return \Jaccob\MediaBundle\Model\Album
     */
    protected function findAlbum($filename)
    {
        $albumModel = $this->getAlbumModel();

        $workingDirectory = $this->getWorkingDirectory();
        if ($workingDirectory && 0 === strpos($filename, $workingDirectory)) {
            $path = substr($filename, strlen($workingDirectory) + 1);
        } else {
            $path = $filename;
        }
        $path = ltrim(dirname($path), '/');

        // We should definitely create the album if possible
        $album = $albumModel
            ->findWhere(
                (new Where())
                    ->andWhere('path = $*', [$path])
            )
        ;

        if (count($album)) {
            // FIXME Why does the fouque reset() not working on Iterator?
            foreach ($album as $item) {
                $album = $item;
                break;
            }
        } else {
            $album = $albumModel->createAndSave([
                'id_account'  => $this->getOwner()->getId(),
                'path'        => $path,
            ]);
        }

        return $album;
    }

    /**
     * Find or create device for the given media
     *
     * @param \Jaccob\MediaBundle\Model\Media $media
     *
     * @return \Jaccob\MediaBundle\Model\Device
     */
    protected function findDevice(Media $media)
    {
        $deviceModel = $this->getDeviceModel();

        // We should definitely create the album if possible
        $device = $deviceModel
            ->findWhere(
                (new Where())
                    ->andWhere('id_account = $*', [$this->getOwner()->getId()])
            )
        ;

        if (count($device)) {
            // FIXME Why does the fouque reset() not working on Iterator?
            foreach ($device as $item) {
                $device = $item;
                break;
            }
        } else {
            $device = $deviceModel->createAndSave([
                'id_account'  => $this->getOwner()->getId(),
                'name'        => "Unknown device",
            ]);
        }

        return $device;
    }

    /**
     * Import single media
     *
     * @param \Jaccob\MediaBundle\Model\Media $media
     *   The unsaved media to import
     * @param \Jaccob\MediaBundle\Model\Album $album
     *   The album to set into, if not set it will be created automatically
     *   during import
     */
    final public function import(Media $media, Album $album = null)
    {
        /* @var $typeFinder \Jaccob\MediaBundle\Type\TypeFinderService */
        $typeFinder = $this->container->get('jaccob_media.type_finder');

        if (!$album) {
            $album = $this->findAlbum($media->path);
        }

        $device     = $this->findDevice($media);
        $owner      = $this->getOwner();
        $mediaModel = $this->getMediaModel();
        $source     = FileSystem::pathJoin($this->getWorkingDirectory(), $media->path, $media->name);

        $media->id_album    = $album->getId();
        $media->id_account  = $owner->getId();
        $media->id_device   = $device->id;

        // Attempt loading by filename and path for graceful merge
        $existing = $mediaModel
            ->findWhere(
                (new Where())
                  ->andWhere('path = $*', [$media->path])
                  ->andWhere('name = $*', [$media->name])
                  ->andWhere('id_account = $*', [$owner->getId()])
                  ->andWhere('id_album = $*', [$album->id])
            )
        ;

        $type = $typeFinder->getTypeFor($media->mimetype);
        if ($type->isValid()) {
            /* $metadata = */ $type->findMetadata($media, $source);
            // @todo Save metadata later after update
        }

        if (!count($existing)) {

            // Get physical target (needs the data dir)
            $target = $this->mediaHelper->getOriginalPath($media);
            // Everything is relative find the real file path and create it
            // if necessary
            FileSystem::ensureDirectory(dirname($target), true, true);

            // Then copy everything
            if (!copy($source, $target)) {
                throw new \RuntimeException("Could not copy file");
            }

            $mediaModel->insertOne($media);

            $this->container->get('event_dispatcher')->dispatch(
                MediaEvent::INSERT,
                new MediaEvent($media, $album)
            );

        } else {

            // FIXME Why does the fouque reset() not working on Iterator?
            foreach ($existing as $item) {
                $existing = $item;
                break;
            }

            // FIXME: type handler might have populated stuff
            // if ($media->md5_hash !== $existing->md5_hash) {
                $values = $media->extract();
                $existing->hydrate($values);
                $mediaModel->updateOne($existing, array_keys($values));
            // }
            // @todo In case it wasn't really changed, do nothing.

            $media = $existing;

            $this->container->get('event_dispatcher')->dispatch(
                MediaEvent::UPDATE,
                new MediaEvent($media, $album)
            );
        }

        if (!$album->id_media_preview) {
            $album->id_media_preview = $media->id;
            $this->getAlbumModel()->updateOne($album, ['id_media_preview']);
        }
    }
}
