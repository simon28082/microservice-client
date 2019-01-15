<?php

namespace CrCms\Microservice\Client\Packer;

use Illuminate\Contracts\Encryption\Encrypter;
use UnexpectedValueException;

/**
 * Class Packer.
 */
class Packer
{
    /**
     * @var Encrypter
     */
    protected $secret;

    /**
     * @var Encrypter
     */
    protected $isSecret;

    /**
     * Packer constructor.
     *
     * @param Encrypter $secret
     * @param bool      $isSecret
     */
    public function __construct(Encrypter $secret, bool $isSecret = true)
    {
        $this->secret = $secret;
        $this->isSecret = $isSecret;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function pack(array $data): string
    {
        return $this->isSecret ?
            $this->secret->encrypt($data) :
            base64_encode(json_encode($data));
    }

    /**
     * @param string $data
     * @param bool   $encryption
     *
     * @return array
     */
    public function unpack(string $data): array
    {
        if ($this->isSecret) {
            return $this->secret->decrypt($data);
        }

        $data = json_decode(base64_decode($data), true);
        if (json_last_error() !== 0) {
            throw new UnexpectedValueException('Parse data error: '.json_last_error_msg());
        }

        return $data;
    }
}
