<?php


namespace Paytic\Omnipay\Common\Tests\Message\Traits;

use Paytic\Omnipay\Common\Tests\AbstractTest;
use Paytic\Omnipay\Common\Tests\Fixtures\Message\PurchaseRequest;

/**
 * Class RequestDataGetWithValidationTraitTest
 * @package Paytic\Omnipay\Common\Tests\Message\Traits
 */
class RequestDataGetWithValidationTraitTest extends AbstractTest
{
    public function test_create()
    {
        $httpClient = $this->getHttpClient();
        $httpRequest = $this->getHttpRequest();
        $message = new PurchaseRequest($httpClient, $httpRequest);

        self::assertInstanceOf(PurchaseRequest::class, $message);
    }
}
