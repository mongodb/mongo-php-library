<?php

namespace MongoDB\Tests\PHPUnit;

use PHPUnit\Framework\Constraint\Constraint;
use ReflectionClass;
use const PHP_VERSION_ID;

$r = new ReflectionClass(Constraint::class);
if (PHP_VERSION_ID < 70000 || ! $r->getMethod('matches')->hasReturnType()) {
    trait ConstraintTrait
    {
        use ConstraintTraitForV6;
    }
} elseif (PHP_VERSION_ID < 70100 || ! $r->getMethod('evaluate')->hasReturnType()) {
    trait ConstraintTrait
    {
        use ConstraintTraitForV7;
    }
} else {
    trait ConstraintTrait
    {
        use ConstraintTraitForV9;
    }
}
