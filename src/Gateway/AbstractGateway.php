<?php

namespace Paytic\Omnipay\Common\Gateway;

/**
 *
 */
abstract class AbstractGateway extends \Omnipay\Common\AbstractGateway
{
    use Traits\HasPsr18ClientTrait;
}

