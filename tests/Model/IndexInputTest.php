<?php

namespace MongoDB\Tests\Model;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\IndexInput;
use MongoDB\Tests\TestCase;
use stdClass;

class IndexInputTest extends TestCase
{
    public function testConstructorShouldRequireKey()
    {
        $this->expectException(InvalidArgumentException::class);
        new IndexInput([]);
    }

    public function testConstructorShouldRequireKeyToBeArrayOrObject()
    {
        $this->expectException(InvalidArgumentException::class);
        new IndexInput(['key' => 'foo']);
    }

    /**
     * @dataProvider provideInvalidFieldOrderValues
     */
    public function testConstructorShouldRequireKeyFieldOrderToBeNumericOrString($order)
    {
        $this->expectException(InvalidArgumentException::class);
        new IndexInput(['key' => ['x' => $order]]);
    }

    public function provideInvalidFieldOrderValues()
    {
        return $this->wrapValuesForDataProvider([true, [], new stdClass]);
    }

    public function testConstructorShouldRequireNamespace()
    {
        $this->expectException(InvalidArgumentException::class);
        new IndexInput(['key' => ['x' => 1]]);
    }

    public function testConstructorShouldRequireNamespaceToBeString()
    {
        $this->expectException(InvalidArgumentException::class);
        new IndexInput(['key' => ['x' => 1], 'ns' => 1]);
    }

    public function testConstructorShouldRequireNameToBeString()
    {
        $this->expectException(InvalidArgumentException::class);
        new IndexInput(['key' => ['x' => 1], 'ns' => 'foo.bar', 'name' => 1]);
    }

    /**
     * @dataProvider provideExpectedNameAndKey
     */
    public function testNameGeneration($expectedName, array $key)
    {
        $this->assertSame($expectedName, (string) new IndexInput(['key' => $key, 'ns' => 'foo.bar']));
    }

    public function provideExpectedNameAndKey()
    {
        return [
            ['x_1', ['x' => 1]],
            ['x_1_y_-1', ['x' => 1, 'y' => -1]],
            ['loc_2dsphere', ['loc' => '2dsphere']],
            ['loc_2dsphere_x_1', ['loc' => '2dsphere', 'x' => 1]],
            ['doc_text', ['doc' => 'text']],
        ];
    }

    public function testBsonSerialization()
    {
        $expected = [
            'key' => ['x' => 1],
            'ns' => 'foo.bar',
            'name' => 'x_1',
        ];

        $indexInput = new IndexInput([
            'key' => ['x' => 1],
            'ns' => 'foo.bar',
        ]);

        $this->assertInstanceOf('MongoDB\BSON\Serializable', $indexInput);
        $this->assertEquals($expected, $indexInput->bsonSerialize());
    }
}
