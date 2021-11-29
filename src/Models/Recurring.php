<?php

namespace Paytic\Omnipay\Common\Models;

use Paytic\Omnipay\Common\Models\Traits\HasParametersTrait;

/**
 * Class Recurring
 * @package Paytic\Omnipay\Common\Models
 */
class Recurring
{
    use HasParametersTrait;

    const DAILY = 'daily';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';

    protected $times = 0;

    protected $interval = 0;

    protected $type = self::DAILY;

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

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }
}
