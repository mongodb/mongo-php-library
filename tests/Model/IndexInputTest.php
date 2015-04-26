<?php

namespace MongoDB\Tests;

use MongoDB\Model\IndexInput;
use MongoDB\Tests\TestCase;

class IndexInputTest extends TestCase
{
    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testConstructorShouldRequireKey()
    {
        new IndexInput(array());
    }

    /**
     * @expectedException MongoDB\Exception\UnexpectedTypeException
     */
    public function testConstructorShouldRequireKeyToBeArrayOrObject()
    {
        new IndexInput(array('key' => 'foo'));
    }

    /**
     * @expectedException MongoDB\Exception\UnexpectedTypeException
     */
    public function testConstructorShouldRequireKeyOrderToBeScalar()
    {
        new IndexInput(array('key' => array('x' => array())));
    }

    /**
     * @expectedException MongoDB\Exception\InvalidArgumentException
     */
    public function testConstructorShouldRequireNamespace()
    {
        new IndexInput(array('key' => array('x' => 1)));
    }

    /**
     * @expectedException MongoDB\Exception\UnexpectedTypeException
     */
    public function testConstructorShouldRequireNamespaceToBeString()
    {
        new IndexInput(array('key' => array('x' => 1), 'ns' => 1));
    }

    /**
     * @expectedException MongoDB\Exception\UnexpectedTypeException
     */
    public function testConstructorShouldRequireNameToBeString()
    {
        new IndexInput(array('key' => array('x' => 1), 'ns' => 'foo.bar', 'name' => 1));
    }

    /**
     * @dataProvider provideExpectedNameAndKey
     */
    public function testNameGeneration($expectedName, array $key)
    {
        $this->assertSame($expectedName, (string) new IndexInput(array('key' => $key, 'ns' => 'foo.bar')));
    }

    public function provideExpectedNameAndKey()
    {
        return array(
            array('x_1', array('x' => 1)),
            array('x_1_y_-1', array('x' => 1, 'y' => -1)),
            array('loc_2dsphere', array('loc' => '2dsphere')),
            array('loc_2dsphere_x_1', array('loc' => '2dsphere', 'x' => 1)),
            array('doc_text', array('doc' => 'text')),
        );
    }

    public function testBsonSerialization()
    {
        $expected = array(
            'key' => array('x' => 1),
            'ns' => 'foo.bar',
            'name' => 'x_1',
        );

        $indexInput = new IndexInput(array(
            'key' => array('x' => 1),
            'ns' => 'foo.bar',
        ));

        $this->assertInstanceOf('BSON\Serializable', $indexInput);
        $this->assertEquals($expected, $indexInput->bsonSerialize());
    }
}
