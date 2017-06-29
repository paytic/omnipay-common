<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

/**
 * Class GatewayNotificationRequestTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
 *
 * @property \Symfony\Component\HttpFoundation\Request $httpRequest
 */
trait GatewayNotificationRequestTrait
{

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->isValidNotification()) {
            return $this->parseNotification();
        }

        return [];
    }

    /**
     * @return bool
     */
    protected function isValidNotification()
    {
        return false;
    }

    /**
     * @return bool|mixed
     */
    protected function parseNotification()
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
