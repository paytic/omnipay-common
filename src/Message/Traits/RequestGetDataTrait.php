<?php

namespace ByTIC\Omnipay\Common\Message\Traits;

/**
 * Trait RequestGetDataTrait
 * @package ByTIC\Omnipay\Mobilpay\Message\Traits
 */
trait RequestGetDataTrait
{

    /**
     * @return array
     */
    public function getData()
    {
        $this->validateData();

        return $this->populateData();
    }

    protected function validateData()
    {
        $fields = null;
        if (method_exists($this, 'validateDataFields')) {
            $fields = $this->validateDataFields();
        }

        if (is_array($fields) && count($fields)) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $this->validate(...$fields);
        }
    }

    /**
     * @return array
     */
    protected function populateData()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public abstract function validate();
}
