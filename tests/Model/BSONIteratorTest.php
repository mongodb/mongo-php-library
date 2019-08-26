<?php

namespace MongoDB\Tests\Model;

use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Model\BSONIterator;
use MongoDB\Tests\TestCase;
use function array_map;
use function implode;
use function iterator_to_array;
use function MongoDB\BSON\fromPHP;
use function substr;

class BSONIteratorTest extends TestCase
{
    /**
     * @dataProvider provideTypeMapOptionsAndExpectedDocuments
     */
    public function testValidValues(array $typeMap = null, $binaryString, array $expectedDocuments)
    {
        $bsonIt = new BSONIterator($binaryString, ['typeMap' => $typeMap]);

        $results = iterator_to_array($bsonIt);

        $this->assertEquals($expectedDocuments, $results);
    }

    public function provideTypeMapOptionsAndExpectedDocuments()
    {
        return [
            [
                null,
                implode(array_map(
                    'MongoDB\BSON\fromPHP',
                    [
                        ['_id' => 1, 'x' => ['foo' => 'bar']],
                        ['_id' => 3, 'x' => ['foo' => 'bar']],
                    ]
                )),
                [
                    (object) ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
                    (object) ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'array', 'document' => 'array'],
                implode(array_map(
                    'MongoDB\BSON\fromPHP',
                    [
                        ['_id' => 1, 'x' => ['foo' => 'bar']],
                        ['_id' => 3, 'x' => ['foo' => 'bar']],
                    ]
                )),
                [
                    ['_id' => 1, 'x' => ['foo' => 'bar']],
                    ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'object', 'document' => 'array'],
                implode(array_map(
                    'MongoDB\BSON\fromPHP',
                    [
                        ['_id' => 1, 'x' => ['foo' => 'bar']],
                        ['_id' => 3, 'x' => ['foo' => 'bar']],
                    ]
                )),
                [
                    (object) ['_id' => 1, 'x' => ['foo' => 'bar']],
                    (object) ['_id' => 3, 'x' => ['foo' => 'bar']],
                ],
            ],
            [
                ['root' => 'array', 'document' => 'stdClass'],
                implode(array_map(
                    'MongoDB\BSON\fromPHP',
                    [
                        ['_id' => 1, 'x' => ['foo' => 'bar']],
                        ['_id' => 3, 'x' => ['foo' => 'bar']],
                    ]
                )),
                [
                    ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
                    ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
                ],
            ],
        ];
    }

    public function testCannotReadLengthFromFirstDocument()
    {
        $binaryString = substr(fromPHP([]), 0, 3);

        $bsonIt = new BSONIterator($binaryString);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected at least 4 bytes; 3 remaining');
        $bsonIt->rewind();
    }

    public function testCannotReadLengthFromSubsequentDocument()
    {
        $binaryString = fromPHP([]) . substr(fromPHP([]), 0, 3);

        $bsonIt = new BSONIterator($binaryString);
        $bsonIt->rewind();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected at least 4 bytes; 3 remaining');
        $bsonIt->next();
    }

    public function testCannotReadFirstDocument()
    {
        $binaryString = substr(fromPHP([]), 0, 4);

        $bsonIt = new BSONIterator($binaryString);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected 5 bytes; 4 remaining');
        $bsonIt->rewind();
    }

    public function testCannotReadSecondDocument()
    {
        $binaryString = fromPHP([]) . substr(fromPHP([]), 0, 4);

        $bsonIt = new BSONIterator($binaryString);
        $bsonIt->rewind();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected 5 bytes; 4 remaining');
        $bsonIt->next();
    }
}
