<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Javascript;
use MongoDB\BSON\MaxKey;
use MongoDB\BSON\MinKey;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\TestCase;
use PHPUnit\Framework\ExpectationFailedException;

use function MongoDB\BSON\fromJSON;
use function MongoDB\BSON\toPHP;
use function unserialize;

use const PHP_INT_SIZE;

class DocumentsMatchConstraintTest extends TestCase
{
    public function testIgnoreExtraKeysInRoot(): void
    {
        $c = new DocumentsMatchConstraint(['x' => 1, 'y' => ['a' => 1, 'b' => 2]], true, false);

        $this->assertResult(false, $c, ['x' => 1, 'y' => 2], 'Incorrect value');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2]], 'Exact match');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2], 'z' => 3], 'Extra keys in root are permitted');
        $this->assertResult(false, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2, 'c' => 3]], 'Extra keys in embedded are not permitted');
        $this->assertResult(true, $c, ['y' => ['b' => 2, 'a' => 1], 'x' => 1], 'Root and embedded key order is not significant');

        // Arrays are always interpreted as root documents
        $c = new DocumentsMatchConstraint([1, ['a' => 1]], true, false);

        $this->assertResult(false, $c, [1, 2], 'Incorrect value');
        $this->assertResult(true, $c, [1, ['a' => 1]], 'Exact match');
        $this->assertResult(true, $c, [1, ['a' => 1], 3], 'Extra keys in root are permitted');
        $this->assertResult(false, $c, [1, ['a' => 1, 'b' => 2]], 'Extra keys in embedded are not permitted');
    }

    public function testFlexibleNumericComparison(): void
    {
        $c = new DocumentsMatchConstraint(['x' => 1, 'y' => 1.0]);
        $this->assertResult(true, $c, ['x' => 1.0, 'y' => 1.0], 'Float instead of expected int matches');
        $this->assertResult(true, $c, ['x' => 1, 'y' => 1], 'Int instead of expected float matches');
        $this->assertResult(false, $c, ['x' => 'foo', 'y' => 1.0], 'Different type does not match');
    }

    public function testIgnoreExtraKeysInEmbedded(): void
    {
        $c = new DocumentsMatchConstraint(['x' => 1, 'y' => ['a' => 1, 'b' => 2]], false, true);

        $this->assertResult(false, $c, ['x' => 1, 'y' => 2], 'Incorrect value');
        $this->assertResult(false, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 3]], 'Incorrect value');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2]], 'Exact match');
        $this->assertResult(false, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2], 'z' => 3], 'Extra keys in root are not permitted');
        $this->assertResult(true, $c, ['x' => 1, 'y' => ['a' => 1, 'b' => 2, 'c' => 3]], 'Extra keys in embedded are permitted');
        $this->assertResult(true, $c, ['y' => ['b' => 2, 'a' => 1], 'x' => 1], 'Root and embedded Key order is not significant');

        // Arrays are always interpreted as root documents
        $c = new DocumentsMatchConstraint([1, ['a' => 1]], false, true);

        $this->assertResult(false, $c, [1, 2], 'Incorrect value');
        $this->assertResult(true, $c, [1, ['a' => 1]], 'Exact match');
        $this->assertResult(false, $c, [1, ['a' => 1], 3], 'Extra keys in root are not permitted');
        $this->assertResult(true, $c, [1, ['a' => 1, 'b' => 2]], 'Extra keys in embedded are permitted');
        $this->assertResult(false, $c, [1, ['a' => 2]], 'Keys must have the correct value');
    }

    /**
     * @dataProvider provideBSONTypes
     */
    public function testBSONTypeAssertions($type, $value): void
    {
        $constraint = new DocumentsMatchConstraint(['x' => ['$$type' => $type]]);

        $this->assertResult(true, $constraint, ['x' => $value], 'Type matches');
    }

    public function provideBSONTypes()
    {
        $undefined = toPHP(fromJSON('{ "x": {"$undefined": true} }'))->x;
        $symbol = toPHP(fromJSON('{ "x": {"$symbol": "test"} }'))->x;
        $dbPointer = toPHP(fromJSON('{ "x": {"$dbPointer": {"$ref": "db.coll", "$id" : { "$oid" : "5a2e78accd485d55b405ac12" }  }} }'))->x;
        $int64 = unserialize('C:18:"MongoDB\BSON\Int64":28:{a:1:{s:7:"integer";s:1:"1";}}');
        $long = PHP_INT_SIZE == 4 ? unserialize('C:18:"MongoDB\BSON\Int64":38:{a:1:{s:7:"integer";s:10:"4294967296";}}') : 4294967296;

        return [
            'double' => ['double', 1.4],
            'string' => ['string', 'foo'],
            'object' => ['object', new BSONDocument()],
            'array' => ['array', ['foo']],
            'binData' => ['binData', new Binary('', 0)],
            'undefined' => ['undefined', $undefined],
            'objectId' => ['objectId', new ObjectId()],
            'bool' => ['bool', true],
            'date' => ['date', new UTCDateTime()],
            'null' => ['null', null],
            'regex' => ['regex', new Regex('.*')],
            'dbPointer' => ['dbPointer', $dbPointer],
            'javascript' => ['javascript', new Javascript('foo = 1;')],
            'symbol' => ['symbol', $symbol],
            'int' => ['int', 1],
            'timestamp' => ['timestamp', new Timestamp(0, 0)],
            'long(int64)' => ['long', $int64],
            'long(long)' => ['long', $long],
            'decimal' => ['decimal', new Decimal128('18446744073709551616')],
            'minKey' => ['minKey', new MinKey()],
            'maxKey' => ['maxKey', new MaxKey()],
            'number(double)' => ['number', 1.4],
            'number(decimal)' => ['number', new Decimal128('18446744073709551616')],
            'number(int)' => ['number', 1],
            'number(int64)' => ['number', $int64],
            'number(long)' => ['number', $long],
        ];
    }

    public function testBSONTypeAssertionsWithMultipleTypes(): void
    {
        $c1 = new DocumentsMatchConstraint(['x' => ['$$type' => ['double', 'int']]]);

        $this->assertResult(true, $c1, ['x' => 1], 'int is double or int');
        $this->assertResult(true, $c1, ['x' => 1.4], 'double is double or int');
        $this->assertResult(false, $c1, ['x' => 'foo'], 'string is not double or int');

        $c2 = new DocumentsMatchConstraint(['x' => ['$$type' => ['number', 'string']]]);

        $this->assertResult(true, $c2, ['x' => 1], 'int is number or string');
        $this->assertResult(true, $c2, ['x' => 1.4], 'double is number or string');
        $this->assertResult(true, $c2, ['x' => 'foo'], 'string is number or string');
        $this->assertResult(false, $c2, ['x' => true], 'bool is not number or string');
    }

    /**
     * @dataProvider errorMessageProvider
     */
    public function testErrorMessages($expectedMessagePart, DocumentsMatchConstraint $constraint, $actualValue): void
    {
        try {
            $constraint->evaluate($actualValue);
            $this->fail('Expected a comparison failure');
        } catch (ExpectationFailedException $failure) {
            $this->assertStringContainsString('Failed asserting that two BSON objects are equal.', $failure->getMessage());
            $this->assertStringContainsString($expectedMessagePart, $failure->getMessage());
        }
    }

    public function errorMessageProvider()
    {
        return [
            'Root type mismatch' => [
                'MongoDB\Model\BSONArray Object (...) is not instance of expected class "MongoDB\Model\BSONDocument"',
                new DocumentsMatchConstraint(['foo' => 'bar']),
                new BSONArray(['foo' => 'bar']),
            ],
            'Missing key' => [
                '$actual is missing key: "foo.bar"',
                new DocumentsMatchConstraint(['foo' => ['bar' => 'baz']]),
                ['foo' => ['foo' => 'bar']],
            ],
            'Extra key' => [
                '$actual has extra key: "foo.foo"',
                new DocumentsMatchConstraint(['foo' => ['bar' => 'baz']]),
                ['foo' => ['foo' => 'bar', 'bar' => 'baz']],
            ],
            'Scalar value not equal' => [
                'Field path "foo": Failed asserting that two strings are equal.',
                new DocumentsMatchConstraint(['foo' => 'bar']),
                ['foo' => 'baz'],
            ],
            'Scalar type mismatch' => [
                'Field path "foo": \'42\' is not instance of expected type "bool".',
                new DocumentsMatchConstraint(['foo' => true]),
                ['foo' => '42'],
            ],
            'Type mismatch' => [
                'Field path "foo": MongoDB\Model\BSONDocument Object (...) is not instance of expected type "MongoDB\Model\BSONArray".',
                new DocumentsMatchConstraint(['foo' => ['bar']]),
                ['foo' => (object) ['bar']],
            ],
        ];
    }

    private function assertResult($expectedResult, DocumentsMatchConstraint $constraint, $value, $message): void
    {
        $this->assertSame($expectedResult, $constraint->evaluate($value, '', true), $message);
    }
}
