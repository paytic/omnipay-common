<?php

namespace ByTIC\Omnipay\Common\Message\Traits\HtmlResponses;

/**
 * Trait HasRedirectUrlTrait
 * @package ByTIC\Omnipay\Common\Message\Traits\HtmlResponses
 */
trait HasRedirectUrlTrait
{
    protected $redirectUrl = null;


    /**
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return $this->getRedirectUrl() != null;
    }

    /**
     * @return null
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param null $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }
}
