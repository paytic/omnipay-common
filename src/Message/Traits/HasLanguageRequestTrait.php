<?php

namespace Paytic\Omnipay\Common\Message\Traits;

/**
 * Trait HasLanguageRequestTrait
 * @package Paytic\Omnipay\Common\Message\Traits
 */
trait HasLanguageRequestTrait
{
    /**
     * @param $value
     * @return mixed
     */
    public function setLang($value)
    {
        return $this->setParameter('lang', $value);
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->getParameter('lang');
    }
}
