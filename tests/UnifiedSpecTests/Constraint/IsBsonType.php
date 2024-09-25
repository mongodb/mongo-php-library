<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use LogicException;
use MongoDB\BSON\BinaryInterface;
use MongoDB\BSON\DBPointer;
use MongoDB\BSON\Decimal128Interface;
use MongoDB\BSON\Int64;
use MongoDB\BSON\JavascriptInterface;
use MongoDB\BSON\MaxKeyInterface;
use MongoDB\BSON\MinKeyInterface;
use MongoDB\BSON\ObjectIdInterface;
use MongoDB\BSON\RegexInterface;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Symbol;
use MongoDB\BSON\TimestampInterface;
use MongoDB\BSON\Type;
use MongoDB\BSON\Undefined;
use MongoDB\BSON\UTCDateTimeInterface;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\LogicalOr;
use RuntimeException;

use function array_keys;
use function array_map;
use function count;
use function in_array;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function range;
use function sprintf;

final class IsBsonType extends Constraint
{
    private static array $types = [
        'double',
        'string',
        'object',
        'array',
        'binData',
        'undefined',
        'objectId',
        'bool',
        'date',
        'null',
        'regex',
        'dbPointer',
        'javascript',
        'symbol',
        'javascriptWithScope',
        'int',
        'timestamp',
        'long',
        'decimal',
        'minKey',
        'maxKey',
        'number',
    ];

    public function __construct(private string $type)
    {
        if (! in_array($type, self::$types)) {
            throw new RuntimeException(sprintf('Type specified for %s <%s> is not a valid type', self::class, $type));
        }
    }

    public static function any(): LogicalOr
    {
        return self::anyOf(...self::$types);
    }

    public static function anyOf(string ...$types): Constraint
    {
        if (count($types) === 1) {
            return new self(...$types);
        }

        return LogicalOr::fromConstraints(...array_map(fn ($type) => new self($type), $types));
    }

    protected function matches($other): bool
    {
        return match ($this->type) {
            'double' => is_float($other),
            'string' => is_string($other),
            'object' => self::isObject($other),
            'array' => self::isArray($other),
            'binData' => $other instanceof BinaryInterface,
            'undefined' => $other instanceof Undefined,
            'objectId' => $other instanceof ObjectIdInterface,
            'bool' => is_bool($other),
            'date' => $other instanceof UTCDateTimeInterface,
            'null' => $other === null,
            'regex' => $other instanceof RegexInterface,
            'dbPointer' => $other instanceof DBPointer,
            'javascript' => $other instanceof JavascriptInterface && $other->getScope() === null,
            'symbol' => $other instanceof Symbol,
            'javascriptWithScope' => $other instanceof JavascriptInterface && $other->getScope() !== null,
            'int' => is_int($other),
            'timestamp' => $other instanceof TimestampInterface,
            'long' => is_int($other) || $other instanceof Int64,
            'decimal' => $other instanceof Decimal128Interface,
            'minKey' => $other instanceof MinKeyInterface,
            'maxKey' => $other instanceof MaxKeyInterface,
            'number' => is_int($other) || $other instanceof Int64 || is_float($other) || $other instanceof Decimal128Interface,
            // This should already have been caught in the constructor
            default => throw new LogicException('Unsupported type: ' . $this->type),
        };
    }

    public function toString(): string
    {
        return sprintf('is of BSON type "%s"', $this->type);
    }

    private static function isArray($other): bool
    {
        if ($other instanceof BSONArray) {
            return true;
        }

        // Serializable can produce an array or object, so recurse on its output
        if ($other instanceof Serializable) {
            return self::isArray($other->bsonSerialize());
        }

        if (! is_array($other)) {
            return false;
        }

        // Empty and indexed arrays serialize as BSON arrays
        return self::isArrayEmptyOrIndexed($other);
    }

    private static function isObject($other): bool
    {
        if ($other instanceof BSONDocument) {
            return true;
        }

        // Serializable can produce an array or object, so recurse on its output
        if ($other instanceof Serializable) {
            return self::isObject($other->bsonSerialize());
        }

        // Non-empty, associative arrays serialize as BSON objects
        if (is_array($other)) {
            return ! self::isArrayEmptyOrIndexed($other);
        }

        if (! is_object($other)) {
            return false;
        }

        /* Serializable has already been handled, so any remaining instances of
         * Type will not serialize as BSON objects */
        return ! $other instanceof Type;
    }

    private static function isArrayEmptyOrIndexed(array $a): bool
    {
        if (empty($a)) {
            return true;
        }

        return array_keys($a) === range(0, count($a) - 1);
    }
}
