<?php

namespace Jaccob\MediaBundle\Command;

use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListJobCommand extends ContainerAwareCommand
{
    use MediaModelAware;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('media:job-list')
            ->setDescription('List queued jobs')
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
        /* @var $jobFactory \Jaccob\MediaBundle\Model\JobQueueManager */
        $jobManager = $container->get('jaccob_media.job_manager');

        var_dump($jobManager->runNext());
    }
}
