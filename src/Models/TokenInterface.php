<?php

namespace Paytic\Omnipay\Common\Models;

/**
 * Interface TokenInterface
 * @package Paytic\Omnipay\Common\Models
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
