<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

/**
 * Trait DataAccessorsTrait
 * @package ByTIC\Omnipay\Common\Message\Traits
 *
 * @property $data
 */
trait DataAccessorsTrait
{

    /**
     * @param $name
     * @return mixed
     */
    public function getDataProperty($name, $default = null)
    {
        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function hasDataProperty($name)
    {
        return isset($this->data[$name]);
    }
}
