<?php

namespace Paytic\Omnipay\Common\Message\Traits;

use Paytic\Omnipay\Common\Models\Token;

/**
 * Trait HasTokenTrait
 * @package Paytic\Omnipay\Common\Message\Traits
 */
trait HasTokenTrait
{
    /**
     * @return Token
     */
    public function getToken(): ?Token
    {
    }
}
