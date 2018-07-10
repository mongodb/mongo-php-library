<?php

namespace MongoDB\Tests\Model;

use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\TestCase;
use ArrayObject;
use stdClass;
use ReflectionClass;

class BSONDocumentTest extends TestCase
{
    public function testConstructorDefaultsToPropertyAccess()
    {
        $document = new BSONDocument(['foo' => 'bar']);
        $this->assertEquals(ArrayObject::ARRAY_AS_PROPS, $document->getFlags());
        $this->assertSame('bar', $document->foo);
    }

    public function testBsonSerializeCastsToObject()
    {
        $data = [0 => 'foo', 2 => 'bar'];

        $document = new BSONDocument($data);
        $this->assertSame($data, $document->getArrayCopy());
        $this->assertEquals((object) [0 => 'foo', 2 => 'bar'], $document->bsonSerialize());
    }

    public function testClone()
    {
        $document = new BSONDocument([
            'a' => [
                'a' => 'foo',
                'b' => new stdClass,
                'c' => ['bar', new stdClass],
            ],
            'b' => new BSONDocument([
                'a' => 'foo',
                'b' => new stdClass,
                'c' => ['bar', new stdClass],
            ]),
        ]);
        $documentClone = clone $document;

        $this->assertSameDocument($document, $documentClone);
        $this->assertNotSame($document, $documentClone);
        $this->assertNotSame($document['a']['b'], $documentClone['a']['b']);
        $this->assertNotSame($document['a']['c'][1], $documentClone['a']['c'][1]);
        $this->assertNotSame($document['b'], $documentClone['b']);
        $this->assertNotSame($document['b']['b'], $documentClone['b']['b']);
        $this->assertNotSame($document['b']['c'][1], $documentClone['b']['c'][1]);
    }

    public function testCloneRespectsUncloneableObjects()
    {
        $this->assertFalse((new ReflectionClass(UncloneableObject::class))->isCloneable());

        $document = new BSONDocument([
            'a' => ['a' => new UncloneableObject],
            'b' => new BSONDocument(['a' => new UncloneableObject]),
        ]);
        $documentClone = clone $document;

        $this->assertNotSame($document, $documentClone);
        $this->assertSame($document['a']['a'], $documentClone['a']['a']);
        $this->assertNotSame($document['b'], $documentClone['b']);
        $this->assertSame($document['b']['a'], $documentClone['b']['a']);
    }

    public function testCloneSupportsBSONTypes()
    {
        /* Note: this test does not check that the BSON type itself is cloned,
         * as that is not yet supported in the driver (see: PHPC-1230). */
        $document = new BSONDocument([
            'a' => ['a' => new ObjectId],
            'b' => new BSONDocument(['a' => new ObjectId]),
        ]);
        $documentClone = clone $document;

        $this->assertNotSame($document, $documentClone);
        $this->assertNotSame($document['b'], $documentClone['b']);
    }

    public function testJsonSerialize()
    {
        $document = new BSONDocument([
            'foo' => 'bar',
            'array' => new BSONArray([1, 2, 3]),
            'object' => new BSONDocument([1, 2, 3]),
            'nested' => new BSONDocument([new BSONDocument([new BSONDocument])]),
        ]);

        $expectedJson = '{"foo":"bar","array":[1,2,3],"object":{"0":1,"1":2,"2":3},"nested":{"0":{"0":{}}}}';

        $this->assertSame($expectedJson, json_encode($document));
    }

    public function testJsonSerializeCastsToObject()
    {
        $data = [0 => 'foo', 2 => 'bar'];

        $document = new BSONDocument($data);
        $this->assertSame($data, $document->getArrayCopy());
        $this->assertEquals((object) [0 => 'foo', 2 => 'bar'], $document->jsonSerialize());
    }

    public function testSetState()
    {
        $data = ['foo' => 'bar'];

        $document = BSONDocument::__set_state($data);
        $this->assertInstanceOf('MongoDB\Model\BSONDocument', $document);
        $this->assertSame($data, $document->getArrayCopy());
    }
}
