<?php

namespace Paytic\Omnipay\Common\Gateway;

use Exception;
use Nip\Http\Request\PsrRequest;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
abstract class AbstractGateway extends \Omnipay\Common\AbstractGateway
{
    use Traits\HasPsr18ClientTrait;

    protected function createRequest($class, array $parameters)
    {
        $httpRequest = $this->httpRequest;
        if ($httpRequest instanceof PsrRequest) {
            $httpRequest = $httpRequest->getSymfonyRequest();
        } elseif (!$httpRequest instanceof Request && $httpRequest instanceof ServerRequestInterface) {
            $httpRequest = (new HttpFoundationFactory())->createRequest($httpRequest);
        }

        $request = new $class($this->httpClient, $httpRequest);

        return $request->initialize(array_replace($this->getParameters(), $parameters));
    }

    public function acceptNotification(array $options = [])
    {
        throw new Exception('Method not implemented');
    }
}

