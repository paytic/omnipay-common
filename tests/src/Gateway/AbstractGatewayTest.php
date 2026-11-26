<?php

namespace Paytic\Omnipay\Common\Tests\Gateway;

use Nip\Http\Request\PsrRequest;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use Paytic\Omnipay\Common\Tests\AbstractTest;
use Paytic\Omnipay\Common\Tests\Fixtures\Gateway;
use Paytic\Omnipay\Common\Tests\Fixtures\Message\PurchaseRequest;
use Symfony\Component\HttpFoundation\Request;

class AbstractGatewayTest extends AbstractTest
{
    public function testSymfonyRequestIsPassedThrough()
    {
        $httpRequest = Request::create('/callback?reference=123', 'POST');
        $gateway = $this->gatewayWithRequest($httpRequest);
        $gateway->setTestMode(true);

        $message = $gateway->makeRequest(['testMode' => false]);

        self::assertSame($httpRequest, $message->getHttpRequest());
        self::assertFalse($message->getTestMode());
        self::assertTrue($gateway->getTestMode());
    }

    public function testByticPsrRequestIncludesPsrChanges()
    {
        $original = new PsrRequest(Request::create('/callback?old=1', 'POST', ['old' => 'value']));
        $httpRequest = $original
            ->withQueryParams(['reference' => '123'])
            ->withParsedBody(['status' => 'paid'])
            ->withAttribute('route', 'callback')
            ->withHeader('X-Callback', 'signed')
            ->withBody(Stream::create('payload'));

        $gateway = $this->gatewayWithRequest($httpRequest);
        $message = $gateway->makeRequest();
        $converted = $message->getHttpRequest();

        self::assertInstanceOf(Request::class, $converted);
        self::assertSame('123', $converted->query->get('reference'));
        self::assertSame('paid', $converted->request->get('status'));
        self::assertSame('callback', $converted->attributes->get('route'));
        self::assertSame('signed', $converted->headers->get('X-Callback'));
        self::assertSame('payload', $converted->getContent());
        self::assertSame('1', $original->getQueryParams()['old']);

        $nextMessage = $gateway->makeRequest();
        self::assertSame('123', $nextMessage->getHttpRequest()->query->get('reference'));
    }

    public function testOtherPsrServerRequestsAreSupported()
    {
        $httpRequest = (new ServerRequest('POST', 'https://example.com/callback'))
            ->withQueryParams(['reference' => '456'])
            ->withParsedBody(['status' => 'paid']);

        $converted = $this->gatewayWithRequest($httpRequest)->makeRequest()->getHttpRequest();

        self::assertSame('456', $converted->query->get('reference'));
        self::assertSame('paid', $converted->request->get('status'));
    }

    public function testInvalidHttpRequestStillFails()
    {
        $this->expectException(\TypeError::class);
        $this->gatewayWithRequest(new \stdClass())->makeRequest();
    }

    private function gatewayWithRequest($httpRequest)
    {
        $gateway = new class extends Gateway {
            public function setHttpRequest($httpRequest)
            {
                $this->httpRequest = $httpRequest;
            }

            public function makeRequest(array $parameters = [])
            {
                return $this->createRequest(PurchaseRequest::class, $parameters);
            }
        };
        $gateway->setHttpRequest($httpRequest);

        return $gateway;
    }
}
