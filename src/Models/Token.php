<?php

namespace ByTIC\Omnipay\Common\Models;

use ByTIC\Omnipay\Common\Models\Traits\HasParametersTrait;
use Nip\Utility\Date;

/**
 * Class Token
 * @package ByTIC\Omnipay\Common\Models
 */
class Token implements TokenInterface
{
    use HasParametersTrait;

    /**
     * @var string
     */
    protected $id;

    protected $expirationDate = null;

    /**
     * Token constructor.
     * @param null $parameters
     */
    public function __construct($parameters = null)
    {
        $this->initialize($parameters);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function hasExpirationDate(): bool
    {
        return $this->getExpirationDate() !== null;
    }

    public function isExpired(): bool
    {
       $date = $this->getExpirationDate();
        if ($date === null) {
            return false;
        }
        return $date->isPast();
    }

    /**
     * @return null|Date
     */
    public function getExpirationDate()
    {
        if (empty($this->expirationDate)) {
            return null;
        }
        return Date::parse($this->expirationDate);
    }

    /**
     * @param null|string $expirationDate
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }
}
