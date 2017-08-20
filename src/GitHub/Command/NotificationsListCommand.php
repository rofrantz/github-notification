<?php

namespace GitHub\Command;

use GitHub\Service\NotificationService;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package GitHub\Command
 */
class NotificationsListCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('List all notifications')
            ->setHelp('This command allows you to list all unread notifications from your GitHub account');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var NotificationService $notifications */
        $notifications = $this->getContainer()->get('notifications_service');

        $data = [];
        //print_r($notifications->getAll());die();
        foreach ($notifications->getAll() as $notification) {
            $type = $notification['subject']['type'];
            switch ($type) {
                case 'PullRequest':
                    $type = 'PR';
                    break;

                case 'Issue':
                    $type = 'I';
                    break;
            }
            $data[] = [
                $notification['id'],
                $notification['repository']['name'],
                $type,
                $notification['subject']['title'],
                $notification['subject']['url'],
            ];
        }

        $count = count($data);
        if ($count) {
            $table = new Table($output);
            $table
                ->setHeaders(array('Id', 'Repository', 'Type', 'Title', 'Url'))
                ->setRows($data)
            ;
            $table->render();

            $output->writeln("There are <info>" . $count . "</info> unread notifications");
        } else {
            $output->writeln("<info>There are no unread notifications ✓</info>");
        }
    }
}