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
    public function getDataProperty($name)
    {
        return $this->data[$name];
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
