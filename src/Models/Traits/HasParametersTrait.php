<?php

namespace Paytic\Omnipay\Common\Models\Traits;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

trait HasParametersTrait
{
    /**
     * The request parameters
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * @param array $parameters
     * @return $this
     */
    public function initialize(array $parameters = [])
    {
        $this->parameters = new ParameterBag;

        Helper::initialize($this, $parameters);

        return $this;
    }
}