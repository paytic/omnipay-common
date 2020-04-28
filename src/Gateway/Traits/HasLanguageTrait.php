<?php

namespace ByTIC\Omnipay\Common\Gateway\Traits;

/**
 * Trait HasLanguageRequestTrait
 * @package ByTIC\Omnipay\Common\Gateway\Traits
 */
trait HasLanguageTrait
{
    /**
     * @var string
     */
    protected $language = null;

    /**
     * @param $value
     * @return string
     */
    public function setLang($value)
    {
        return $this->language = $value;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->language;
    }
}
