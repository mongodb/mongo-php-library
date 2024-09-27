<?php

namespace MongoDB\Tests\Model;

use Generator;
use MongoDB\BSON\Document;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Model\BSONIterator;
use MongoDB\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function array_map;
use function implode;
use function iterator_to_array;
use function substr;

class BSONIteratorTest extends TestCase
{
    #[DataProvider('provideTypeMapOptionsAndExpectedDocuments')]
    public function testValidValues(?array $typeMap, array $expectedDocuments): void
    {
        $binaryString = implode(array_map(
            fn ($input) => (string) Document::fromPHP($input),
            [
                ['_id' => 1, 'x' => ['foo' => 'bar']],
                ['_id' => 3, 'x' => ['foo' => 'bar']],
            ],
        ));

        $bsonIt = new BSONIterator($binaryString, ['typeMap' => $typeMap]);

        $results = iterator_to_array($bsonIt);

        $this->assertEquals($expectedDocuments, $results);
    }

    public static function provideTypeMapOptionsAndExpectedDocuments(): Generator
    {
        yield 'No type map' => [
            'typeMap' => null,
            'expectedDocuments' => [
                (object) ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
                (object) ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
            ],
        ];

        yield 'Array type map' => [
            'typeMap' => ['root' => 'array', 'document' => 'array'],
            'expectedDocuments' => [
                ['_id' => 1, 'x' => ['foo' => 'bar']],
                ['_id' => 3, 'x' => ['foo' => 'bar']],
            ],
        ];

        yield 'Root as object' => [
            'typeMap' => ['root' => 'object', 'document' => 'array'],
            'expectedDocuments' => [
                (object) ['_id' => 1, 'x' => ['foo' => 'bar']],
                (object) ['_id' => 3, 'x' => ['foo' => 'bar']],
            ],
        ];

        yield 'Document as object' => [
            'typeMap' => ['root' => 'array', 'document' => 'stdClass'],
            'expectedDocuments' => [
                ['_id' => 1, 'x' => (object) ['foo' => 'bar']],
                ['_id' => 3, 'x' => (object) ['foo' => 'bar']],
            ],
        ];
    }

    public function testCannotReadLengthFromFirstDocument(): void
    {
        $binaryString = substr((string) Document::fromPHP([]), 0, 3);

        $bsonIt = new BSONIterator($binaryString);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected at least 4 bytes; 3 remaining');
        $bsonIt->rewind();
    }

    public function testCannotReadLengthFromSubsequentDocument(): void
    {
        $binaryString = (string) Document::fromPHP([]) . substr((string) Document::fromPHP([]), 0, 3);

        $bsonIt = new BSONIterator($binaryString);
        $bsonIt->rewind();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected at least 4 bytes; 3 remaining');
        $bsonIt->next();
    }

    public function testCannotReadFirstDocument(): void
    {
        $binaryString = substr((string) Document::fromPHP([]), 0, 4);

        $bsonIt = new BSONIterator($binaryString);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected 5 bytes; 4 remaining');
        $bsonIt->rewind();
    }

    public function testCannotReadSecondDocument(): void
    {
        $binaryString = (string) Document::fromPHP([]) . substr((string) Document::fromPHP([]), 0, 4);

        $bsonIt = new BSONIterator($binaryString);
        $bsonIt->rewind();

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected 5 bytes; 4 remaining');
        $bsonIt->next();
    }
}
