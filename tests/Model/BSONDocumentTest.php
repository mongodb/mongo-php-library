<?php

namespace MongoDB\Tests;

use MongoDB\Model\BSONDocument;
use ArrayObject;

class BSONDocumentTest extends TestCase
{
    public function testBsonSerializeCastsToObject()
    {
        $data = [0 => 'foo', 2 => 'bar'];

        $document = new BSONDocument($data);
        $this->assertSame($data, $document->getArrayCopy());
        $this->assertEquals((object) [0 => 'foo', 2 => 'bar'], $document->bsonSerialize());
    }
}
