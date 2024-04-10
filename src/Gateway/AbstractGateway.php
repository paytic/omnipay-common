<?php

namespace Paytic\Omnipay\Common\Gateway;

use Exception;

/**
 *
 */
abstract class AbstractGateway extends \Omnipay\Common\AbstractGateway
{
    use Traits\HasPsr18ClientTrait;

    public function acceptNotification(array $options = [])
    {
        throw new Exception('Method not implemented');
    }
}

