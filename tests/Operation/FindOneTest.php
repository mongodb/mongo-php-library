<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\FindOne;

class FindOneTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     * @dataProvider provideInvalidConstructorTypeMapOptions
     */
    public function testConstructorTypeMapOption($typeMap)
    {
        new FindOne($this->getDatabaseName(), $this->getCollectionName(), [], ['typeMap' => $typeMap]);
    }

    public function provideInvalidConstructorTypeMapOptions()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidArrayValues());
    }
}
