<?php

namespace NotificationChannels\MessengerPeople;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;

abstract class ChannelAbstract
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * MobtextingChannel constructor.
     *
     * @param Client     $client
     * @param Dispatcher $events
     */
    public function __construct(Client $client, Dispatcher $events)
    {
        $this->client = $client;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed                                  $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws CouldNotSendNotification
     *
     * @return mixed
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            $to     = $this->getTo($notifiable);
            $method = 'to' . \ucfirst($this->channel);

            $message = $notification->{$method}($notifiable);

            if (\is_string($message)) {
                $message = new Message($message);
            }

            if (!$message instanceof MessageAbstract) {
                throw Exceptions\CouldNotSendNotification::invalidMessageObject($message);
            }

            $response = $this->client->send($message, $to);

            if (!in_array($response->getStatusCode(), [200, 202])) {
                throw Exceptions\CouldNotSendNotification::serviceRespondedWithAnError($response);
            }

        } catch (\Exception $e) {
            $event = new NotificationFailed(
                $notifiable,
                $notification,
                $this->channel,
                ['message' => $e->getMessage(), 'exception' => $e]
            );

            if (\function_exists('event')) { // Use event helper when possible to add Lumen support
                event($event);
            } else {
                $this->events->fire($event);
            }
        }
    }

    /**
     * Get the address to send a notification to.
     *
     * @param mixed $notifiable
     *
     * @return mixed
     */
    protected function getTo($notifiable)
    {
        if ($notifiable->routeNotificationFor($this->channel)) {
            return $notifiable->routeNotificationFor($this->channel);
        }

        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        if (isset($notifiable->mobile)) {
            return $notifiable->mobile;
        }

        if (isset($notifiable->phone)) {
            return $notifiable->phone;
        }

        return false;
    }
}
