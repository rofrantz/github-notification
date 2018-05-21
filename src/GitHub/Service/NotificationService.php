<?php
namespace GitHub\Service;

use Github\Api\ApiInterface;
use Github\Api\CurrentUser;
use Github\Api\CurrentUser\Notifications;
use Github\Client;
use Github\ResultPager;

class NotificationService
{
    /** @var Client */
    private $client;

    /** @var ResultPager */
    private $paginator;

    /** @var ApiInterface */
    private $api;

    /**
     * NotificationService constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    private function api(): Notifications
    {
        if (!$this->api) {
            /** @var CurrentUser $currentUser */
            $currentUser = $this->client->api('currentUser');
            $this->api = $currentUser->notifications();
        }

        return $this->api;
    }

    public function paginator(): ResultPager
    {
        if (!$this->paginator) {
            $this->paginator = new ResultPager($this->client);
        }

        return $this->paginator;
    }

    public function getAll(array $params = []): array
    {
        return $this->paginator()->fetchAll($this->api(), 'all', $params);
    }

    public function markAsRead($id, array $params = [])
    {
        return $this->api()->markAsRead($id, $params);
    }

    public function createSubscription($id, array $params)
    {
        return $this->api()->createSubscription($id, $params);
    }
}