<?php
namespace App\Notifications\Channels;

use JPush\Client;
use Illuminate\Notifications\Notification;

class JpushChannel
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send($notifiable, Notification $notification)
    {
        $notification->toJpush($notifiable, $this->client->push())->send();
    }
}
