<?php

namespace MongoDB\Tests\Model;

use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\TestCase;
use stdClass;
use ReflectionClass;

class BSONArrayTest extends TestCase
{
    public function testBsonSerializeReindexesKeys()
    {
        $data = [0 => 'foo', 2 => 'bar'];

        $array = new BSONArray($data);
        $this->assertSame($data, $array->getArrayCopy());
        $this->assertSame(['foo', 'bar'], $array->bsonSerialize());
    }

    public function testClone()
    {
        $array = new BSONArray([
            [
                'foo',
                new stdClass,
                ['bar', new stdClass],
            ],
            new BSONArray([
                'foo',
                new stdClass,
                ['bar', new stdClass],
            ]),
        ]);
        $arrayClone = clone $array;

        $this->assertSameDocument($array, $arrayClone);
        $this->assertNotSame($array, $arrayClone);
        $this->assertNotSame($array[0][1], $arrayClone[0][1]);
        $this->assertNotSame($array[0][2][1], $arrayClone[0][2][1]);
        $this->assertNotSame($array[1], $arrayClone[1]);
        $this->assertNotSame($array[1][1], $arrayClone[1][1]);
        $this->assertNotSame($array[1][2][1], $arrayClone[1][2][1]);
    }

    public function testCloneRespectsUncloneableObjects()
    {
        $this->assertFalse((new ReflectionClass(UncloneableObject::class))->isCloneable());

        $array = new BSONArray([
            [new UncloneableObject],
            new BSONArray([new UncloneableObject]),
        ]);
        $arrayClone = clone $array;

        $this->assertNotSame($array, $arrayClone);
        $this->assertSame($array[0][0], $arrayClone[0][0]);
        $this->assertNotSame($array[1], $arrayClone[1]);
        $this->assertSame($array[1][0], $arrayClone[1][0]);
    }

    public function testCloneSupportsBSONTypes()
    {
        /* Note: this test does not check that the BSON type itself is cloned,
         * as that is not yet supported in the driver (see: PHPC-1230). */
        $array = new BSONArray([
            [new ObjectId],
            new BSONArray([new ObjectId]),
        ]);
        $arrayClone = clone $array;

        $this->assertNotSame($array, $arrayClone);
        $this->assertNotSame($array[1], $arrayClone[1]);
    }

    public function testJsonSerialize()
    {
        $document = new BSONArray([
            'foo',
            new BSONArray(['foo' => 1, 'bar' => 2, 'baz' => 3]),
            new BSONDocument(['foo' => 1, 'bar' => 2, 'baz' => 3]),
            new BSONArray([new BSONArray([new BSONArray])]),
        ]);

        $expectedJson = '["foo",[1,2,3],{"foo":1,"bar":2,"baz":3},[[[]]]]';

        $this->assertSame($expectedJson, json_encode($document));
    }

    public function testJsonSerializeReindexesKeys()
    {
        $data = [0 => 'foo', 2 => 'bar'];

        $array = new BSONArray($data);
        $this->assertSame($data, $array->getArrayCopy());
        $this->assertSame(['foo', 'bar'], $array->jsonSerialize());
    }

    public function testSetState()
    {
        $data = ['foo', 'bar'];

        $array = BSONArray::__set_state($data);
        $this->assertInstanceOf('MongoDB\Model\BSONArray', $array);
        $this->assertSame($data, $array->getArrayCopy());
    }
}
