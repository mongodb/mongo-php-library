<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

/**
 * @internal
 */
trait ExpressionFactoryTrait
{
    public static function arrayFieldPath(string $name): ArrayFieldPath
    {
        return new ArrayFieldPath($name);
    }

    public static function binDataFieldPath(string $name): BinDataFieldPath
    {
        return new BinDataFieldPath($name);
    }

    public static function boolFieldPath(string $name): BoolFieldPath
    {
        return new BoolFieldPath($name);
    }

    public static function dateFieldPath(string $name): DateFieldPath
    {
        return new DateFieldPath($name);
    }

    public static function decimalFieldPath(string $name): DecimalFieldPath
    {
        return new DecimalFieldPath($name);
    }

    public static function doubleFieldPath(string $name): DoubleFieldPath
    {
        return new DoubleFieldPath($name);
    }

    public static function fieldPath(string $name): FieldPath
    {
        return new FieldPath($name);
    }

    public static function intFieldPath(string $name): IntFieldPath
    {
        return new IntFieldPath($name);
    }

    public static function javascriptFieldPath(string $name): JavascriptFieldPath
    {
        return new JavascriptFieldPath($name);
    }

    public static function longFieldPath(string $name): LongFieldPath
    {
        return new LongFieldPath($name);
    }

    public static function nullFieldPath(string $name): NullFieldPath
    {
        return new NullFieldPath($name);
    }

    public static function numberFieldPath(string $name): NumberFieldPath
    {
        return new NumberFieldPath($name);
    }

    public static function objectFieldPath(string $name): ObjectFieldPath
    {
        return new ObjectFieldPath($name);
    }

    public static function objectIdFieldPath(string $name): ObjectIdFieldPath
    {
        return new ObjectIdFieldPath($name);
    }

    public static function regexFieldPath(string $name): RegexFieldPath
    {
        return new RegexFieldPath($name);
    }

    public static function stringFieldPath(string $name): StringFieldPath
    {
        return new StringFieldPath($name);
    }

    public static function timestampFieldPath(string $name): TimestampFieldPath
    {
        return new TimestampFieldPath($name);
    }

    public static function variable(string $name): Variable
    {
        return new Variable($name);
    }
}
