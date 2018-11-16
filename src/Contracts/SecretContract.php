<?php

namespace CrCms\Microservice\Client\Contracts;

/**
 * Interface SecretContract
 * @package CrCms\Microservice\Client\Contracts
 */
interface SecretContract
{
    /**
     * @param array $data
     * @return array
     */
    public function encrypt(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function decrypt(array $data): array;
}