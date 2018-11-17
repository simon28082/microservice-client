<?php

namespace CrCms\Microservice\Client;

use BadMethodCallException;
use ArrayAccess;
use UnexpectedValueException;

/**
 * Class ServiceData
 * @package CrCms\Microservice\Client
 */
class ServiceData implements ArrayAccess
{
    /**
     * @var object
     */
    protected $data;

    /**
     * ServiceData constructor.
     * @param $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * @param $data
     * @return object
     */
    protected function resolveData($data)
    {
        $newData = json_decode($data);
        if (json_last_error() !== 0) {
            throw new UnexpectedValueException("Parse error, The data {$data}");
        }

        return $newData;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function data(string $key, $default = null)
    {
        if (is_null($this->data)) {
            return $default;
        }
        return data_get($this->data, $key, $default);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        }

        return null;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        if (is_null($this->data)) {
            return false;
        }

        return $this->data($offset, '--$--') !== '--$--';
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->data($offset, null);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("not support method[offsetSet]");
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException("not support method[offsetUnset]");
    }
}