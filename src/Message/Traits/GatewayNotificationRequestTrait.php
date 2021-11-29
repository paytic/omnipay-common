<?php

namespace Paytic\Omnipay\Common\Message\Traits;

/**
 * Class GatewayNotificationRequestTrait
 * @package Paytic\Omnipay\Common\Message\Traits
 *
 * @property \Symfony\Component\HttpFoundation\Request $httpRequest
 */
trait GatewayNotificationRequestTrait
{
    use RequestDataPersistentTrait;

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if ($this->isValidNotification()) {
            $this->setDataItem('notification', $this->parseNotification());
        }

        return $this->getDataArray();
    }

    /**
     * @return bool
     */
    abstract public function isValidNotification();

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
