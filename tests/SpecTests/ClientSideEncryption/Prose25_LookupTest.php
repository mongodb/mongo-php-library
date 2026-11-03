<?php

namespace MongoDB\Tests\SpecTests\ClientSideEncryption;

use MongoDB\BSON\Binary;
use MongoDB\Client;
use PHPUnit\Framework\Attributes\Group;

use function base64_decode;
use function file_get_contents;

/**
 * Prose test 25: Test lookup
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.md#25-test-lookup
 */
#[Group('csfle')]
class Prose25_LookupTest extends FunctionalTestCase
{
    private $key1Id;

    private const COLL_CSFLE = 'csfle';
    private const COLL_CSFLE2 = 'csfle2';
    private const COLL_QE = 'qe';
    private const COLL_QE2 = 'qe2';
    private const COLL_NO_SCHEMA = 'no_schema';
    private const COLL_NO_SCHEMA2 = 'no_schema2';

    private static string $dataDir = __DIR__ . '/../../specifications/source/client-side-encryption/etc/data/lookup';

    public function setUp(): void
    {
        parent::setUp();

        if ($this->isStandalone()) {
            $this->markTestSkipped('Lookup tests require replica sets');
        }

        $this->skipIfServerVersion('<', '7.0.0', 'Lookup encryption tests require MongoDB 7.0 or later');

        $key1Document = $this->decodeJson(file_get_contents(self::$dataDir . '/key-doc.json'));
        $this->key1Id = $key1Document->_id;

        $encryptedClient = $this->getEncryptedClient();

        // Drop the key vault collection and insert key1Document with a majority write concern
        self::insertKeyVaultData($encryptedClient, [$key1Document]);

        $this->refreshCollections($encryptedClient);
    }

