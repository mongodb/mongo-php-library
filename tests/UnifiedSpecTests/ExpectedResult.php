<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\BulkWriteResult;
use MongoDB\DeleteResult;
use MongoDB\Driver\WriteResult;
use MongoDB\InsertManyResult;
use MongoDB\InsertOneResult;
use MongoDB\Tests\UnifiedSpecTests\Constraint\Matches;
use MongoDB\UpdateResult;
use stdClass;
use function is_object;
use function PHPUnit\Framework\assertThat;
use function property_exists;

final class ExpectedResult
{
    /** @var Matches */
    private $constraint;

    /** @var EntityMap */
    private $entityMap;

    /**
     * ID of the entity yielding the result. This is mainly used to associate
     * entities with a root client for collation of observed events.
     *
     * @var ?string
     */
    private $yieldingEntityId;

    public function __construct(stdClass $o, EntityMap $entityMap, string $yieldingEntityId = null)
    {
        if (property_exists($o, 'expectResult')) {
            $this->constraint = new Matches($o->expectResult, $entityMap);
        }

        $this->entityMap = $entityMap;
        $this->yieldingEntityId = $yieldingEntityId;
    }

    public function assert($actual, string $saveResultAsEntity = null)
    {
        if ($this->constraint === null && $saveResultAsEntity === null) {
            return;
        }

        $actual = self::prepare($actual);

        if ($this->constraint !== null) {
            assertThat($actual, $this->constraint);
        }

        if ($saveResultAsEntity !== null) {
            $this->entityMap->set($saveResultAsEntity, $actual, $this->yieldingEntityId);
        }
    }

    private static function prepare($value)
    {
        if (! is_object($value)) {
            return $value;
        }

        if ($value instanceof BulkWriteResult ||
            $value instanceof WriteResult ||
            $value instanceof DeleteResult ||
            $value instanceof InsertOneResult ||
            $value instanceof InsertManyResult ||
            $value instanceof UpdateResult) {
            return self::prepareWriteResult($value);
        }

        return $value;
    }

    private static function prepareWriteResult($value)
    {
        $result = ['acknowledged' => $value->isAcknowledged()];

        if (! $result['acknowledged']) {
            return $result;
        }

        if ($value instanceof BulkWriteResult || $value instanceof WriteResult) {
            $result['deletedCount'] = $value->getDeletedCount();
            $result['insertedCount'] = $value->getInsertedCount();
            $result['matchedCount'] = $value->getMatchedCount();
            $result['modifiedCount'] = $value->getModifiedCount();
            $result['upsertedCount'] = $value->getUpsertedCount();
            $result['upsertedIds'] = (object) $value->getUpsertedIds();
        }

        // WriteResult does not provide insertedIds (see: PHPLIB-428)
        if ($value instanceof BulkWriteResult) {
            $result['insertedIds'] = (object) $value->getInsertedIds();
        }

        if ($value instanceof DeleteResult) {
            $result['deletedCount'] = $value->getDeletedCount();
        }

        if ($value instanceof InsertManyResult) {
            $result['insertedCount'] = $value->getInsertedCount();
            $result['insertedIds'] = (object) $value->getInsertedIds();
        }

        if ($value instanceof InsertOneResult) {
            $result['insertedCount'] = $value->getInsertedCount();
            $result['insertedId'] = $value->getInsertedId();
        }

        if ($value instanceof UpdateResult) {
            $result['matchedCount'] = $value->getMatchedCount();
            $result['modifiedCount'] = $value->getModifiedCount();
            $result['upsertedCount'] = $value->getUpsertedCount();
            $result['upsertedId'] = $value->getUpsertedId();
        }

        return $result;
    }
}
