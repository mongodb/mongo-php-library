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
use Symfony\Bridge\PhpUnit\ConstraintTrait;
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
use const PHP_INT_SIZE;

final class IsBsonType extends Constraint
{
    use ConstraintTrait;

    /** @var array */
    private static $types = [
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
    ];

    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        if (! in_array($type, self::$types)) {
            throw new RuntimeException(sprintf('Type specified for %s <%s> is not a valid type', self::class, $type));
        }

        $this->type = $type;
    }

    public static function any() : LogicalOr
    {
        return self::anyOf(...self::$types);
    }

    public static function anyOf(string ...$types) : Constraint
    {
        if (count($types) === 1) {
            return new self(...$types);
        }

        return LogicalOr::fromConstraints(...array_map(function ($type) {
            return new self($type);
        }, $types));
    }

    private function doMatches($other) : bool
    {
        switch ($this->type) {
            case 'double':
                return is_float($other);
            case 'string':
                return is_string($other);
            case 'object':
                return self::isObject($other);
            case 'array':
                return self::isArray($other);
            case 'binData':
                return $other instanceof BinaryInterface;
            case 'undefined':
                return $other instanceof Undefined;
            case 'objectId':
                return $other instanceof ObjectIdInterface;
            case 'bool':
                return is_bool($other);
            case 'date':
                return $other instanceof UTCDateTimeInterface;
            case 'null':
                return $other === null;
            case 'regex':
                return $other instanceof RegexInterface;
            case 'dbPointer':
                return $other instanceof DBPointer;
            case 'javascript':
                return $other instanceof JavascriptInterface && $other->getScope() === null;
            case 'symbol':
                return $other instanceof Symbol;
            case 'javascriptWithScope':
                return $other instanceof JavascriptInterface && $other->getScope() !== null;
            case 'int':
                return is_int($other);
            case 'timestamp':
                return $other instanceof TimestampInterface;
            case 'long':
                if (PHP_INT_SIZE == 4) {
                    return $other instanceof Int64;
                }

                return is_int($other);
            case 'decimal':
                return $other instanceof Decimal128Interface;
            case 'minKey':
                return $other instanceof MinKeyInterface;
            case 'maxKey':
                return $other instanceof MaxKeyInterface;
            default:
                // This should already have been caught in the constructor
                throw new LogicException('Unsupported type: ' . $this->type);
        }
    }

    private function doToString() : string
    {
        return sprintf('is of BSON type "%s"', $this->type);
    }

    private static function isArray($other) : bool
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

    private static function isObject($other) : bool
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

    private static function isArrayEmptyOrIndexed(array $a) : bool
    {
        if (empty($a)) {
            return true;
        }

        return array_keys($a) === range(0, count($a) - 1);
    }
}
