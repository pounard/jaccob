<?php

namespace Jaccob\MediaBundle\Command;

use Jaccob\MediaBundle\MediaModelAware;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption(
                'page',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Which page to display',
                1
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Number of elements to display',
                20
            )
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Filter by type'
            )
        ;
    }

    /**app
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /* @var $jobFactory \Jaccob\MediaBundle\Type\Job\JobFactory */
        $jobFactory = $container->get('jaccob_media.job_factory');
        /* @var $jobManager \Jaccob\MediaBundle\Model\JobQueueManager */
        $jobManager = $container->get('jaccob_media.job_manager');

        $conditions = [];

        $limit  = $input->getOption('limit');
        $page   = $input->getOption('page');
        $type   = $input->getOption('type');

        if ($type) {
          $conditions['type'] = $type;
        }

        $total = $jobManager->countAll($conditions);

        $rows = [];
        foreach ($jobManager->listAll($conditions, $limit, $page) as $data) {
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

        $output->writeln(sprintf("Displaying %d/%d jobs, page %d/%d",
            count($rows),
            $total,
            $page,
            ceil($total / $limit)
        ));
    }
}

