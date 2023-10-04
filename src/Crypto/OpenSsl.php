<?php

declare(strict_types=1);


namespace Paytic\Omnipay\Common\Crypto;

use Exception;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;
use phpseclib3\Crypt\RC4;

/**
 *
 * @inspiration https://github.com/nextcloud/server/blob/master/apps/encryption/lib/Crypto/Crypt.php
 */
class OpenSsl
{
    public const ERROR_ENCRYPT_DATA = 0x10000002;
    public const ERROR_DECRYPT_DATA = 0x10000003;

    /**
     * Custom implementation of openssl_open()
     *
     * @param OpenSSLAsymmetricKey|OpenSSLCertificate|array|string $private_key
     */
    public function open(string $data, string &$output, string $encrypted_key, $private_key, string $cipher_algo): bool
    {
        try {
            // check if RC4 is used
            if (false == $this->isAlgoRC4($cipher_algo)) {
                $result = $this->openSsl($data, $output, $encrypted_key, $private_key, $cipher_algo);
            } else {
                $result = $this->openRc4($data, $output, $encrypted_key, $private_key, $cipher_algo);
            }
            if ($result === false) {
                throw new Exception();
            }
        } catch (Exception $exception) {
            $this->throwException(
                "Error while opening crypt data!",
                self::ERROR_DECRYPT_DATA,
                $exception
            );
        }

        return $result;
    }

    public function isAlgoRC4(string $cipher_algo): bool
    {
        return strcasecmp($cipher_algo, "rc4") === 0;
    }

    /**
     * @param string $data
     * @param string $output
     * @param string $encrypted_key
     * @param OpenSSLAsymmetricKey|array|string|OpenSSLCertificate $private_key
     * @param string $cipher_algo
     * @return bool
     */
    protected function openSsl(
        string $data,
        string &$output,
        string $encrypted_key,
        OpenSSLAsymmetricKey|array|string|OpenSSLCertificate $private_key,
        string $cipher_algo
    ): bool {
        return openssl_open($data, $output, $encrypted_key, $private_key, $cipher_algo);
    }

    /**
     * @param string $data
     * @param string $output
     * @param string $encrypted_key
     * @param OpenSSLAsymmetricKey|array|string|OpenSSLCertificate $private_key
     * @param string $cipher_algo
     * @return bool
     */
    protected function openRc4(
        string $data,
        string &$output,
        string $encrypted_key,
        OpenSSLAsymmetricKey|array|string|OpenSSLCertificate $private_key,
        string $cipher_algo
    ): bool {
        $result = false;
        // decrypt the intermediate key with RSA
        if (openssl_private_decrypt($encrypted_key, $intermediate, $private_key, OPENSSL_PKCS1_PADDING)) {
            // decrypt the file key with the intermediate key
            // using our own RC4 implementation
            $output = $this->rc4Decrypt($data, $intermediate);
            $result = (strlen($output) === strlen($data));
        }

        return $result;
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
     * @param Exception $exception
     * @param string|null $code
     * @param string|null $message
     * @throws Exception
     */
    public function throwException($message = null, $code = null, $exception = null)
    {
        $message = $message ?: "Error open ssl!";
        if ($exception) {
            $message .= " Exception:" . $exception->getMessage();
        }
        $message .= " Reason:";
        while (($errorString = openssl_error_string())) {
            $message .= $errorString . "\n";
        }
        $code = $code ?: self::ERROR_ENCRYPT_DATA;
        throw new Exception($message, $code);
    }

    public function seal(
        string $data,
        string &$sealed_data,
        array &$encrypted_keys,
        array $public_key,
        string $cipher_algo
    ): int|false {
        $cipher_algo = strtolower($cipher_algo);
        $result = false;
        try {
            // check if RC4 is used
            if ($this->isAlgoRC4($cipher_algo)) {
                $result = $this->sealWithRC4($data, $sealed_data, $encrypted_keys, $public_key, $cipher_algo);
            } else {
                $result = $this->sealWithOpenSsl($data, $sealed_data, $encrypted_keys, $public_key, $cipher_algo);
            }
            if ($result === false) {
                throw new Exception();
            }
        } catch (Exception $exception) {
            $this->throwException(
                "Error while sealing data!",
                self::ERROR_ENCRYPT_DATA,
                $exception
            );
        }
        return $result;
    }

    protected function sealWithRC4(
        string $data,
        string &$sealed_data,
        array &$encrypted_keys,
        array $public_key,
        string $cipher_algo
    ) {
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
    private function rc4Encrypt(
        string $data,
        string $secret
    ): string {
        $rc4 = new RC4();
        /** @psalm-suppress InternalMethod */
        $rc4->setKey($secret);

        return $rc4->encrypt($data);
    }

    protected function sealWithOpenSsl(
        string $data,
        string &$sealed_data,
        array &$encrypted_keys,
        array $public_key,
        string $cipher_algo
    ) {
        return openssl_seal($data, $sealed_data, $encrypted_keys, $public_key, $cipher_algo);
    }

}