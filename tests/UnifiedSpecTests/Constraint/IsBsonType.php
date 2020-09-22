<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use MongoDB\BSON\BinaryInterface;
use MongoDB\BSON\DBPointer;
use MongoDB\BSON\Decimal128Interface;
use MongoDB\BSON\Int64;
use MongoDB\BSON\JavascriptInterface;
use MongoDB\BSON\MaxKeyInterface;
use MongoDB\BSON\MinKeyInterface;
use MongoDB\BSON\ObjectIdInterface;
use MongoDB\BSON\RegexInterface;
use MongoDB\BSON\Symbol;
use MongoDB\BSON\TimestampInterface;
use MongoDB\BSON\Undefined;
use MongoDB\BSON\UTCDateTimeInterface;
use MongoDB\Model\BSONArray;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Bridge\PhpUnit\ConstraintTrait;
use RuntimeException;
use LogicException;

final class IsBsonType extends Constraint
{
    use ConstraintTrait;

    private static $knownTypes = [
        'double' => 1,
        'string' => 1,
        'object' => 1,
        'array' => 1,
        'binData' => 1,
        'undefined' => 1,
        'objectId' => 1,
        'bool' => 1,
        'date' => 1,
        'null' => 1,
        'regex' => 1,
        'dbPointer' => 1,
        'javascript' => 1,
        'symbol' => 1,
        'javascriptWithScope' => 1,
        'int' => 1,
        'timestamp' => 1,
        'long' => 1,
        'decimal' => 1,
        'minKey' => 1,
        'maxKey' => 1,
    ];

    private $type;

    public function __construct(string $type)
    {
        if (! isset(self::$knownTypes[$type])) {
            throw new RuntimeException(sprintf('Type specified for %s <%s> is not a valid type', self::class, $type));
        }

        $this->type = $type;
    }

    private function doMatches($other): bool
    {
        switch ($this->type) {
            case 'double':
                return is_float($other);

            case 'string':
                return is_string($other);

            case 'object':
                return is_object($other) && (! $other instanceof BSONArray);

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

    private function doToString(): string
    {
        return sprintf('is of BSON type "%s"', $this->type);
    }

    private static function isArray($other): bool
    {
        if ($other instanceof BSONArray) {
            return true;
        }

        if (! is_array($other)) {
            return false;
        }

        if (empty($other)) {
            return true;
        }

        return array_keys($other) === range(0, count($other) - 1);
    }
}
