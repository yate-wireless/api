<?php

/*
 * Yate core products API wrapper library
 * (c) Alexey Pavlyuts <alexey@pavlyuts.ru>
 */

namespace Yate\Api;

/**
 * Represent API call result
 *
 * This class allows to access API answer fields either as associative array
 * elements and as class properties.
 *
 */
class ApiResponse implements \ArrayAccess
{

    protected array $result;

    /**
     * Populates instance with API answer data
     *
     * @param array $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * Returns API answer as associative array
     *
     * @return array
     */
    public function asArray(): array
    {
        return $this->result;
    }

    // Methods to allow array-like and property-like access
    public function offsetExists($offset): bool
    {
        return isset($this->result[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->result[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        // Do nothing, data read-only
    }

    public function offsetUnset($offset): void
    {
        // Do nothing, data read-only
    }

    public function __get($name)
    {
        return $this->result[$name] ?? null;
    }

    public function __isset($name)
    {
        return isset($this->result[$name]);
    }

    public function __set($name, $value)
    {
        // Do nothing, data read-only
    }

    public function __unset($name)
    {
        // Do nothing, data read-only
    }
}
