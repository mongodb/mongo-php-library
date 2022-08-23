<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use ArrayAccess;
use MongoDB\ChangeStream;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Session;
use MongoDB\GridFS\Bucket;
use MongoDB\Tests\UnifiedSpecTests\Constraint\IsBsonType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use ReturnTypeWillChange;
use stdClass;

use function array_key_exists;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\logicalOr;
use function sprintf;

class EntityMap implements ArrayAccess
{
    /** @var array */
    private $map = [];

    /**
     * Track lsids so they can be accessed after Session::getLogicalSessionId()
     * has been called.
     *
     * @var stdClass[]
     */
    private $lsidsBySession = [];

    /** @var Constraint */
    private static $isSupportedType;

    public function __destruct()
    {
        /* TODO: Determine if this is actually necessary. References to session
         * entities should not persist between tests.
         *
         * Note: This does not appear to trigger after a test due to cyclic
         * references (see comment in UnifiedSpecTest.php). */
        foreach ($this->map as $entity) {
            if ($entity->value instanceof Session) {
                $entity->value->endSession();
            }
        }
    }

    /**
     * @see https://php.net/arrayaccess.offsetexists
     */
    public function offsetExists($id): bool
    {
        assertIsString($id);

        return array_key_exists($id, $this->map);
    }

    /**
     * @see https://php.net/arrayaccess.offsetget
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($id)
    {
        assertIsString($id);
        assertArrayHasKey($id, $this->map, sprintf('No entity is defined for "%s"', $id));

        return $this->map[$id]->value;
    }

    /**
     * @see https://php.net/arrayaccess.offsetset
     */
    public function offsetSet($id, $value): void
    {
        Assert::fail('Entities can only be set via set()');
    }

    /**
     * @see https://php.net/arrayaccess.offsetunset
     */
    public function offsetUnset($id): void
    {
        Assert::fail('Entities cannot be removed from the map');
    }

    public function set(string $id, $value, ?string $parentId = null): void
    {
        assertArrayNotHasKey($id, $this->map, sprintf('Entity already exists for "%s" and cannot be replaced', $id));
        assertThat($value, self::isSupportedType());

        if ($value instanceof Session) {
            $this->lsidsBySession[$id] = $value->getLogicalSessionId();
        }

        $parent = $parentId === null ? null : $this->map[$parentId];

        $this->map[$id] = new class ($id, $value, $parent) {
            /** @var string */
            public $id;
            /** @var mixed */
            public $value;
            /** @var self */
            public $parent;

            public function __construct(string $id, $value, ?self $parent = null)
            {
                $this->id = $id;
                $this->value = $value;
                $this->parent = $parent;
            }

            public function getRoot(): self
            {
                $root = $this;

                while ($root->parent !== null) {
                    $root = $root->parent;
                }

                return $root;
            }
        };
    }

    /**
     * Closes a cursor by removing it from the entity map.
     *
     * @see Operation::executeForCursor()
     */
    public function closeCursor(string $cursorId): void
    {
        assertInstanceOf(Cursor::class, $this[$cursorId]);
        unset($this->map[$cursorId]);
    }

    public function getClient(string $clientId): Client
    {
        return $this[$clientId];
    }

    public function getCollection(string $collectionId): Collection
    {
        return $this[$collectionId];
    }

    public function getDatabase(string $databaseId): Database
    {
        return $this[$databaseId];
    }

    public function getSession(string $sessionId): Session
    {
        return $this[$sessionId];
    }

    public function getLogicalSessionId(string $sessionId): stdClass
    {
        return $this->lsidsBySession[$sessionId];
    }

    public function getRootClientIdOf(string $id)
    {
        $root = $this->map[$id]->getRoot();

        return $root->value instanceof Client ? $root->id : null;
    }

    private static function isSupportedType(): Constraint
    {
        if (self::$isSupportedType === null) {
            self::$isSupportedType = logicalOr(
                isInstanceOf(Client::class),
                isInstanceOf(ClientEncryption::class),
                isInstanceOf(Database::class),
                isInstanceOf(Collection::class),
                isInstanceOf(Session::class),
                isInstanceOf(Bucket::class),
                isInstanceOf(ChangeStream::class),
                isInstanceOf(Cursor::class),
                IsBsonType::any()
            );
        }

        return self::$isSupportedType;
    }
}
