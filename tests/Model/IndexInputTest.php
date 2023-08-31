<?php

namespace MongoDB\Tests\Model;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\IndexInput;
use MongoDB\Tests\TestCase;
use stdClass;

class IndexInputTest extends TestCase
{
    public function testConstructorShouldRequireKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required "key" document is missing from index specification');
        new IndexInput([]);
    }

    /** @dataProvider provideInvalidDocumentValues */
    public function testConstructorShouldRequireKeyToBeArrayOrObject($key): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "key" option to have type "document"');
        new IndexInput(['key' => $key]);
    }

    /** @dataProvider provideInvalidFieldOrderValues */
    public function testConstructorShouldRequireKeyFieldOrderToBeNumericOrString($order): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected order value for "x" field within "key" option to have type "numeric or string"');
        new IndexInput(['key' => ['x' => $order]]);
    }

    public function provideInvalidFieldOrderValues()
    {
        return $this->wrapValuesForDataProvider([true, [], new stdClass()]);
    }

    /** @dataProvider provideInvalidStringValues */
    public function testConstructorShouldRequireNameToBeString($name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected "name" option to have type "string"');
        new IndexInput(['key' => ['x' => 1], 'name' => $name]);
    }

    /**
     * @dataProvider provideExpectedNameAndKey
     * @param array|object $key
     */
    public function testNameGeneration($expectedName, $key): void
    {
        $this->assertSame($expectedName, (string) new IndexInput(['key' => $key]));
    }

    public function provideExpectedNameAndKey(): array
    {
        return [
            ['x_1', ['x' => 1]],
            ['x_1', (object) ['x' => 1]],
            ['x_1', new BSONDocument(['x' => 1])],
            ['x_1', Document::fromPHP(['x' => 1])],
            ['x_1_y_-1', ['x' => 1, 'y' => -1]],
            ['loc_2dsphere', ['loc' => '2dsphere']],
            ['loc_2dsphere_x_1', ['loc' => '2dsphere', 'x' => 1]],
            ['doc_text', ['doc' => 'text']],
        ];
    }

    public function testBsonSerialization(): void
    {
        $expected = (object) [
            'key' => ['x' => 1],
            'unique' => true,
            'name' => 'x_1',
        ];

        $indexInput = new IndexInput([
            'key' => ['x' => 1],
            'unique' => true,
        ]);

        $this->assertInstanceOf(Serializable::class, $indexInput);
        $this->assertEquals($expected, $indexInput->bsonSerialize());
    }
}
