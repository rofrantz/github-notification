<?php

namespace GitHub\Command;

use GitHub\Service\NotificationService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package GitHub\Command
 */
class NotificationsProcessCommand extends BaseCommand
{
    /** @var  NotificationService */
    private $notificationService;

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Process all notifications')
            ->setHelp('This command allows you to list all unread notifications from your GitHub account');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->notificationService = $this->getContainer()->get('notifications_service');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $notifications = $this->notificationService->getAll();

        $count = count($notifications);
        if ($count) {
            $rulesByRepository = $this->getRulesByRepository();
            $processed = 0;
            foreach ($notifications as $notification) {
                $actions = $this->takeActionsForNotification($notification, $rulesByRepository);
                if ($this->applyActionsForNotification($notification, $actions)) {
                    $output->writeln("Processed #{$notification['id']} - " .
                        "{$notification['subject']['title']} " .
                        "(" . implode(",", $actions) . ") " .
                        "[{$notification['repository']['name']}]"
                    );
                    $processed++;
                }
            }

            $output->writeln("There were " .
                ($count ? "<info>{$processed}</info>/<info>{$count}</info>" : "no") . " " .
                "notifications <comment>processed</comment>"
            );
        } else {
            $output->writeln("<info>There are no unread notifications to be processed ✓</info>");
        }
    }

    private function getRulesByRepository(): array
    {
        return $this->getContainer()->getParameter('repositories');
    }

    private function takeActionsForNotification(array $notification, array $rulesByRepository): array
    {
        $actions = [];
        $notificationRepo = $notification['repository']['name'];
        foreach ($rulesByRepository as $repo) {
            $currentRepo = key($repo);
            if (in_array($notificationRepo, ['all', $currentRepo])) {
                foreach ($repo['rules'] as $rule) {
                    $field = null;
                    switch ($rule['by']) {
                        case 'title':
                            $field = $notification['subject']['title'];
                            break;
                    }

                    if (
                        (isset($rule['words']) && $this->filterFieldByWords($field, $rule['words'])) ||
                        (isset($rule['exact']) && in_array($field, $rule['exact'])) ||
                        (isset($rule['regexp']) && $this->filterFieldByRegexp($field, $rule['regexp']))
                    ) {
                        foreach ($rule['actions'] as $action) {
                            $actions[] = $action;
                        }
                    }
                }
            }
        }

        return $actions;
    }

    private function applyActionsForNotification(array $notification, array $actions): bool
    {
        $applied = false;
        $id = $notification['id'];
        foreach (array_unique($actions) as $action) {
            switch ($action) {
                case 'mark-as-read':
                    $this->notificationService->markAsRead($id);
                    $applied = true;
                    break;

                case 'unsubscribe':
                    $this->notificationService->createSubscription($id, ['subscribed' =>false, 'ignored' => true]);
                    $applied = true;
                    break;
            }
        }

        return $applied;
    }

    private function filterFieldByWords(string $field, array $words): bool
    {
        $field = preg_replace(['/\-/', '/\x20+/', '/[^\w\d\x20]/i'], [' ', ' ', ''], strtolower($field));
        return $this->stringContains($field, $words);
    }

    private function filterFieldByRegexp(string $field, array $expressions): bool
    {
        $match = false;
        foreach ($expressions as $expression) {
            if (preg_match("/{$expression}/", $field) === 1) {
                $match = true;
                break;
            }
        }

        return $match;
    }

    private function stringContains(string $string, array $matches)
    {
        return count(array_intersect(array_map('strtolower', explode(' ', $string)), $matches)) > 0;
    }
}