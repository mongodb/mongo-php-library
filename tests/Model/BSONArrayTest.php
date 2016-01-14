<?php

namespace MongoDB\Tests;

use MongoDB\Model\BSONArray;

class BSONArrayTest extends TestCase
{
    public function testBsonSerializeReindexesKeys()
    {
        $data = [0 => 'foo', 2 => 'bar'];

        $array = new BSONArray($data);
        $this->assertSame($data, $array->getArrayCopy());
        $this->assertSame(['foo', 'bar'], $array->bsonSerialize());
    }
}
