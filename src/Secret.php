<?php

namespace CrCms\Microservice\Client;

use CrCms\Microservice\Client\Contracts\SecretContract;
use Illuminate\Contracts\Config\Repository;
use RangeException;

/**
 * Class Secret
 * @package CrCms\Microservice\Client
 */
class Secret implements SecretContract
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Repository
     */
    protected $config;

    /**
     * Data constructor.
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
        $this->options = $this->secretOptions();
    }

    /**
     * @param array $data
     * @return array
     */
    public function encrypt(array $data): array
    {
        if ($this->options['status'] === false) {
            return $data;
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->options['cipher']));
        $data = openssl_encrypt(
            serialize($data),
            $this->options['cipher'],
            $this->options['key'],
            OPENSSL_ZERO_PADDING,
            $iv
        );

        return ['data' => $data, 'iv' => base64_encode($iv)];
    }

    /**
     * @param array $data
     * @return array
     */
    public function decrypt(array $data): array
    {
        if ($this->options['status'] === false) {
            return $data;
        }

        $array = unserialize(
            openssl_decrypt(
                $data['data'],
                $this->options['cipher'],
                $this->options['key'],
                OPENSSL_ZERO_PADDING,
                base64_decode($data['iv'])
            )
        );

        if (!is_array($array)) {
            throw new RangeException("Parse content error : {$data}");
        }

        return $array;
    }

    /**
     * @return array
     */
    protected function secretOptions(): array
    {
        return [
            'key' => $this->config->get('microservice-client.secret'),
            'cipher' => $this->config->get('microservice-client.secret_cipher'),
            'status' => $this->config->get('microservice-client.secret_status'),
        ];
    }
}