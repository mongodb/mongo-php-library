<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use MongoDB\Model\BSONArray;
use MongoDB\Tests\TestCase;
use PHPUnit\Framework\ExpectationFailedException;

class MatchTest extends TestCase
{
    public function testIgnoreExtraKeysInRoot()
    {
        $c = new Match(['x' => 1, 'y' => ['a' => 1, 'b' => 2]], true, false);

        $this->assertResult(false, $c, ['x' => 1, 'y' => 2], 'Incorrect value');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2]], 'Exact match');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2], 'z' => 3], 'Extra keys in root are permitted');
        $this->assertResult(false, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2, 'c' => 3]], 'Extra keys in embedded are not permitted');
        $this->assertResult(true, $c, ['y' => ['b' => 2, 'a' => 1], 'x' => 1], 'Root and embedded key order is not significant');

        // Arrays are always interpreted as root documents
        $c = new Match([1, ['a' => 1]], true, false);

        $this->assertResult(false, $c, [1, 2], 'Incorrect value');
        $this->assertResult(true, $c, [1, ['a' => 1]], 'Exact match');
        $this->assertResult(true, $c, [1, ['a' => 1], 3], 'Extra keys in root are permitted');
        $this->assertResult(false, $c, [1, ['a' => 1, 'b' => 2]], 'Extra keys in embedded are not permitted');
    }

    public function testIgnoreExtraKeysInEmbedded()
    {
        $c = new Match(['x' => 1, 'y' => ['a' => 1, 'b' => 2]], false, true);

        $this->assertResult(false, $c, ['x' => 1, 'y' => 2], 'Incorrect value');
        $this->assertResult(false, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 3]], 'Incorrect value');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2]], 'Exact match');
        $this->assertResult(false, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2], 'z' => 3], 'Extra keys in root are not permitted');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2, 'c' => 3]], 'Extra keys in embedded are permitted');
        $this->assertResult(true, $c, ['y' => ['b' => 2, 'a' => 1], 'x' => 1], 'Root and embedded Key order is not significant');

        // Arrays are always interpreted as root documents
        $c = new Match([1, ['a' => 1]], false, true);

        $this->assertResult(false, $c, [1, 2], 'Incorrect value');
        $this->assertResult(true, $c, [1, ['a' => 1]], 'Exact match');
        $this->assertResult(false, $c, [1, ['a' => 1], 3], 'Extra keys in root are not permitted');
        $this->assertResult(true, $c, [1, ['a' => 1, 'b' => 2]], 'Extra keys in embedded are permitted');
        $this->assertResult(false, $c, [1, ['a' => 2]], 'Keys must have the correct value');
    }

    public function testSpecialOperatorExists()
    {
        $c = new Match(['x' => ['$$exists' => true]]);
        $this->assertResult(true, $c, ['x' => '1'], 'top-level $$exists:true and field exists');
        $this->assertResult(false, $c, [], 'top-level $$exists:true and field missing');

        $c = new Match(['x' => ['$$exists' => false]]);
        $this->assertResult(false, $c, ['x' => '1'], 'top-level $$exists:false and field exists');
        $this->assertResult(true, $c, [], 'top-level $$exists:false and field missing');

        $c = new Match(['x' => ['y' => ['$$exists' => true]]]);
        $this->assertResult(true, $c, ['x' => ['y' => '1']], 'nested $$exists:true and field exists');
        $this->assertResult(false, $c, ['x' => (object) []], 'nested $$exists:true and field missing');

        $c = new Match(['x' => ['y' => ['$$exists' => false]]]);
        $this->assertResult(false, $c, ['x' => ['y' => 1]], 'nested $$exists:false and field exists');
        $this->assertResult(true, $c, ['x' => (object) []], 'nested $$exists:false and field missing');
    }

    public function testSpecialOperatorType()
    {
        $c = new Match(['x' => ['$$type' => 'string']]);

        $this->assertResult(true, $c, ['x' => 'foo'], '$$type:string matches string');
        $this->assertResult(false, $c, ['x' => 1], '$$type:string does not match int');

        $c = new Match(['x' => ['$$type' => ['string', 'bool']]]);

        $this->assertResult(true, $c, ['x' => 'foo'], '$$type:[string,bool] matches string');
        $this->assertResult(true, $c, ['x' => true], '$$type:[string,bool] matches bool');
        $this->assertResult(false, $c, ['x' => 1], '$$type:[string,bool] does not match int');
    }

    /**
     * @dataProvider errorMessageProvider
     */
    public function testErrorMessages($expectedMessagePart, Match $constraint, $actualValue)
    {
        try {
            $constraint->evaluate($actualValue);
            $this->fail('Expected a comparison failure');
        } catch (ExpectationFailedException $failure) {
            $this->assertStringContainsString('Failed asserting that expected value matches actual value.', $failure->getMessage());
            $this->assertStringContainsString($expectedMessagePart, $failure->getMessage());
        }
    }

    public function errorMessageProvider()
    {
        return [
            'Root type mismatch' => [
                'MongoDB\Model\BSONArray Object (...) is not instance of expected class "MongoDB\Model\BSONDocument"',
                new Match(['foo' => 'bar']),
                new BSONArray(['foo' => 'bar']),
            ],
            'Missing key' => [
                'Field path "foo": $actual does not have expected key "bar"',
                new Match(['foo' => ['bar' => 'baz']]),
                ['foo' => ['foo' => 'bar']],
            ],
            'Extra key' => [
                'Field path "foo": $actual has extra key "foo"',
                new Match(['foo' => ['bar' => 'baz']]),
                ['foo' => ['foo' => 'bar', 'bar' => 'baz']],
            ],
            'Scalar value not equal' => [
                'Field path "foo": Failed asserting that two strings are equal.',
                new Match(['foo' => 'bar']),
                ['foo' => 'baz'],
            ],
            'Scalar type mismatch' => [
                'Field path "foo": \'42\' is not instance of expected type "integer".',
                new Match(['foo' => 42]),
                ['foo' => '42'],
            ],
            'Type mismatch' => [
                'Field path "foo": MongoDB\Model\BSONDocument Object (...) is not instance of expected class "MongoDB\Model\BSONArray"',
                new Match(['foo' => ['bar']]),
                ['foo' => (object) ['bar']],
            ],
        ];
    }

    private function assertResult($expectedResult, Match $constraint, $value, $message)
    {
        $this->assertSame($expectedResult, $constraint->evaluate($value, '', true), $message);
    }
}
