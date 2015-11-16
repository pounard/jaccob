<?php

namespace Jaccob\MediaBundle;

use Jaccob\MediaBundle\DependencyInjection\TypeFinderCompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JaccobMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TypeFinderCompilerPass());
    }

    public function boot()
    {
        // @todo Find a better way via services.yml or parameters.yml
        /* @var $jobFactory \Jaccob\MediaBundle\Type\Job\JobFactory */
        $jobFactory = $this->container->get('jaccob_media.job_factory');
        $jobFactory->addType('video_transcode', '\Jaccob\MediaBundle\Type\Impl\Video\TranscodeJob');
    }
}
