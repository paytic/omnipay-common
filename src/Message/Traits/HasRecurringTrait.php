<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

use ByTIC\Omnipay\Common\Recurring;
use Omnipay\Common\CreditCard;

/**
 * Trait HasRecurringTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
 */
trait HasRecurringTrait
{

    /**
     * @return Recurring
     */
    public function getRecurrence()
    {
        return $this->getParameter('recurrence');
    }

    /**
     * @param $value
     * @return static
     */
    public function setRecurrence($value)
    {
        if ($value && !$value instanceof Recurring) {
            $value = new CreditCard($value);
        }
        return $this->setParameter('recurrence', $value);
    }
}
