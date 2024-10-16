<?php

namespace MongoDB\Tests\Operation;

use Generator;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\CreateEncryptedCollection;
use PHPUnit\Framework\Attributes\DataProvider;

class CreateEncryptedCollectionTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateEncryptedCollection($this->getDatabaseName(), $this->getCollectionName(), $options);
    }

    public static function provideInvalidConstructorOptions(): Generator
    {
        yield 'encryptedFields is required' => [
            [],
        ];

        yield from self::createOptionDataProvider([
            'encryptedFields' => self::getInvalidDocumentValues(),
        ]);
    }
}
