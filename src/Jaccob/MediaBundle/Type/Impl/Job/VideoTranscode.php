<?php

namespace Jaccob\MediaBundle\Type\Impl\Job;

use Jaccob\MediaBundle\Import\DefaultImporter;
use Jaccob\MediaBundle\MediaModelAware;
use Jaccob\MediaBundle\Model\Media;
use Jaccob\MediaBundle\Toolkit\ExternalFFMpegVideoToolkit;
use Jaccob\MediaBundle\Type\Job\JobInterface;
use Jaccob\MediaBundle\Util\MediaHelperAwareTrait;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Console\Output\OutputInterface;

class VideoTranscode implements JobInterface
{
    use ContainerAwareTrait;
    use MediaHelperAwareTrait;
    use MediaModelAware;

    /**
     * {@inheritdoc}
     */
    public function run(Media $media, array $options = [], OutputInterface $output)
    {
        $container = $this->container;
        $formats = $container->getParameter('jaccob_media.video.derivatives');

        if (empty($formats)) {
            return;
        }

        $derivativeModel = $this->getMediaDerivativeModel();

        foreach ($formats as $options) {

            if (empty($options['format'])) {
                trigger_error(sprintf("'format' is missing from format options"), E_USER_WARNING);
                continue;
            }
            if (empty($options['video'])) {
                trigger_error(sprintf("'video' is missing from format options"), E_USER_WARNING);
                continue;
            }
            if (empty($options['audio'])) {
                $options['audio'] = null;
            }

            $source = $this->mediaHelper->getOriginalPath($media);
            $target = $source;
            if ($pos = strrpos($target, '.')) {
                $target = substr($target, 0, $pos) . '.' . $options['format'];
            }
            if (file_exists($target)) {
                print "Ignoring '" . $target . "'\n";
                continue;
            }
            print "Generating '" . $target . "'\n";

            // Convert!
            $toolkit = new ExternalFFMpegVideoToolkit();
            $toolkit->transcode($source, $target, $options['video'], $options['format'], $options['audio'], $options);

            $mimetype = DefaultImporter::findFileMimeType($target);

            $derivative = $derivativeModel->findOneByMediaAndMimetype($media->id, $mimetype);
            if ($derivative) {
                $derivative->name           = basename($target);
                $derivative->physical_path  = dirname($target);
                $derivative->md5_hash       = md5_file($target);
                $derivative->mimetype       = $mimetype;
                $derivativeModel->updateOne($derivative, ['name', 'physical_path', 'md5_hash', 'mimetype']);
            } else {
                $derivativeModel->createAndSave([
                    'id_media'      => $media->id,
                    'name'          => basename($target),
                    'physical_path' => $media->physical_path,
                    'filesize'      => filesize($target),
                    'width'         => $media->width,
                    'height'        => $media->height,
                    'md5_hash'      => md5_file($target),
                    'mimetype'      => $mimetype,
                ]);
            }
        }
    }
}
