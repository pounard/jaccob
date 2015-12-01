<?php

namespace Jaccob\MediaBundle\EventListener;

use Jaccob\MediaBundle\Event\MediaEvent;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MediaListener
{
    use ContainerAwareTrait;

    public function onMediaSave(MediaEvent $event)
    {
        $this
            ->container
            ->get('jaccob_media.job_manager')
            ->push(
                $event->getMedia()->id,
                'media_thumbnail'
            )
        ;
    }
}
