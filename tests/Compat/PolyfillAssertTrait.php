<?php

namespace MongoDB\Tests\Compat;

use PHPUnit\Framework\Assert;
use ReflectionClass;
use Symfony\Bridge\PhpUnit\Legacy\PolyfillAssertTrait as SymfonyPolyfillAssertTrait;

$r = new ReflectionClass(Assert::class);
if (! $r->hasMethod('assertEqualsWithDelta')) {
    /**
     * @internal
     */
    trait PolyfillAssertTrait
    {
        use SymfonyPolyfillAssertTrait;
    }
} else {
    /**
     * @internal
     */
    trait PolyfillAssertTrait
    {
    }
}
