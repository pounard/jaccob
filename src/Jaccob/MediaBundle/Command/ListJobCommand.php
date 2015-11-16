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
        /* @var $jobManager \Jaccob\MediaBundle\Model\JobQueueManager */
        $jobManager = $container->get('jaccob_media.job_manager');

        // $jobManager->runNext();
        // return;

        $rows = [];
        foreach ($jobManager->listAll() as $data) {
            $rows[] = [
                $data['id'],
                $data['type'],
                $data['id_media'],
                $data['ts_added']->format('Y-m-d H:i:s'),
                $data['is_running'] ? 'Yes' : 'No',
                $data['is_failed'] ? 'Yes' : 'No',
            ];
        }

        $this->getHelper('table')
            ->setHeaders(['id', 'type', 'media', 'added', 'running', 'failed'])
            ->setRows($rows)
            ->render($output)
        ;
    }
}
