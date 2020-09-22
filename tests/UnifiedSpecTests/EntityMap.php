<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Session;
use PHPUnit\Framework\Assert;
use ArrayAccess;
use function sprintf;

class EntityMap implements ArrayAccess
{
    private $map = [];

    public function __destruct()
    {
        /* TODO: Determine if this is actually necessary. References to session
         * entities should not persist between tests. */
        foreach ($this->map as $entity) {
            if ($entity instanceof Session) {
                $entity->endSession();
            }
        }
    }

    /**
     * @see http://php.net/arrayaccess.offsetexists
     */
    public function offsetExists($key)
    {
        assertIsString($key);

        return array_key_exists($key, $this->map);
    }

    /**
     * @see http://php.net/arrayaccess.offsetget
     */
    public function offsetGet($key)
    {
        assertIsString($key);
        assertArrayHasKey($key, $this->map, sprintf('No entity is defined for "%s"', $key));

        return $this->map[$key];
    }

    /**
     * @see http://php.net/arrayaccess.offsetset
     */
    public function offsetSet($key, $value)
    {
        assertIsString($key);
        assertArrayNotHasKey($key, $this->map, sprintf('Entity already exists for key "%s" and cannot be replaced', $key));

        $this->map[$key] = $value;
    }

    /**
     * @see http://php.net/arrayaccess.offsetunset
     */
    public function offsetUnset($key)
    {
        Assert::fail('Entities cannot be removed from the map');
    }
}