    private function getEncryptedClient(): Client
    {
        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY))]],
        ];

        return self::createTestClient(null, [], [
            'autoEncryption' => $autoEncryptionOpts,
            /* libmongocrypt caches results from listCollections. Use a new
             * client in each test to ensure its encryptedFields is applied. */
            'disableClientPersistence' => true,
        ]);
    }

    private function refreshCollections(Client $client): void
    {
        $encryptedDb = $client->getDatabase(self::getDatabaseName());
        $unencryptedDb = self::createTestClient()->getDatabase(self::getDatabaseName());

        $optionsMap = [
            self::COLL_CSFLE => [
                'validator' => [
                    '$jsonSchema' => $this->decodeJson(file_get_contents(self::$dataDir . '/schema-csfle.json')),
                ],
            ],
            self::COLL_CSFLE2 => [
                'validator' => [
                    '$jsonSchema' => $this->decodeJson(file_get_contents(self::$dataDir . '/schema-csfle2.json')),
                ],
            ],
            self::COLL_QE => [
                'encryptedFields' => $this->decodeJson(file_get_contents(self::$dataDir . '/schema-qe.json')),
            ],
            self::COLL_QE2 => [
                'encryptedFields' => $this->decodeJson(file_get_contents(self::$dataDir . '/schema-qe2.json')),
            ],
            self::COLL_NO_SCHEMA => [],
            self::COLL_NO_SCHEMA2 => [],
        ];

        foreach ($optionsMap as $collectionName => $options) {
            $encryptedDb->dropCollection($collectionName);
            $encryptedDb->createCollection($collectionName, $options);

            $collection = $unencryptedDb->getCollection($collectionName);

            $result = $encryptedDb->getCollection($collectionName)->insertOne([$collectionName => $collectionName]);

            if ($options) {
                $document = $collection->findOne(['_id' => $result->getInsertedId()]);
                $this->assertInstanceOf(Binary::class, $document->{$collectionName});
            }
        }
    }

    private function assertPipelineReturnsSingleDocument(string $collection, array $pipeline, array $expected): void
    {
        $this->skipIfServerVersion('<', '8.1.0', 'Lookup test case requires server version 8.1.0 or later');
        $this->skipIfClientSideEncryptionIsNotSupported();

        $cursor = $this
            ->getEncryptedClient()
            ->getCollection(self::getDatabaseName(), $collection)
            ->aggregate($pipeline);

        $cursor->rewind();
        $this->assertMatchesDocument(
            $expected,
            $cursor->current(),
        );
        $this->assertNull($cursor->next());
    }

    public function testCase1_CsfleJoinsNoSchema(): void
    {
        $pipeline = [
            [
                '$match' => ['csfle' => 'csfle'],
            ],
            [
                '$lookup' => [
                    'from' => 'no_schema',
                    'as' => 'matched',
                    'pipeline' => [
                        [
                            '$match' => ['no_schema' => 'no_schema'],
                        ],
                        [
                            '$project' => ['_id' => 0],
                        ],
                    ],
                ],
            ],
            [
                '$project' => ['_id' => 0],
            ],
        ];
        $expected = [
            'csfle' => 'csfle',
            'matched' => [
                ['no_schema' => 'no_schema'],
            ],
        ];

        $this->assertPipelineReturnsSingleDocument(self::COLL_CSFLE, $pipeline, $expected);
    }

    public function testCase2_QeJoinsNoSchema(): void
    {
        $pipeline = [
            [
                '$match' => ['qe' => 'qe'],
            ],
            [
                '$lookup' => [
                    'from' => 'no_schema',
                    'as' => 'matched',
                    'pipeline' => [
                        [
                            '$match' => ['no_schema' => 'no_schema'],
                        ],
                        [
                            '$project' => [
                                '_id' => 0,
                                '__safeContent__' => 0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$project' => [
                    '_id' => 0,
                    '__safeContent__' => 0,
                ],
            ],
        ];
        $expected = [
            'qe' => 'qe',
            'matched' => [
                ['no_schema' => 'no_schema'],
            ],
        ];

        $this->assertPipelineReturnsSingleDocument(self::COLL_QE, $pipeline, $expected);
    }

    public function testCase3_NoSchemaJoinsCsfle(): void
    {
        $pipeline = [['$match' => ['no_schema' => 'no_schema']],
            [
                '$lookup' => [
                    'from' => 'csfle',
                    'as' => 'matched',
                    'pipeline' => [
                        [
                            '$match' => ['csfle' => 'csfle'],
                        ],
                        [
                            '$project' => ['_id' => 0],
                        ],
                    ],
                ],
            ],
            ['$project' => ['_id' => 0]],
        ];
        $expected = ['no_schema' => 'no_schema', 'matched' => [['csfle' => 'csfle']]];

        $this->assertPipelineReturnsSingleDocument(self::COLL_NO_SCHEMA, $pipeline, $expected);
    }

    public function testCase4_NoSchemaJoinsQe(): void
    {
        $pipeline = [
            [
                '$match' => ['no_schema' => 'no_schema'],
            ],
            [
                '$lookup' => [
                    'from' => 'qe',
                    'as' => 'matched',
                    'pipeline' => [
                        [
                            '$match' => ['qe' => 'qe'],
                        ],
                        [
                            '$project' => [
                                '_id' => 0,
                                '__safeContent__' => 0,
                            ],
                        ],
                    ],
                ],
            ],
            [
                '$project' => ['_id' => 0],
            ],
        ];
        $expected = [
            'no_schema' => 'no_schema',
            'matched' => [
                ['qe' => 'qe'],
            ],
        ];

        $this->assertPipelineReturnsSingleDocument(self::COLL_NO_SCHEMA, $pipeline, $expected);
    }

    public function testCase5_CsfleJoinsCsfle2(): void
    {
        $pipeline = [
            ['$match' => ['csfle' => 'csfle']],
            [
                '$lookup' => [
                    'from' => 'csfle2',
                    'as' => 'matched',
                    'pipeline' => [
                        [
                            '$match' => ['csfle2' => 'csfle2'],
                        ],
                        [
                            '$project' => ['_id' => 0],
                        ],
                    ],
                ],
            ],
            ['$project' => ['_id' => 0]],
        ];
        $expected = ['csfle' => 'csfle', 'matched' => [['csfle2' => 'csfle2']]];

        $this->assertPipelineReturnsSingleDocument(self::COLL_CSFLE, $pipeline, $expected);
    }

    public function testCase6_QeJoinsQe2(): void
    {
        $pipeline = [
            ['$match' => ['qe' => 'qe']],
            [
                '$lookup' => [
                    'from' => 'qe2',
                    'as' => 'matched',
                    'pipeline' => [
                        [
                            '$match' => ['qe2' => 'qe2'],
                        ],
                        [
                            '$project' => [
                                '_id' => 0,
                                '__safeContent__' => 0,
                            ],
                        ],
                    ],
                ],
            ],
            ['$project' => ['_id' => 0, '__safeContent__' => 0]],
        ];
        $expected = ['qe' => 'qe', 'matched' => [['qe2' => 'qe2']]];

        $this->assertPipelineReturnsSingleDocument(self::COLL_QE, $pipeline, $expected);
    }

    public function testCase7_NoSchemaJoinsNoSchema2(): void
    {
        $pipeline = [
            ['$match' => ['no_schema' => 'no_schema']],
            [
                '$lookup' => [
                    'from' => 'no_schema2',
                    'as' => 'matched',
                    'pipeline' => [
                        ['$match' => ['no_schema2' => 'no_schema2']],
                        ['$project' => ['_id' => 0]],
                    ],
                ],
            ],
            ['$project' => ['_id' => 0]],
        ];
        $expected = ['no_schema' => 'no_schema', 'matched' => [['no_schema2' => 'no_schema2']]];

        $this->assertPipelineReturnsSingleDocument(self::COLL_NO_SCHEMA, $pipeline, $expected);
    }

    public function testCase8_CsfleJoinsQeFails(): void
    {
        $this->skipIfServerVersion('>', '8.2.0', 'Test must be updated for 8.2+ (PHPLIB-1727)');

        $this->skipIfServerVersion('<', '8.1.0', 'Lookup test case requires server version 8.1.0 or later');
        $this->skipIfClientSideEncryptionIsNotSupported();

        $this->expectExceptionMessage('not supported');

        $this->getEncryptedClient()
            ->getCollection(self::getDatabaseName(), self::COLL_CSFLE)
            ->aggregate([
                [
                    '$match' => ['csfle' => 'qe'],
                ],
                [
                    '$lookup' => [
                        'from' => 'qe',
                        'as' => 'matched',
                        'pipeline' => [
                            [
                                '$match' => ['qe' => 'qe'],
                            ],
                            [
                                '$project' => ['_id' => 0],
                            ],
                        ],
                    ],
                ],
                [
                    '$project' => ['_id' => 0],
                ],
            ]);
    }

    public function testCase9_TestErrorWithLessThan8_1(): void
    {
        $this->markTestSkipped('Depends on PHPC-2616 to determine crypt shared version.');
    }
}
