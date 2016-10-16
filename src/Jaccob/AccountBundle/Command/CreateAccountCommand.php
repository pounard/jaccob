<?php

namespace Jaccob\AccountBundle\Command;

use Jaccob\AccountBundle\AccountModelAware;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class CreateAccountCommand extends ContainerAwareCommand
{
    use AccountModelAware;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('account:create')
            ->setDescription('Create new account')
            ->addArgument('name', InputArgument::REQUIRED, "User display name")
            ->addArgument('mail', InputArgument::REQUIRED, "User e-mail address")
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Create the user as administrator')
            ->addOption('disabled', null, InputOption::VALUE_NONE, 'Leave the user disabled upon creation')
            ->addOption('send-mail', null, InputOption::VALUE_NONE, 'Send login token mail to new user')
        ;
    }

    /**app
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = trim($input->getArgument('name'));
        $mail = trim($input->getArgument('mail'));

        if (!$name) {
            $output->writeln('<error>name cannot be empty</error>');
            return;
        }
        if (!$mail) {
            $output->writeln('<error>mail cannot be empty</error>');
            return;
        }

        $account = $this->getAccountModel()->findUserByMail($mail);
        if ($account) {
            throw new \InvalidArgumentException(sprintf("%s: account already exists", $mail));
        }

        $this->getAccountModel()->createAndSave([
            'mail' => $mail,
            'user_name' => $name,
            'is_admin' => $input->getOption('admin'),
            'is_active' => !$input->getOption('disabled'),
        ]);

        if ($input->getOption('send-mail')) {
            $output->writeln('<error>sending mail is not implemented yet</error>');
        }

        $output->writeln('<info>user created with no password, use the account:password command to set one</info>');
    }
}

