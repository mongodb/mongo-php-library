<?php

namespace MongoDB\Tests\SpecTests\Crud;

use MongoDB\ClientBulkWrite;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Tests\SpecTests\FunctionalTestCase;

use function phpversion;
use function version_compare;

/**
 * Prose test 13: MongoClient.bulkWrite returns an error if auto-encryption is configured
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud/tests#13-mongoclientbulkwrite-returns-an-error-if-auto-encryption-is-configured
 */
class Prose13_BulkWriteUnsupportedForAutoEncryptionTest extends FunctionalTestCase
{
    public function testErrorIfAutoEncryptionIsConfigured(): void
    {
        $this->skipIfServerVersion('<', '8.0', 'bulkWrite command is not supported');

        if (version_compare(phpversion('mongodb'), '2.2', '>=')) {
            $this->markTestSkipped('MongoDB extension 2.2.0+ supports auto encryption with bulkWrite');
        }

        $this->skipIfClientSideEncryptionIsNotSupported();

        $client = self::createTestClient(null, [], [
            'autoEncryption' => [
                'keyVaultNamespace' => $this->getNamespace(),
                'kmsProviders' => ['aws' => ['accessKeyId' => 'foo', 'secretAccessKey' => 'bar']],
            ],
        ]);

        $collection = $client->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $bulkWrite = ClientBulkWrite::createWithCollection($collection);
        $bulkWrite->insertOne(['a' => 'b']);

        try {
            $client->bulkWrite($bulkWrite);
            self::fail('InvalidArgumentException was not thrown');
        } catch (InvalidArgumentException $e) {
            self::assertStringContainsString('bulkWrite does not currently support automatic encryption', $e->getMessage());
        }
    }
}
