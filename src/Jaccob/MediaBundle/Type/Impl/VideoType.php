<?php

namespace Jaccob\MediaBundle\Type\Impl;

use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Toolkit\ExternalFFMpegVideoToolkit;
use Jaccob\MediaBundle\Type\TypeInterface;
use Jaccob\MediaBundle\Util\Date;
use Jaccob\MediaBundle\Util\FileSystem;

use Symfony\Component\DependencyInjection\ContainerAware;
use Jaccob\MediaBundle\Toolkit\ExternalImagickImageToolkit;

class VideoType extends ContainerAware implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMetadata(Media $media, $filename = null)
    {
        $ret = [];

        if (!$filename) {
            $publicDirectory = $this->container->getParameter('jaccob_media.directory.public');
            $filename = FileSystem::pathJoin($publicDirectory, 'full', $media->physical_path);
        }

        $toolkit = new ExternalFFMpegVideoToolkit();
        $data = $toolkit->findMetaData($filename);

        // Find video stream attributes to update the media instance
        if (!empty($data['stream'])) {
            foreach ($data['stream'] as $streamData) {
                // Note: I choosed not to test with 'codec_type' to be liberal
                if (isset($streamData['width'])) {
                    $media->width = $streamData['width'];
                    $media->height = $streamData['height'];
                    break;
                }
            }
        }

        // Attempt find any kind of date stamp
        foreach ($data as $section => $sectionData) {
            if ('stream' === $section) {
                continue; // Skip specific stream section (see toolkit impl)
            }

            foreach ($sectionData as $key => $value) {
                // Be over liberal...
                if (preg_match('@(time|date)@is', $key)) {
                    // Attempt to parse the date any mean possible
                    foreach ([
                        Date::EXIF_DATETIME,
                        Date::MYSQL_DATETIME,
                        Date::MYSQL_DATE,
                        \DateTime::ATOM,
                        \DateTime::COOKIE,
                        \DateTime::ISO8601,
                        \DateTime::RFC1036,
                        \DateTime::RFC1123,
                        \DateTime::RFC2822,
                        \DateTime::RFC3339,
                        \DateTime::RFC822,
                        \DateTime::RFC850,
                        \DateTime::RSS,
                        \DateTime::W3C,
                    ] as $format) {
                        try {
                            $date = \DateTime::createFromFormat($format, $value);
                            if ($date) {
                                $media->ts_user_date = $date;
                                break; // Not really sure, but found something
                            }
                        } catch (\Exception $e) {
                            // The cost of being too liberal, continue...
                        }
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTwigTemplateName()
    {
        return 'JaccobMediaBundle:Type:video.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function canDoThumbnail()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnailExtension(Media $media, $size, $modifier)
    {
        return 'png';
    }

    /**
     * {@inheritdoc}
     */
    public function createThumbnail(Media $media, $inFile, $outFile, $size, $modifier)
    {
        $toolkit = new ExternalFFMpegVideoToolkit();
        $toolkit->generateThumbnail($inFile, $outFile);

        $toolkit = new ExternalImagickImageToolkit();

        switch ($modifier) {

            case 'm':
                $toolkit->scaleTo($outFile, $outFile, $size, $size, true);
                break;

            case 'h':
                $toolkit->scaleTo($outFile, $outFile, null, $size, true);
                break;

            case 'w':
                $toolkit->scaleTo($outFile, $outFile, $size, null, true);
                break;

            case 's':
                $toolkit->scaleAndCrop($outFile, $outFile, $size, $size);
                break;

            default:
                return false;
        }

        return true;
    }
}
