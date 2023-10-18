<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder;

use Generator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Variable;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    /** @dataProvider provideVariableBuilders */
    public function testSystemVariables($factory): void
    {
        $variable = $factory();
        $this->assertInstanceOf(Expression\Variable::class, $variable);
        $this->assertStringStartsNotWith('$$', $variable->name);
    }

    public function provideVariableBuilders(): Generator
    {
        yield 'now' => [fn () => Variable::now()];
        yield 'clusterTime' => [fn () => Variable::clusterTime()];
        yield 'root' => [fn () => Variable::root()];
        yield 'current' => [fn () => Variable::current()];
        yield 'remove' => [fn () => Variable::remove()];
        yield 'descend' => [fn () => Variable::descend()];
        yield 'prune' => [fn () => Variable::prune()];
        yield 'keep' => [fn () => Variable::keep()];
        yield 'searchMeta' => [fn () => Variable::searchMeta()];
        yield 'userRoles' => [fn () => Variable::userRoles()];
    }

    public function testCurrent(): void
    {
        $variable = Variable::current();
        $this->assertInstanceOf(Expression\Variable::class, $variable);
        $this->assertSame('CURRENT', $variable->name);

        $variable = Variable::current('foo');
        $this->assertInstanceOf(Expression\Variable::class, $variable);
        $this->assertSame('CURRENT.foo', $variable->name);
    }

    public function testCustomVariable(): void
    {
        $this->assertInstanceOf(Expression\Variable::class, Variable::variable('foo'));
    }
}
