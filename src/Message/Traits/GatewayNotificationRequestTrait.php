<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Class GatewayNotificationRequestTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
 *
 * @property \Symfony\Component\HttpFoundation\Request $httpRequest
 */
trait GatewayNotificationRequestTrait
{
    /**
     * Send the request
     *
     * @return ResponseInterface|bool
     */
    public function send()
    {
        if ($this->isProviderRequest()) {
            return parent::send();
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function isProviderRequest()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->isProviderRequest()) {
            $this->generateData();
        }

        return false;
    }

    /**
     * @return bool|mixed
     */
    protected function generateData()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function hasGet()
    {
        foreach (func_get_args() as $key) {
            if (!$this->httpRequest->query->has($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasPOST()
    {
        foreach (func_get_args() as $key) {
            if (!$this->httpRequest->request->has($key)) {
                return false;
            }
        }

        return true;
    }
}
