<?php

declare(strict_types=1);

namespace Paytic\Omnipay\Common\Gateway\Traits;

use Paytic\Omnipay\Common\Http\Client;

/**
 *
 */
trait HasPsr18ClientTrait
{
    protected function getDefaultHttpClient()
    {
        return new Client();
    }
}