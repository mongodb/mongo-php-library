<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use ArrayAccess;
use BadMethodCallException;
use InvalidArgumentException;
use OutOfBoundsException;
use function is_string;
use function sprintf;

class EntityMap implements ArrayAccess
{
    private $map = [];

    /**
     * Check whether an entity exists in the map.
     *
     * @see http://php.net/arrayaccess.offsetexists
     * @param mixed $key
     * @return boolean
     * @throws InvalidArgumentException if the key is not a string
     */
    public function offsetExists($key)
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException('Key is not a string');
        }

        return array_key_exists($key, $this->map);
    }

    /**
     * Return an entity from the map.
     *
     * @see http://php.net/arrayaccess.offsetget
     * @param mixed $key
     * @return mixed
     * @throws InvalidArgumentException if the key is not a string
     * @throws OutOfBoundsException if the entity is not defined
     */
    public function offsetGet($key)
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException('Key is not a string');
        }

        if (! $this->offsetExists($key)) {
            throw new OutOfBoundsException(sprintf('No entity is defined for "%s"', $key));
        }

        return $this->map[$key];
    }

    /**
     * Assigns an entity to the map.
     *
     * @see http://php.net/arrayaccess.offsetset
     * @param mixed $key
     * @param mixed $value
     * @throws InvalidArgumentException if the key is not a string
     * @throws OutOfBoundsException if the entity is already defined
     */
    public function offsetSet($key, $value)
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException('Key is not a string');
        }

        if ($this->offsetExists($key)) {
            throw new OutOfBoundsException('Entity already exists for key "%s" and cannot be replaced');
        }

        $this->map[$key] = $value;
    }

    /**
     * Not supported.
     *
     * @see http://php.net/arrayaccess.offsetunset
     * @param mixed $key
     * @throws BadMethodCallException
     */
    public function offsetUnset($key)
    {
        throw new BadMethodCallException('Entities cannot be removed from the map');
    }
}
