<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Helper;

/**
 * Trait ParametersTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
 */
trait ParametersTrait
{
    /**
     * Validate the request.
     *
     * This method is called internally by gateways to avoid wasting time with an API call
     * when the request is clearly invalid.
     *
     * @param string ... a variable length list of required parameters
     * @throws InvalidRequestException
     */
    public function validateCard(...$args)
    {
        /** @var CreditCard $card */
        $card = $this->getCard();

        foreach ($args as $key) {
            $method = 'get'.ucfirst(Helper::camelCase($key));
            $value = $card->$method();
            if (! isset($value)) {
                throw new InvalidRequestException("The $key parameter is required for card");
            }
        }
    }
}
