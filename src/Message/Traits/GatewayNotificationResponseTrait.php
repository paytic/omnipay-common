<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

/**
 * Class GatewayNotificationResponseTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
 *
 * @property \Symfony\Component\HttpFoundation\Request $httpRequest
 * @property [] $data
 *
 * @method mixed getDataProperty($key)
 */
trait GatewayNotificationResponseTrait
{

    /**
     * @param $key
     * @return mixed
     */
    public function getNotificationDataItem($key)
    {
        return $this->getDataProperty('notification')[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function hasNotificationDataItem($key)
    {
        return isset($this->data['notification'][$key]);
    }
}
