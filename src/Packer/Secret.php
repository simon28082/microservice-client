<?php

namespace CrCms\Microservice\Client\Packer;

use CrCms\Microservice\Client\Contracts\SecretContract;
use UnexpectedValueException;

/**
 * Class Secret
 * @package CrCms\Microservice\Client\Packer
 */
class Secret implements SecretContract
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $cipher;

    /**
     * @var string
     */
    protected $iv;

    /**
     * Data constructor.
     * @param Repository $config
     */
    public function __construct(string $key, string $cipher)
    {
        $this->key = $key;
        $this->cipher = $cipher;
    }

    /**
     * @param array $data
     * @return string
     */
    public function encrypt(array $data): string
    {
        $value = openssl_encrypt(
            serialize($data),
            $this->cipher,
            $this->key,
            OPENSSL_ZERO_PADDING,
            $this->iv()
        );

        if ($value === false) {
            throw new UnexpectedValueException('Could not encrypt the data.');
        }

        return $value;
    }

    /**
     * @param string $data
     * @param string $iv
     * @return array
     */
    public function decrypt(string $data, string $iv): array
    {
        $value = openssl_decrypt(
            $data,
            $this->cipher,
            $this->key,
            OPENSSL_ZERO_PADDING,
            base64_decode($iv)
        );

        if ($value === false) {
            throw new UnexpectedValueException('Could not decrypt the data.');
        }

        $array = unserialize($value);
        if (!is_array($array)) {
            throw new UnexpectedValueException("Parse data error");
        }

        return $array;
    }

    /**
     * @return string
     */
    protected function iv()
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        return $this->iv;
    }

    /**
     * @return string
     */
    public function getIv(): string
    {
        return base64_encode($this->iv);
    }
}