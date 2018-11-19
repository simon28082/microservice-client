<?php

namespace CrCms\Microservice\Client\Packer;

use CrCms\Microservice\Client\Contracts\SecretContract;
use UnexpectedValueException;

/**
 * Class Packer
 * @package CrCms\Microservice\Client\Packer
 */
class Packer
{
    /**
     * @var SecretContract
     */
    protected $secret;

    /**
     * Packer constructor.
     * @param SecretContract $secret
     */
    public function __construct(SecretContract $secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param array $data
     * @param bool $encryption
     * @return string
     */
    public function pack(array $data, $encryption = true): string
    {
        $data = $encryption ? ['data' => $this->secret->encrypt($data), 'iv' => $this->secret->getIv()] : $data;
        return base64_encode(json_encode($data));
    }

    /**
     * @param string $data
     * @param bool $encryption
     * @return array
     */
    public function unpack(string $data, $encryption = true): array
    {
        $data = json_decode(base64_decode($data), true);
        if (json_last_error() !== 0) {
            throw new UnexpectedValueException("Parse data error: " . json_last_error_msg());
        }

        return $encryption ?
            $this->secret->decrypt($data['data'], $data['iv']) :
            $data;
    }
}