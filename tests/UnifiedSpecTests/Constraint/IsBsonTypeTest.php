<?php

namespace MongoDB\Tests\UnifiedSpecTests\Constraint;

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
use stdClass;
use function MongoDB\BSON\fromJSON;
use function MongoDB\BSON\toPHP;
use function unserialize;
use const PHP_INT_SIZE;

class IsBsonTypeTest extends TestCase
{
    /**
     * @dataProvider provideTypes
     */
    public function testConstraint($type, $value)
    {
        $c = new IsBsonType($type);

        $this->assertTrue($c->evaluate($value, '', true));
    }

    public function provideTypes()
    {
        $undefined = toPHP(fromJSON('{ "undefined": {"$undefined": true} }'));
        $symbol = toPHP(fromJSON('{ "symbol": {"$symbol": "test"} }'));
        $dbPointer = toPHP(fromJSON('{ "dbPointer": {"$dbPointer": {"$ref": "phongo.test", "$id" : { "$oid" : "5a2e78accd485d55b405ac12" }  }} }'));

        return [
            'double' => ['double', 1.4],
            'string' => ['string', 'foo'],
            'object(stdClass)' => ['object', new stdClass()],
            'object(BSONDocument)' => ['object', new BSONDocument()],
            'array(array)' => ['array', ['foo']],
            'array(BSONArray)' => ['array', new BSONArray()],
            'binData' => ['binData', new Binary('', 0)],
            'undefined' => ['undefined', $undefined->undefined],
            'objectId' => ['objectId', new ObjectId()],
            'bool' => ['bool', true],
            'date' => ['date', new UTCDateTime()],
            'null' => ['null', null],
            'regex' => ['regex', new Regex('.*')],
            'dbPointer' => ['dbPointer', $dbPointer->dbPointer],
            'javascript' => ['javascript', new Javascript('foo = 1;')],
            'symbol' => ['symbol', $symbol->symbol],
            'javascriptWithScope' => ['javascriptWithScope', new Javascript('foo = 1;', ['x' => 1])],
            'int' => ['int', 1],
            'timestamp' => ['timestamp', new Timestamp(0, 0)],
            'long' => ['long', PHP_INT_SIZE == 4 ? unserialize('C:18:"MongoDB\BSON\Int64":38:{a:1:{s:7:"integer";s:10:"4294967296";}}') : 4294967296],
            'decimal' => ['decimal', new Decimal128('18446744073709551616')],
            'minKey' => ['minKey', new MinKey()],
            'maxKey' => ['maxKey', new MaxKey()],
        ];
    }

    public function testErrorMessage()
    {
        $c = new IsBsonType('string');

        try {
            $c->evaluate(1);
            $this->fail('Expected a comparison failure');
        } catch (ExpectationFailedException $e) {
            $this->assertStringMatchesFormat('Failed asserting that %s is of BSON type "string".', $e->getMessage());
        }
    }

    public function testTypeArray()
    {
        $c = new IsBsonType('array');

        $this->assertFalse($c->evaluate(1, '', true));
        $this->assertFalse($c->evaluate(['x' => 1], '', true));
        $this->assertFalse($c->evaluate([0 => 'a', 2 => 'c'], '', true));
    }

    public function testTypeObject()
    {
        $c = new IsBsonType('object');

        $this->assertFalse($c->evaluate(1, '', true));
        $this->assertFalse($c->evaluate(new BSONArray(), '', true));
    }

    public function testTypeJavascript()
    {
        $c = new IsBsonType('javascript');

        $this->assertFalse($c->evaluate(1, '', true));
        $this->assertFalse($c->evaluate(new Javascript('foo = 1;', ['x' => 1]), '', true));
    }

    public function testTypeJavascriptWithScope()
    {
        $c = new IsBsonType('javascriptWithScope');

        $this->assertFalse($c->evaluate(1, '', true));
        $this->assertFalse($c->evaluate(new Javascript('foo = 1;'), '', true));
    }
}
