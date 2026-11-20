<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

/**
 * Prose test 17: Ensure database and collection names are validated
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#17-ensure-database-and-collection-names-are-validated
 */
class Prose17_DatabaseAndCollectionNameValidationTest extends FunctionalTestCase
{
    public function testDotInDatabaseNameViaGetDatabase(): void
    {
        $client = self::createTestClient();

        $this->expectException(InvalidArgumentException::class);
        $client->getDatabase('foo.bar')->getCollection('coll')->insertOne([]);
    }

    public function testDotInDatabaseNameViaGetCollection(): void
    {
        $client = self::createTestClient();

        $this->expectException(InvalidArgumentException::class);
        $client->getCollection('foo.bar', 'coll')->insertOne([]);
    }

    public function testNulByteInDatabaseName(): void
    {
        $client = self::createTestClient();

        $this->expectException(InvalidArgumentException::class);
        $client->getDatabase("foo\0bar")->getCollection('coll')->insertOne([]);
    }

    public function testNulByteInCollectionName(): void
    {
        $client = self::createTestClient();

        $this->expectException(InvalidArgumentException::class);
        $client->getDatabase('db')->getCollection("foo\0bar")->insertOne([]);
    }

    public function testNulByteInBulkWriteDatabaseName(): void
    {
        $client = self::createTestClient();

        $this->expectException(InvalidArgumentException::class);
        ClientBulkWrite::createWithCollection($client->getCollection("foo\0bar", 'coll'))
            ->insertOne([]);
    }

    public function testNulByteInBulkWriteCollectionName(): void
    {
        $client = self::createTestClient();

        $this->expectException(InvalidArgumentException::class);
        ClientBulkWrite::createWithCollection($client->getCollection('db', "foo\0bar"))
            ->insertOne([]);
    }
}
