<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use ArrayAccess;
use MongoDB\ChangeStream;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Session;
use MongoDB\GridFS\Bucket;
use MongoDB\Tests\UnifiedSpecTests\Constraint\IsBsonType;
use MongoDB\Tests\UnifiedSpecTests\Constraint\IsStream;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use function array_key_exists;
use function assertArrayHasKey;
use function assertArrayNotHasKey;
use function assertInternalType;
use function assertThat;
use function isInstanceOf;
use function logicalOr;
use function sprintf;

class EntityMap implements ArrayAccess
{
    /** @var array */
    private $map = [];

    /** @var Constraint */
    private static $isSupportedType;

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
        assertThat($value, self::isSupportedType());

        $this->map[$key] = $value;
    }

    /**
     * @see http://php.net/arrayaccess.offsetunset
     */
    public function offsetUnset($key)
    {
        Assert::fail('Entities cannot be removed from the map');
    }

    private static function isSupportedType() : Constraint
    {
        if (self::$isSupportedType === null) {
            self::$isSupportedType = logicalOr(
                isInstanceOf(Client::class),
                isInstanceOf(Database::class),
                isInstanceOf(Collection::class),
                isInstanceOf(Session::class),
                isInstanceOf(Bucket::class),
                isInstanceOf(ChangeStream::class),
                IsBsonType::any(),
                new IsStream()
            );
        }

        return self::$isSupportedType;
    }
}
