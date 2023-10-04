<?php

declare(strict_types=1);

namespace Paytic\Omnipay\Common\Library;

use Exception;
use phpseclib3\Crypt\RC4;

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
        try {
            $result = $this->opensslSeal($content, $encData, $envKeys, [$key], $cipher_algo);
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
     *
     * @return null
     * @throws Exception
     */
    public function openContentWithRSA($sealedData, $envKey)
    {
        $key = $this->getPrivateKeyData();

        $decryptedData = null;
        try {
            $result = openssl_open($sealedData, $decryptedData, $envKey, $key, "RC4");
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
    public function throwExceptionOpenssl($message = null, $code = null, $exception = null)
    {
        $message = $message ? $message : "Error open ssl!";
        if ($exception) {
            $message .= " Exception:" . $exception->getMessage();
        }
        $message .= " Reason:";
        while (($errorString = openssl_error_string())) {
            $message .= $errorString . "\n";
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

    private function opensslSeal(
        string $data,
        string &$sealed_data,
        array &$encrypted_keys,
        array $public_key,
        string $cipher_algo
    ): int|false {
        $cipher_algo = strtolower($cipher_algo);
        // check if RC4 is used
        if (strcasecmp($cipher_algo, "rc4") !== 0) {
            return openssl_seal($data, $sealed_data, $encrypted_keys, $public_key, $cipher_algo);
        }

        $result = false;
        // make sure that there is at least one public key to use
        if (count($public_key) >= 1) {
            // generate the intermediate key
            $intermediate = openssl_random_pseudo_bytes(16, $strong_result);

            // check if we got strong random data
            if ($strong_result) {
                // encrypt the file key with the intermediate key
                // using our own RC4 implementation
                $sealed_data = $this->rc4Encrypt($data, $intermediate);
                if (strlen($sealed_data) === strlen($data)) {
                    // prepare the encrypted keys
                    $encrypted_keys = [];

                    // iterate over the public keys and encrypt the intermediate
                    // for each of them with RSA
                    foreach ($public_key as $tmp_key) {
                        if (openssl_public_encrypt($intermediate, $tmp_output, $tmp_key, OPENSSL_PKCS1_PADDING)) {
                            $encrypted_keys[] = $tmp_output;
                        }
                    }

                    // set the result if everything worked fine
                    if (count($public_key) === count($encrypted_keys)) {
                        $result = strlen($sealed_data);
                    }
                }
            }
        }
        return $result ?? false;
    }

    /**
     * Uses phpseclib RC4 implementation
     */
    private function rc4Decrypt(
        string $data,
        string $secret
    ): string {
        $rc4 = new RC4();
        /** @psalm-suppress InternalMethod */
        $rc4->setKey($secret);

        return $rc4->decrypt($data);
    }

    /**
     * Uses phpseclib RC4 implementation
     */
    private function rc4Encrypt(
        string $data,
        string $secret
    ): string {
        $rc4 = new RC4();
        /** @psalm-suppress InternalMethod */
        $rc4->setKey($secret);

        return $rc4->encrypt($data);
    }
}
