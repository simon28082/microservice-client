<?php

namespace CrCms\Foundation\MicroService\Client;

use BadMethodCallException;
use ArrayAccess;

/**
 * Class ServiceData
 * @package CrCms\Foundation\MicroService\Client
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
    public function __construct($data)
    {
        $this->data = $this->resolveData($data);
    }

    /**
     * @param $data
     * @return mixed|null
     */
    protected function resolveData($data)
    {
        if ((bool)($newData = json_decode($data)) && json_last_error() === 0) {
            return $newData;
        } else {
            return null;
        }
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function data(string $key, $default = null)
    {
        return data_get($this->data, $key);
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

        return isset($this->data->$offset);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->data->$offset;
        }

        return null;
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