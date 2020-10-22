<?php

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

if (! function_exists('assertIsArray')) {
    /**
     * Asserts that a variable is of type array.
     *
     * @see Assert::assertIsArray
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert array $actual
     */
    function assertIsArray($actual, string $message = '')
    {
        Assert::assertIsArray(...func_get_args());
    }
}

if (! function_exists('assertIsBool')) {
    /**
     * Asserts that a variable is of type bool.
     *
     * @see Assert::assertIsBool
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert bool $actual
     */
    function assertIsBool($actual, string $message = '')
    {
        Assert::assertIsBool(...func_get_args());
    }
}

if (! function_exists('assertIsFloat')) {
    /**
     * Asserts that a variable is of type float.
     *
     * @see Assert::assertIsFloat
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert float $actual
     */
    function assertIsFloat($actual, string $message = '')
    {
        Assert::assertIsFloat(...func_get_args());
    }
}

if (! function_exists('assertIsInt')) {
    /**
     * Asserts that a variable is of type int.
     *
     * @see Assert::assertIsInt
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert int $actual
     */
    function assertIsInt($actual, string $message = '')
    {
        Assert::assertIsInt(...func_get_args());
    }
}

if (! function_exists('assertIsNumeric')) {
    /**
     * Asserts that a variable is of type numeric.
     *
     * @see Assert::assertIsNumeric
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert numeric $actual
     */
    function assertIsNumeric($actual, string $message = '')
    {
        Assert::assertIsNumeric(...func_get_args());
    }
}

if (! function_exists('assertIsObject')) {
    /**
     * Asserts that a variable is of type object.
     *
     * @see Assert::assertIsObject
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert object $actual
     */
    function assertIsObject($actual, string $message = '')
    {
        Assert::assertIsObject(...func_get_args());
    }
}

if (! function_exists('assertIsResource')) {
    /**
     * Asserts that a variable is of type resource.
     *
     * @see Assert::assertIsResource
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert resource $actual
     */
    function assertIsResource($actual, string $message = '')
    {
        Assert::assertIsResource(...func_get_args());
    }
}

if (! function_exists('assertIsString')) {
    /**
     * Asserts that a variable is of type string.
     *
     * @see Assert::assertIsString
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert string $actual
     */
    function assertIsString($actual, string $message = '')
    {
        Assert::assertIsString(...func_get_args());
    }
}

if (! function_exists('assertIsScalar')) {
    /**
     * Asserts that a variable is of type scalar.
     *
     * @see Assert::assertIsScalar
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert scalar $actual
     */
    function assertIsScalar($actual, string $message = '')
    {
        Assert::assertIsScalar(...func_get_args());
    }
}

if (! function_exists('assertIsCallable')) {
    /**
     * Asserts that a variable is of type callable.
     *
     * @see Assert::assertIsCallable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert callable $actual
     */
    function assertIsCallable($actual, string $message = '')
    {
        Assert::assertIsCallable(...func_get_args());
    }
}

if (! function_exists('assertIsIterable')) {
    /**
     * Asserts that a variable is of type iterable.
     *
     * @see Assert::assertIsIterable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert iterable $actual
     */
    function assertIsIterable($actual, string $message = '')
    {
        Assert::assertIsIterable(...func_get_args());
    }
}
