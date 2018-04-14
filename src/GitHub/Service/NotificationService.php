<?php
namespace GitHub\Service;

use Github\Api\CurrentUser;
use Github\Api\CurrentUser\Notifications;
use Github\Client;

class NotificationService
{
    /** @var Client */
    private $client;

    /**
     * NotificationService constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    private function getNotificationsApi(): Notifications
    {
        /** @var CurrentUser $currentUser */
        $currentUser = $this->client->api('currentUser');
        return $currentUser->notifications();
    }

    public function getAll(array $params = []):array
    {
        return $this->getNotificationsApi()->all($params);
    }

    public function markAsRead($id, array $params = [])
    {
        return $this->getNotificationsApi()->markAsRead($id, $params);
    }

    public function createSubscription($id, array $params)
    {
        return $this->getNotificationsApi()->createSubscription($id, $params);
    }
}