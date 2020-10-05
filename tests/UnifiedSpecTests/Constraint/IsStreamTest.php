<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use MongoDB\Tests\TestCase;
use function fopen;

class IsStreamTest extends TestCase
{
    public function testConstraint()
    {
        $c = new IsStream();

        $this->assertTrue($c->evaluate(fopen('php://temp', 'w+b'), '', true));
        $this->assertFalse($c->evaluate(1, '', true));
        $this->assertFalse($c->evaluate('foo', '', true));
    }
}
