<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Javascript;
use MongoDB\BSON\MaxKey;
use MongoDB\BSON\MinKey;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Timestamp;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\TestCase;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use stdClass;

use function fopen;
use function MongoDB\BSON\fromJSON;
use function MongoDB\BSON\toPHP;
use function unserialize;

use const PHP_INT_SIZE;

class IsBsonTypeTest extends TestCase
{
    /**
     * @dataProvider provideTypes
     */
    public function testConstraint($type, $value): void
    {
        $this->assertResult(true, new IsBsonType($type), $value, $this->dataName() . ' is ' . $type);
    }

    public function provideTypes()
    {
        $undefined = toPHP(fromJSON('{ "x": {"$undefined": true} }'))->x;
        $symbol = toPHP(fromJSON('{ "x": {"$symbol": "test"} }'))->x;
        $dbPointer = toPHP(fromJSON('{ "x": {"$dbPointer": {"$ref": "db.coll", "$id" : { "$oid" : "5a2e78accd485d55b405ac12" }  }} }'))->x;
        $int64 = unserialize('C:18:"MongoDB\BSON\Int64":28:{a:1:{s:7:"integer";s:1:"1";}}');
        $long = PHP_INT_SIZE == 4 ? unserialize('C:18:"MongoDB\BSON\Int64":38:{a:1:{s:7:"integer";s:10:"4294967296";}}') : 4294967296;

        return [
            'double' => ['double', 1.4],
            'string' => ['string', 'foo'],
            // Note: additional tests in testTypeObject
            'object(stdClass)' => ['object', new stdClass()],
            'object(BSONDocument)' => ['object', new BSONDocument()],
            // Note: additional tests tests in testTypeArray
            'array(indexed array)' => ['array', ['foo']],
            'array(BSONArray)' => ['array', new BSONArray()],
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
            'javascriptWithScope' => ['javascriptWithScope', new Javascript('foo = 1;', ['x' => 1])],
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

    /**
     * @dataProvider provideTypes
     */
    public function testAny($type, $value): void
    {
        $this->assertResult(true, IsBsonType::any(), $value, $this->dataName() . ' is a BSON type');
    }

    public function testAnyExcludesStream(): void
    {
        $this->assertResult(false, IsBsonType::any(), fopen('php://temp', 'w+b'), 'stream is not a BSON type');
    }

    public function testAnyOf(): void
    {
        $c = IsBsonType::anyOf('double', 'int');

        $this->assertResult(true, $c, 1, 'int is double or int');
        $this->assertResult(true, $c, 1.4, 'double is double or int');
        $this->assertResult(false, $c, 'foo', 'string is not double or int');
    }

    public function testAnyOfWithNumberAlias(): void
    {
        $c = IsBsonType::anyOf('number', 'string');

        $this->assertResult(true, $c, 1, 'int is number or string');
        $this->assertResult(true, $c, 1.4, 'double is number or string');
        $this->assertResult(true, $c, 'foo', 'string is number or string');
        $this->assertResult(false, $c, true, 'bool is not number or string');
    }

    public function testErrorMessage(): void
    {
        $c = new IsBsonType('string');

        try {
            $c->evaluate(1);
            $this->fail('Expected a comparison failure');
        } catch (ExpectationFailedException $e) {
            $this->assertStringMatchesFormat('Failed asserting that %s is of BSON type "string".', $e->getMessage());
        }
    }

    public function testTypeArray(): void
    {
        $c = new IsBsonType('array');

        $this->assertResult(true, $c, [], 'empty array is array');
        $this->assertResult(true, $c, ['foo'], 'indexed array is array');
        $this->assertResult(true, $c, new BSONArray(), 'BSONArray is array');
        $this->assertResult(true, $c, new SerializableArray(), 'SerializableArray is array');

        $this->assertResult(false, $c, 1, 'integer is not array');
        $this->assertResult(false, $c, ['x' => 1], 'associative array is not array');
        $this->assertResult(false, $c, new BSONDocument(), 'BSONDocument is not array');
        $this->assertResult(false, $c, new SerializableObject(), 'SerializableObject is not array');
    }

    public function testTypeObject(): void
    {
        $c = new IsBsonType('object');

        $this->assertResult(true, $c, new stdClass(), 'stdClass is object');
        $this->assertResult(true, $c, new BSONDocument(), 'BSONDocument is object');
        $this->assertResult(true, $c, ['x' => 1], 'associative array is object');
        $this->assertResult(true, $c, new SerializableObject(), 'SerializableObject is object');

        $this->assertResult(false, $c, 1, 'integer is not object');
        $this->assertResult(false, $c, [], 'empty array is not object');
        $this->assertResult(false, $c, ['foo'], 'indexed array is not object');
        $this->assertResult(false, $c, new BSONArray(), 'BSONArray is not object');
        $this->assertResult(false, $c, new SerializableArray(), 'SerializableArray is not object');
        $this->assertResult(false, $c, new ObjectId(), 'Type other than Serializable is not object');
    }

    public function testTypeJavascript(): void
    {
        $c = new IsBsonType('javascript');

        $this->assertResult(false, $c, 1, 'integer is not javascript');
        $this->assertResult(false, $c, new Javascript('foo = 1;', ['x' => 1]), 'javascriptWithScope is not javascript');
    }

    public function testTypeJavascriptWithScope(): void
    {
        $c = new IsBsonType('javascriptWithScope');

        $this->assertResult(false, $c, 1, 'integer is not javascriptWithScope');
        $this->assertResult(false, $c, new Javascript('foo = 1;'), 'javascript is not javascriptWithScope');
    }

    private function assertResult($expected, Constraint $constraint, $value, string $message = ''): void
    {
        $this->assertSame($expected, $constraint->evaluate($value, '', true), $message);
    }
}

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
class SerializableArray implements Serializable
{
    public function bsonSerialize(): array
    {
        return ['foo'];
    }
}

class SerializableObject implements Serializable
{
    public function bsonSerialize(): array
    {
        return ['x' => 1];
    }
}
// phpcs:enable
