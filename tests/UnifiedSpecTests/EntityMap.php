<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use ArrayAccess;
use MongoDB\Driver\Session;
use PHPUnit\Framework\Assert;
use function array_key_exists;
use function assertArrayHasKey;
use function assertArrayNotHasKey;
use function assertInternalType;
use function sprintf;

class EntityMap implements ArrayAccess
{
    /** @var array */
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
        assertInternalType('string', $key);

        return array_key_exists($key, $this->map);
    }

    /**
     * @see http://php.net/arrayaccess.offsetget
     */
    public function offsetGet($key)
    {
        assertInternalType('string', $key);
        assertArrayHasKey($key, $this->map, sprintf('No entity is defined for "%s"', $key));

        return $this->map[$key];
    }

    /**
     * @see http://php.net/arrayaccess.offsetset
     */
    public function offsetSet($key, $value)
    {
        assertInternalType('string', $key);
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
