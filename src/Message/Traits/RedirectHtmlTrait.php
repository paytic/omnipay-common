<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse as HttpRedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Class RedirectHtmlTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
 *
 * @method RequestInterface getRequest
 * @method array getData
 */
trait RedirectHtmlTrait
{
    use HasViewTrait;
    use DataAccessorsTrait;

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * Returns whether the transaction should continue
     * on a redirected page
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @return HttpRedirectResponse|HttpResponse
     */
    public function getRedirectResponse()
    {
        if (!$this instanceof RedirectResponseInterface || !$this->isRedirect()) {
            throw new RuntimeException('This response does not support redirection.');
        }

        if ('GET' === $this->getRedirectMethod()) {
            return HttpRedirectResponse::create($this->getRedirectUrl());
        } elseif ('POST' === $this->getRedirectMethod()) {
            $output = $this->getRedirectHTML();

            return HttpResponse::create($output);
        }

        throw new RuntimeException('Invalid redirect method "' . $this->getRedirectMethod() . '".');
    }

    /**
     * Returns redirect URL method
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * Returns the redirect URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        if (!$this->hasDataProperty('redirectUrl')) {
            throw new \InvalidArgumentException(
                "Missing paramenter redirectURL in ResponseMessage " . get_class($this)
            );
        }
        return $this->getDataProperty('redirectUrl');
    }

    /**
     * @return string
     */
    public function getRedirectHTML()
    {
        return $this->getViewContent();
    }

    /**
     * @return string
     */
    public function getViewFile()
    {
        return '/redirect';
    }

    protected function initViewVars()
    {
        $this->getView()->set('inputsHidden', $this->generateHiddenInputs());
    }

    /**
     * @return string
     */
    public function generateHiddenInputs()
    {
        $hiddenFields = '';
        foreach ($this->getRedirectData() as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $iKey => $iValue) {
                    $nKey = $key . '[' . $iKey . ']';
                    $hiddenFields .= $this->generateHiddenInput($nKey, $iValue) . "\n";
                }
            } else {
                $hiddenFields .= $this->generateHiddenInput($key, $value) . "\n";
            }
        }

        return $hiddenFields;
    }

    /**
     * Returns the FORM data for the redirect
     *
     * @return array
     */
    public function getRedirectData()
    {
        $data = $this->getData();
        $data = $this->filterRedirectData($data);
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function filterRedirectData($data)
    {
        return $data;
    }

    /**
     * @param $key
     * @param $value
     * @return string
     */
    public function generateHiddenInput($key, $value)
    {
        $key = htmlentities($key, ENT_QUOTES, 'UTF-8', false);
        $value = htmlentities($value, ENT_QUOTES, 'UTF-8', false);

        return sprintf(
            '<input type="hidden" name="%1$s" value="%2$s" />',
            $key,
            $value
        );
    }
}
