<?php

namespace ByTIC\Omnipay\Common\Models;

/**
 * Interface TokenInterface
 * @package ByTIC\Omnipay\Common\Models
 */
interface TokenInterface
{

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     */
    public function setId($id);
}
