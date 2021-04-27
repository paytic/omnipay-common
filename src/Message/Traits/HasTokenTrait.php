<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

use ByTIC\Omnipay\Common\Models\Token;

/**
 * Trait HasTokenTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
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
