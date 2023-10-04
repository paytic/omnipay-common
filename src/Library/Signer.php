<?php

declare(strict_types=1);

namespace Paytic\Omnipay\Common\Library;

use Exception;
use Paytic\Omnipay\Common\Crypto\OpenSsl;

/**
 * Class Signer
 * @package Paytic\Omnipay\Common\Library
 */
class Signer
{
    public const ERROR_LOAD_KEY = 0x10000001;
    public const ERROR_ENCRYPT_DATA = 0x10000002;
    public const ERROR_DECRYPT_DATA = 0x10000003;

    public const KEY_TYPE_PUBLIC = 1;
    public const KEY_TYPE_PRIVATE = 2;

    protected $certificate = null;
    protected $certificateData = null;
    protected $privateKey = null;
    protected $privateKeyData = null;

    protected $openSsl;

    public function __construct()
    {
        $this->openSsl = new OpenSsl();
    }

    /**
     * @param $content
     *
     * @return array
     * @throws Exception
     */
    public function sealContentWithRSA($content, $cipher_algo = 'RC4')
    {
        $key = $this->getCertificateData();
        $encData = '';
        $envKeys = [];
        $this->openSsl->seal($content, $encData, $envKeys, [$key], $cipher_algo);

        return [$encData, $envKeys];
    }

    /**
     * @param $sealedData
     * @param $envKey
     *
     * @return null
     * @throws Exception
     */
    public function openContentWithRSA($sealedData, $envKey)
    {
        $key = $this->getPrivateKeyData();
        $decryptedData = '';
        $this->openSsl->open($sealedData, $decryptedData, $envKey, $key, 'RC4');
        return $decryptedData;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCertificateData()
    {
        if ($this->certificateData === null) {
            $this->initCertificateData();
        }

        return $this->certificateData;
    }

    /**
     * @param null $certificateData
     */
    protected function setCertificateData($certificateData)
    {
        $this->certificateData = $certificateData;
    }

    /**
     * @throws Exception
     */
    public function initCertificateData()
    {
        $certificate = $this->getCertificate();
        if ($certificate == null) {
            throw new Exception('Certificate must be set in order to use Signer');
        }
        $this->setCertificateData($this->loadKey($certificate, self::KEY_TYPE_PUBLIC));
    }

    /**
     * @return mixed
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param mixed $certificate
     * @return $this
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;

        return $this;
    }

    /**
     * @param $key
     * @param $type
     * @return bool|resource
     * @throws Exception
     */
    protected function loadKey($key, $type)
    {
        $key = $this->prefixCertificateKeyPath($key);
        $key = $this->format($key, $type);
        $result = $type == self::KEY_TYPE_PUBLIC ? openssl_pkey_get_public($key) : openssl_pkey_get_private($key);
        if ($result === false) {
            $errorMessage = "Error while loading openssl [{$key}]!";
            $errorMessage .= $type == self::KEY_TYPE_PUBLIC ? '[PublicKey]' : '[PrivateKey]';
            $this->throwExceptionOpenssl($errorMessage, self::ERROR_LOAD_KEY);
        }

        return $result;
    }

    /**
     * @param $key
     * @return string
     */
    protected function prefixCertificateKeyPath($key)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN'
            && is_file($key)
            && substr($key, 0, 7) != 'file://'
        ) {
            $key = 'file://' . $key;
        }

        return $key;
    }

    /**
     * Convert key to standard format
     *
     * @param $key
     * @param $type
     *
     * @return string
     */
    public function format($key, $type)
    {
        if (is_file($key)) {
            $key = file_get_contents($key);
        }
        if (is_string($key) && strpos($key, '-----') === false) {
            $key = $this->convertKey($key, $type);
        }

        return $key;
    }

    /**
     * Convert one line key to standard format
     *
     * @param $key
     * @param $type
     *
     * @return string
     */
    public function convertKey($key, $type)
    {
        $lines = [];
        if ($type == self::KEY_TYPE_PUBLIC) {
            $lines[] = '-----BEGIN PUBLIC KEY-----';
        } else {
            $lines[] = '-----BEGIN RSA PRIVATE KEY-----';
        }
        /** @noinspection PhpVariableNamingConventionInspection */
        for ($i = 0; $i < strlen($key); $i += 64) {
            $lines[] = trim(substr($key, $i, 64));
        }
        if ($type == self::KEY_TYPE_PUBLIC) {
            $lines[] = '-----END PUBLIC KEY-----';
        } else {
            $lines[] = '-----END RSA PRIVATE KEY-----';
        }

        return implode("\n", $lines);
    }

    /**
     * @param Exception $exception
     * @param string|null $code
     * @param string|null $message
     * @throws Exception
     */
    public function throwExceptionOpenssl($message = null, $code = self::ERROR_ENCRYPT_DATA, $exception = null)
    {
        return $this->openSsl->throwException($message, $code, $exception);
    }

    /**
     * @param $content
     * @param int $alg
     * @return null|string
     * @throws Exception
     */
    public function signContentWithRSA($content, $alg = OPENSSL_ALGO_SHA1)
    {
        $key = $this->getPrivateKeyData();

        $sign = null;
        try {
            openssl_sign($content, $sign, $key, $alg);
        } catch (Exception $exception) {
            if ($exception->getCode() == 2) {
                $this->throwExceptionOpenssl(
                    "Error while loading X509 public key certificate!",
                    null,
                    $exception
                );
            }
        }
        openssl_free_key($key);
        $sign = base64_encode($sign);

        return $sign;
    }

    /**
     * @return null
     * @throws Exception
     */
    public function getPrivateKeyData()
    {
        if ($this->certificateData === null) {
            $this->initPrivateKeyData();
        }

        return $this->privateKeyData;
    }

    /**
     * @param null $privateKeyData
     */
    protected function setPrivateKeyData($privateKeyData)
    {
        $this->privateKeyData = $privateKeyData;
    }

    /**
     * @throws Exception
     */
    protected function initPrivateKeyData()
    {
        $key = $this->getPrivateKey();
        if ($key == null) {
            throw new Exception('Private Key must be set in order to use Signer', self::ERROR_LOAD_KEY);
        }
        $this->setPrivateKeyData($this->loadKey($key, self::KEY_TYPE_PRIVATE));
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param mixed $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }
}
