<?php

namespace MongoDB\Tests\Operation;

use Generator;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CreateEncryptedCollection;

use function get_debug_type;

class CreateEncryptedCollectionTest extends TestCase
{
    /**
     * @dataProvider provideInvalidConstructorOptions
     */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateEncryptedCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public function provideInvalidConstructorOptions(): Generator
    {
        yield 'encryptedFields is required' => [
            [],
        ];

        foreach ($this->getInvalidDocumentValues() as $value) {
            yield 'encryptedFields type: ' . get_debug_type($value) => [
                ['encryptedFields' => $value],
            ];
        }
    }
}
