<?php

namespace Jaccob\MediaBundle\Event;

use Jaccob\MediaBundle\Model\Album;
use Jaccob\MediaBundle\Model\Media;

use Symfony\Component\EventDispatcher\Event;

class MediaEvent extends Event
{
    const INSERT = 'jaccob_media.insert';

    const UPDATE = 'jaccob_media.update';

    /**
     * @var \Jaccob\MediaBundle\Model\Album
     */
    protected $album;

    /**
     * @var \Jaccob\MediaBundle\Model\Media
     */
    protected $media;

    /**
     * Default constructor
     *
     * @param \Jaccob\MediaBundle\Model\Album $album
     * @param \Jaccob\MediaBundle\Model\Media $media
     */
    public function __construct(Media $media, Album $album)
    {
        $this->album = $album;
        $this->media = $media;
    }

    /**
     * Get album
     *
     * @return \Jaccob\MediaBundle\Model\Album
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Get media
     *
     * @return \Jaccob\MediaBundle\Model\Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}
