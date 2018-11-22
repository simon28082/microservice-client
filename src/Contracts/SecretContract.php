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
     * @return string
     */
    public function encrypt(array $data): string;

    /**
     * @param string $data
     * @param string $iv
     * @return array
     */
    public function decrypt(string $data, string $iv): array;

    /**
     * @return string
     */
    public function getIv(): string;
}