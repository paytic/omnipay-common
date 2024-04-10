<?php

namespace Paytic\Omnipay\Common\Tests\Gateway\Traits;

use Omnipay\Common\Http\ClientInterface;
use Paytic\Omnipay\Common\Tests\AbstractTest;
use Paytic\Omnipay\Common\Tests\Fixtures\Gateway;

/**
 * Class HasLanguageTraitTest
 * @package Paytic\Omnipay\Common\Tests\Gateway\Traits
 */
class HasPsr18ClientTraitTest extends AbstractTest
{
    /**
     */
    public function test_getClientDefault()
    {
        $gateway = new Gateway();
        $client = $gateway->getHttpClient();

        self::assertInstanceOf(ClientInterface::class, $client);
    }

}
