<?php

namespace ByTIC\Omnipay\Common;

use Omnipay\Common\ParametersTrait;

/**
 * Class Recurring
 * @package ByTIC\Omnipay\Common
 */
class Recurring
{
    use ParametersTrait;

    protected $times = 0;

    protected $interval = 0;

    /**
     * Recurring constructor.
     * @param null $parameters
     */
    public function __construct($parameters = null)
    {
        $this->initialize($parameters);
    }

    /**
     * @return int
     */
    public function getTimes(): int
    {
        return $this->times;
    }

    /**
     * @param int $times
     */
    public function setTimes(int $times)
    {
        $this->times = $times;
    }

    /**
     * @return int
     */
    public function getInterval(): int
    {
        return $this->interval;
    }

    /**
     * @param int $interval
     */
    public function setInterval(int $interval)
    {
        $this->interval = $interval;
    }
}
