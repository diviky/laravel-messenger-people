<?php

namespace NotificationChannels\MessengerPeople;

use GuzzleHttp\Client as Guzzle;

class Client
{
    const API_URL = 'https://api.messengerpeople.dev';
    /**
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * @var ChannelConfig
     */
    protected $config;

    /**
     * Mobtexting constructor.
     *
     * @param \GuzzleHttp\Client $http
     * @param ChannelConfig     $config
     */
    public function __construct(ChannelConfig $config, $http = null)
    {
        $this->http   = $http ?: new Guzzle();
        $this->config = $config;
    }

    /**
     * Send a MessageAbstract to the a phone number.
     *
     * @param MessageAbstract $message
     * @param string          $to
     * @param bool            $useAlphanumericSender
     *
     * @throws CouldNotSendNotification
     *
     * @return mixed
     */
    public function send(MessageAbstract $message, $to)
    {
        if ($message instanceof MessageAbstract) {
            return $this->sendMessage($message, $to);
        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }

    /**
     * Send an sms message using the Mobtexting Service.
     *
     * @param Message $message
     * @param string  $to
     *
     * @throws CouldNotSendNotification
     *
     * @return \GuzzleHttp\Client
     */
    protected function sendMessage(MessageAbstract $message, $to)
    {
        $to   = $message->getTo() ?: $to;
        $data = [
            'identifier' => $this->getFrom($message) . ':' . preg_replace("/[^0-9]/", '', $to),
            'payload'    => $message->getPayload(),
        ];

        return $this->sendPayload($data);
    }

    /**
     * Get the from address from message, or config.
     *
     * @param MessageAbstract $message
     *
     * @throws CouldNotSendNotification
     *
     * @return string
     */
    protected function getFrom(MessageAbstract $message)
    {
        if (!$from = $message->getFrom() ?: $this->config->get('number_id')) {
            throw Exceptions\CouldNotSendNotification::missingFrom();
        }

        return $from;
    }

    protected function getAccessToken()
    {
        $response = $this->http->request('POST', 'https://auth.messengerpeople.dev/token', [
            'form_params' => [
                'client_id'     => $this->config->get('client_id'),
                'client_secret' => $this->config->get('client_secret'),
                'grant_type'    => 'client_credentials',
                'scope'         => 'messages:send messages:read messages:delete media:create',
            ],
        ]);

        $responseData = json_decode($response->getBody());

        return $responseData->access_token;
    }

    /**
     * @param mixed $payload
     * @return Response
     */
    public function sendPayload($payload)
    {
        $headers = [
            'Content-Type'  => 'application/vnd.messengerpeople.v1+json',
            'Accept'        => 'application/vnd.messengerpeople.v1+json',
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];

        return $this->http->request('POST', self::API_URL . '/messages', [
            'headers' => $headers,
            'body'    => \json_encode($payload),
        ]);
    }
}
