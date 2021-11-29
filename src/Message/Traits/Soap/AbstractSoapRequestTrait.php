<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Paytic\Omnipay\Common\Message\Traits\Soap;

use SoapClient;
use SoapFault;

/**
 * Trait AbstractSoapRequestTrait
 * @package Paytic\Omnipay\Common\Message\Traits\Soap
 */
trait AbstractSoapRequestTrait
{
    /** @var  SoapClient */
    protected $soapClient;

    /**
     * The amount of time in seconds to wait for both a connection and a response.
     *
     * Total potential wait time is this value times 2 (connection + response).
     *
     * @var float
     */
    public $timeout = 10;

    /**
     * @return SoapClient
     */
    public function getSoapClient(): SoapClient
    {
        return $this->soapClient;
    }

    /**
     * @param SoapClient $soapClient
     */
    public function setSoapClient(SoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * Build the SOAP Client and the internal request object
     *
     * @return SoapClient
     * @throws \Exception
     */
    public function buildSoapClient()
    {
        if (!empty($this->soapClient)) {
            return $this->soapClient;
        }

        $context_options = [
            'http' => [
                'user_agent' => 'ByTIC Omnipay',
                'timeout' => $this->timeout,
            ],
        ];

        $context = stream_context_create($context_options);
        $options = $this->getSoapOptions();
        $options['stream_context'] = $context;

        try {
            // create the soap client
            $this->soapClient = new \SoapClient($this->getSoapEndpoint(), $options);

            return $this->soapClient;
        } catch (SoapFault $sf) {
            throw new \Exception($sf->getMessage(), $sf->getCode());
        }
    }

    /**
     * Send Data to the Gateway
     *
     * @param array $data
     * @return AbstractSoapResponseTrait
     * @throws \Exception
     */
    public function sendData($data)
    {
        // Build the SOAP client
        $soapClient = $this->buildSoapClient();

        // Replace this line with the correct function.
        $response = $this->runTransaction($soapClient, $data);

        $class = $this->getResponseClass();

        return $this->response = new $class($this, $response);
    }

    protected function getSoapOptionsGeneric(): array
    {
        // options we pass into the soap client
        // turn on HTTP compression
        // set the internal character encoding to avoid random conversions
        // throw SoapFault exceptions when there is an error
        $options = [
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            'encoding' => 'utf-8',
            'exceptions' => true,
            'connection_timeout' => $this->timeout
        ];

        // if we're in test mode, don't cache the wsdl
        if ($this->getTestMode()) {
            $options['cache_wsdl'] = WSDL_CACHE_NONE;
        } else {
            $options['cache_wsdl'] = WSDL_CACHE_BOTH;
        }

        return $options;

    }

    protected function getSoapOptions(): array
    {
        return $this->getSoapOptionsGeneric();
    }

    /**
     * @return string
     */
    abstract public function getSoapEndpoint();

    /**
     * Run the SOAP transaction
     *
     * Over-ride this in sub classes.
     *
     * @param SoapClient $soapClient
     * @param array $data
     * @return array
     * @throws \Exception
     */
    abstract protected function runTransaction($soapClient, $data);

    /**
     * @param SoapClient $soapClient
     * @param $method
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    protected function runSoapTransaction($soapClient, $method, $data = [])
    {
        try {
            return $soapClient->__soapCall($method, $data);
        } catch (SoapFault $soapFault) {
            return [
                "code" => $soapFault->faultcode,
                "message" => $soapFault->getMessage(),
            ];
        }
    }
}
