<?php

namespace Paytic\Omnipay\Common\Message\Traits;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Trait SendDataRequestTrait
 * @package Paytic\Omnipay\Common\Message\Traits
 *
 * @property ResponseInterface $response
 */
trait SendDataRequestTrait
{

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return bool
     */
    public function sendData($data)
    {
        if (is_array($data) && count($data)) {
            $class = $this->getResponseClass();

            return $this->response = new $class($this, $data);
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getResponseClass()
    {
        $fullClassName = get_class($this);
        $partsClassName = explode('\\', $fullClassName);
        $classFirstName = array_pop($partsClassName);
        $classNamespacePath = implode('\\', $partsClassName);
        $class = str_replace('Request', 'Response', $classFirstName);

        return '\\' . $classNamespacePath . '\\' . $class;
    }
}
