<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder;

use MongoDB\Builder\Expression\ArrayFieldPath;
use MongoDB\Builder\Expression\BinaryFieldPath;
use MongoDB\Builder\Expression\BoolFieldPath;
use MongoDB\Builder\Expression\DateFieldPath;
use MongoDB\Builder\Expression\DecimalFieldPath;
use MongoDB\Builder\Expression\DoubleFieldPath;
use MongoDB\Builder\Expression\FieldName;
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\FloatFieldPath;
use MongoDB\Builder\Expression\IntFieldPath;
use MongoDB\Builder\Expression\Literal;
use MongoDB\Builder\Expression\LongFieldPath;
use MongoDB\Builder\Expression\NullFieldPath;
use MongoDB\Builder\Expression\NumberFieldPath;
use MongoDB\Builder\Expression\ObjectFieldPath;
use MongoDB\Builder\Expression\ObjectIdFieldPath;
use MongoDB\Builder\Expression\StringFieldPath;
use MongoDB\Builder\Expression\TimestampFieldPath;
use MongoDB\Builder\Expression\Variable;

final class Expression
{
    public static function arrayFieldPath(string $expression): ArrayFieldPath
    {
        return new ArrayFieldPath($expression);
    }

    public static function binaryFieldPath(string $expression): BinaryFieldPath
    {
        return new BinaryFieldPath($expression);
    }

    public static function boolFieldPath(string $expression): BoolFieldPath
    {
        return new BoolFieldPath($expression);
    }

    public static function dateFieldPath(string $expression): DateFieldPath
    {
        return new DateFieldPath($expression);
    }

    public static function decimalFieldPath(string $expression): DecimalFieldPath
    {
        return new DecimalFieldPath($expression);
    }

    public static function doubleFieldPath(string $expression): DoubleFieldPath
    {
        return new DoubleFieldPath($expression);
    }

    public static function fieldName(string $expression): FieldName
    {
        return new FieldName($expression);
    }

    public static function fieldPath(string $expression): FieldPath
    {
        return new FieldPath($expression);
    }

    public static function floatFieldPath(string $expression): FloatFieldPath
    {
        return new FloatFieldPath($expression);
    }

    public static function intFieldPath(string $expression): IntFieldPath
    {
        return new IntFieldPath($expression);
    }

    public static function literal(string $expression): Literal
    {
        return new Literal($expression);
    }

    public static function longFieldPath(string $expression): LongFieldPath
    {
        return new LongFieldPath($expression);
    }

    public static function nullFieldPath(string $expression): NullFieldPath
    {
        return new NullFieldPath($expression);
    }

    public static function numberFieldPath(string $expression): NumberFieldPath
    {
        return new NumberFieldPath($expression);
    }

    public static function objectFieldPath(string $expression): ObjectFieldPath
    {
        return new ObjectFieldPath($expression);
    }

    public static function objectIdFieldPath(string $expression): ObjectIdFieldPath
    {
        return new ObjectIdFieldPath($expression);
    }

    public static function stringFieldPath(string $expression): StringFieldPath
    {
        return new StringFieldPath($expression);
    }

    public static function timestampFieldPath(string $expression): TimestampFieldPath
    {
        return new TimestampFieldPath($expression);
    }

    public static function variable(string $expression): Variable
    {
        return new Variable($expression);
    }

    /**
     * This class cannot be instantiated.
     */
    private function __construct()
    {
    }
}
