<?php

namespace ByTIC\Omnipay\Common\Library;

use Exception;

/**
 * Class Signer
 * @package ByTIC\Omnipay\Common\Library
 */
class Signer
{
    const ERROR_LOAD_KEY = 0x10000001;
    const ERROR_ENCRYPT_DATA = 0x10000002;
    const ERROR_DECRYPT_DATA = 0x10000003;

    const KEY_TYPE_PUBLIC = 1;
    const KEY_TYPE_PRIVATE = 2;
    protected $certificate = null;
    protected $certificateData = null;
    protected $privateKey = null;
    protected $privateKeyData = null;

    /**
     * @param $content
     * @return array
     */
    public function sealContentWithRSA($content)
    {
        $key = $this->getCertificateData();
        $encData = null;
        $envKeys = null;
        try {
            $result = openssl_seal($content, $encData, $envKeys, [$key]);
            if ($result === false) {
                throw new Exception();
            }
        } catch (Exception $exception) {
            $this->throwExceptionOpenssl(
                "Error while sealing data!",
                self::ERROR_ENCRYPT_DATA,
                $exception
            );
        }

        return [$encData, $envKeys];
    }

    /**
     * @param $sealedData
     * @param $envKey
     * @return null
     */
    public function openContentWithRSA($sealedData, $envKey)
    {
        $key = $this->getPrivateKeyData();

        $decryptedData = null;
        try {
            $result = openssl_open($sealedData, $decryptedData, $envKey, $key);
            if ($result === false) {
                throw new Exception();
            }
        } catch (Exception $exception) {
            $this->throwExceptionOpenssl(
                "Error while opening crypt data!",
                self::ERROR_DECRYPT_DATA,
                $exception
            );
        }

        return $decryptedData;
    }

    /**
     * @return string
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
            $key = 'file://'.$key;
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
    public function throwExceptionOpenssl($message = null, $code = null, $exception = null)
    {
        $message = $message ? $message : "Error open ssl!";
        if ($exception) {
            $message .= " Exception:".$exception->getMessage();
        }
        $message .= " Reason:";
        while (($errorString = openssl_error_string())) {
            $message .= $errorString."\n";
        }
        $code = $code ? $code : self::ERROR_ENCRYPT_DATA;
        throw new Exception($message, $code);
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

    protected function initPrivateKeyData()
    {
        $key = $this->getPrivateKey();
        if ($key == null) {
            throw new Exception('Private Key must be set in order to use Signer');
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
