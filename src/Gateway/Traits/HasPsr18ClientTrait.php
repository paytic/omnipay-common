<?php

declare(strict_types=1);

namespace Paytic\Omnipay\Common\Gateway\Traits;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Omnipay\Common\Http\Client;

/**
 *
 */
trait HasPsr18ClientTrait
{
    protected function getDefaultHttpClient()
    {
        $httpClient = Psr18ClientDiscovery::find();
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        return new Client($httpClient, $requestFactory);
    }
}