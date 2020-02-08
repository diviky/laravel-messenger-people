<?php

namespace NotificationChannels\MessengerPeople;

class ChannelConfig
{
    /**
     * @var array
     */
    protected $config;

    /**
     * MobtextingConfig constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get the auth token.
     *
     * @return string
     */
    public function get($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        return $this->config[$key];
    }
}
