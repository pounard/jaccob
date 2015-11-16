<?php

namespace Jaccob\MediaBundle\Command;

use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunNextJobCommand extends ContainerAwareCommand
{
    use MediaModelAware;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media:job-run-next')
            ->setDescription('Run next job')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /* @var $jobFactory \Jaccob\MediaBundle\Type\Job\JobFactory */
        $jobFactory = $container->get('jaccob_media.job_factory');
        /* @var $jobManager \Jaccob\MediaBundle\Model\JobQueueManager */
        $jobManager = $container->get('jaccob_media.job_manager');

        $jobManager->runNext();
    }
}
