<?php

namespace NotificationChannels\MessengerPeople;

class Message extends MessageAbstract
{
    /**
     * The message content.
     *
     * @var string
     */
    public $payload = [];

    public function getPayload()
    {
        return $this->payload;
    }

    public function setPayload($payload)
    {
        if (is_string($payload)) {
            $payload = [
                'type' => 'text',
                'text' => $payload,
            ];
        }

        $this->payload = (array) $payload;

        return $this;
    }
}
