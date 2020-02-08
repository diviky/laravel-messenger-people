<?php

namespace NotificationChannels\MessengerPeople;

abstract class MessageAbstract
{
    /**
     * The message content.
     *
     * @var string
     */
    public $text;

    /**
     * The phone number the message should be sent from.
     *
     * @var string
     */
    public $from;

    /**
     * The phone number the message should be sent to.
     *
     * @var string
     */
    public $to;

    /**
     * @var array
     */
    public $params = [];

    /**
     * Create a new message instance.
     *
     * @param string $text
     */
    public function __construct($payload = '')
    {
        $this->setPayload($payload);
    }

    /**
     * Create a message object.
     *
     * @param string $text
     *
     * @return static
     */
    public static function create($text = '')
    {
        return new static($text);
    }

    /**
     * Set the phone number the message should be sent from.
     *
     * @param string $from
     *
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the phone number the message should be sent to.
     *
     * @param string $from
     * @param mixed  $to
     *
     * @return $this
     */
    public function to($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get the from address.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Get the phone number the message should be sent to.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Get the value of params.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set Param.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Set Param.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function getParam($key, $default = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        };

        return $default;
    }

    /**
     * Set the value of params.
     *
     * @param array $params
     *
     * @return self
     */
    public function setParams(array $params)
    {
        $this->params = \array_merge($this->params, $params);

        return $this;
    }
}
