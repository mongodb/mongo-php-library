<?php

namespace MongoDB\Tests\SpecTests\ClientSideEncryption;

use ArrayIterator;
use Generator;
use Iterator;
use MongoDB\BSON\Binary;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\ClientEncryption;
use MongoDB\Driver\Exception\EncryptionException;
use MultipleIterator;

use function base64_decode;
use function file_get_contents;
use function get_debug_type;
use function is_int;

/**
 * Prose test 22: Range Explicit Encryption
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#range-explicit-encryption
 * @group csfle
 * @group serverless
 */
class Prose22_RangeExplicitEncryptionTest extends FunctionalTestCase
{
    private ?ClientEncryption $clientEncryption = null;
    private ?Collection $collection = null;
    private ?Client $encryptedClient = null;
    private $key1Id;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->isStandalone()) {
            $this->markTestSkipped('Range explicit encryption tests require replica sets');
        }

        $this->skipIfServerVersion('<', '7.0.0', 'Range explicit encryption tests require MongoDB 7.0 or later');
        $this->skipIfServerVersion('>=', '8.0.0', 'Range explicit encryption tests require MongoDB 8.0 or earlier');

        $client = static::createTestClient();

        $key1Document = $this->decodeJson(file_get_contents(__DIR__ . '/../client-side-encryption/etc/data/keys/key1-document.json'));
        $this->key1Id = $key1Document->_id;

        // Drop the key vault collection and insert key1Document with a majority write concern
        self::insertKeyVaultData($client, [$key1Document]);

        $this->clientEncryption = $client->createClientEncryption([
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY))]],
        ]);

        $autoEncryptionOpts = [
            'keyVaultNamespace' => 'keyvault.datakeys',
            'kmsProviders' => ['local' => ['key' => new Binary(base64_decode(self::LOCAL_MASTERKEY))]],
            'bypassQueryAnalysis' => true,
        ];

        $this->encryptedClient = self::createTestClient(null, [], [
            'autoEncryption' => $autoEncryptionOpts,
            /* libmongocrypt caches results from listCollections. Use a new
             * client in each test to ensure its encryptedFields is applied. */
            'disableClientPersistence' => true,
        ]);
    }

    public function setUpWithTypeAndRangeOpts(string $type, array $rangeOpts): void
    {
        if ($type === 'DecimalNoPrecision' || $type === 'DecimalPrecision') {
            $this->markTestSkipped('Bundled libmongocrypt does not support Decimal128 (PHPC-2207)');
        }

        /* Read the encryptedFields file directly into BSON to preserve typing
         * for 64-bit integers. This means that DropEncryptedCollection and
         * CreateEncryptedCollection will be unable to inspect the option for
         * metadata collection names, but that's not necessary for the test. */
        $encryptedFields = Document::fromJSON(file_get_contents(__DIR__ . '/../client-side-encryption/etc/data/range-encryptedFields-' . $type . '.json'));

        $database = $this->encryptedClient->selectDatabase($this->getDatabaseName());
        $database->dropCollection('explicit_encryption', ['encryptedFields' => $encryptedFields]);
        $database->createCollection('explicit_encryption', ['encryptedFields' => $encryptedFields]);
        $this->collection = $database->selectCollection('explicit_encryption');

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts,
        ];

        $cast = self::getCastCallableForType($type);
        $fieldName = 'encrypted' . $type;

        $this->collection->insertMany([
            ['_id' => 0, $fieldName => $this->clientEncryption->encrypt($cast(0), $encryptOpts)],
            ['_id' => 1, $fieldName => $this->clientEncryption->encrypt($cast(6), $encryptOpts)],
            ['_id' => 2, $fieldName => $this->clientEncryption->encrypt($cast(30), $encryptOpts)],
            ['_id' => 3, $fieldName => $this->clientEncryption->encrypt($cast(200), $encryptOpts)],
        ]);
    }

    public function tearDown(): void
    {
        /* Since encryptedClient is created with disableClientPersistence=true,
         * free any objects that may hold a reference to its mongoc_client_t */
        $this->collection = null;
        $this->clientEncryption = null;
        $this->encryptedClient = null;
    }

    /** @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#test-setup-rangeopts */
    public static function provideTypeAndRangeOpts(): Generator
    {
        // TODO: skip DecimalNoPrecision test on mongos
        yield 'DecimalNoPrecision' => [
            'DecimalNoPrecision',
            ['sparsity' => 1],
        ];

        yield 'DecimalPrecision' => [
            'DecimalPrecision',
            [
                'min' => new Decimal128('0'),
                'max' => new Decimal128('200'),
                'sparsity' => 1,
                'precision' => 2,
            ],
        ];

        yield 'DoubleNoPrecision' => [
            'DoubleNoPrecision',
            ['sparsity' => 1],
        ];

        yield 'DoublePrecision' => [
            'DoublePrecision',
            [
                'min' => 0.0,
                'max' => 200.0,
                'sparsity' => 1,
                'precision' => 2,
            ],
        ];

        yield 'Date' => [
            'Date',
            [
                'min' => new UTCDateTime(0),
                'max' => new UTCDateTime(200),
                'sparsity' => 1,
            ],
        ];

        yield 'Int' => [
            'Int',
            [
                'min' => 0,
                'max' => 200,
                'sparsity' => 1,
            ],
        ];

        yield 'Long' => [
            'Long',
            [
                'min' => new Int64(0),
                'max' => new Int64(200),
                'sparsity' => 1,
            ],
        ];
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-1-can-decrypt-a-payload
     * @dataProvider provideTypeAndRangeOpts
     */
    public function testCase1_CanDecryptAPayload(string $type, array $rangeOpts): void
    {
        $this->setUpWithTypeAndRangeOpts($type, $rangeOpts);

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts,
        ];

        $cast = self::getCastCallableForType($type);
        $originalValue = $cast(6);

        $insertPayload = $this->clientEncryption->encrypt($originalValue, $encryptOpts);
        $decryptedValue = $this->clientEncryption->decrypt($insertPayload);

        /* Decryption of a 64-bit integer will likely result in a scalar int, so
         * cast it back to an Int64 before comparing to the original value. */
        if ($type === 'Long' && is_int($decryptedValue)) {
            $decryptedValue = $cast($decryptedValue);
        }

        /* Use separate assertions for type and equality as assertSame isn't
         * suitable for comparing BSON objects and using assertEquals alone
         * would disregard scalar type differences. */
        $this->assertSame(get_debug_type($originalValue), get_debug_type($decryptedValue));
        $this->assertEquals($originalValue, $decryptedValue);
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-2-can-find-encrypted-range-and-return-the-maximum
     * @dataProvider provideTypeAndRangeOpts
     */
    public function testCase2_CanFindEncryptedRangeAndReturnTheMaximum(string $type, array $rangeOpts): void
    {
        $this->setUpWithTypeAndRangeOpts($type, $rangeOpts);

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'queryType' => ClientEncryption::QUERY_TYPE_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts,
        ];

        $cast = self::getCastCallableForType($type);
        $fieldName = 'encrypted' . $type;

        $expr = [
            '$and' => [
                [$fieldName => ['$gte' => $cast(6)]],
                [$fieldName => ['$lte' => $cast(200)]],
            ],
        ];

        $encryptedExpr = $this->clientEncryption->encryptExpression($expr, $encryptOpts);
        $cursor = $this->collection->find($encryptedExpr, ['sort' => ['_id' => 1]]);

        $expectedDocuments = [
            ['_id' => 1, $fieldName => $cast(6)],
            ['_id' => 2, $fieldName => $cast(30)],
            ['_id' => 3, $fieldName => $cast(200)],
        ];

        $this->assertMultipleDocumentsMatch($expectedDocuments, $cursor);
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-3-can-find-encrypted-range-and-return-the-minimum
     * @dataProvider provideTypeAndRangeOpts
     */
    public function testCase3_CanFindEncryptedRangeAndReturnTheMinimum(string $type, array $rangeOpts): void
    {
        $this->setUpWithTypeAndRangeOpts($type, $rangeOpts);

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'queryType' => ClientEncryption::QUERY_TYPE_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts,
        ];

        $cast = self::getCastCallableForType($type);
        $fieldName = 'encrypted' . $type;

        $expr = [
            '$and' => [
                [$fieldName => ['$gte' => $cast(0)]],
                [$fieldName => ['$lte' => $cast(6)]],
            ],
        ];

        $encryptedExpr = $this->clientEncryption->encryptExpression($expr, $encryptOpts);
        $cursor = $this->collection->find($encryptedExpr, ['sort' => ['_id' => 1]]);

        $expectedDocuments = [
            ['_id' => 0, $fieldName => $cast(0)],
            ['_id' => 1, $fieldName => $cast(6)],
        ];

        $this->assertMultipleDocumentsMatch($expectedDocuments, $cursor);
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-4-can-find-encrypted-range-with-an-open-range-query
     * @dataProvider provideTypeAndRangeOpts
     */
    public function testCase4_CanFindEncryptedRangeWithAnOpenRangeQuery(string $type, array $rangeOpts): void
    {
        $this->setUpWithTypeAndRangeOpts($type, $rangeOpts);

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'queryType' => ClientEncryption::QUERY_TYPE_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts,
        ];

        $cast = self::getCastCallableForType($type);
        $fieldName = 'encrypted' . $type;

        $expr = ['$and' => [[$fieldName => ['$gt' => $cast(30)]]]];

        $encryptedExpr = $this->clientEncryption->encryptExpression($expr, $encryptOpts);
        $cursor = $this->collection->find($encryptedExpr, ['sort' => ['_id' => 1]]);
        $expectedDocuments = [['_id' => 3, $fieldName => $cast(200)]];

        $this->assertMultipleDocumentsMatch($expectedDocuments, $cursor);
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-5-can-run-an-aggregation-expression-inside-expr
     * @dataProvider provideTypeAndRangeOpts
     */
    public function testCase5_CanRunAnAggregationExpressionInsideExpr(string $type, array $rangeOpts): void
    {
        $this->setUpWithTypeAndRangeOpts($type, $rangeOpts);

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'queryType' => ClientEncryption::QUERY_TYPE_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts,
        ];

        $cast = self::getCastCallableForType($type);
        $fieldName = 'encrypted' . $type;
        $fieldPath = '$' . $fieldName;

        $expr = ['$and' => [['$lt' => [$fieldPath, $cast(30)]]]];

        $encryptedExpr = $this->clientEncryption->encryptExpression($expr, $encryptOpts);
        $cursor = $this->collection->find(['$expr' => $encryptedExpr], ['sort' => ['_id' => 1]]);

        $expectedDocuments = [
            ['_id' => 0, $fieldName => $cast(0)],
            ['_id' => 1, $fieldName => $cast(6)],
        ];

        $this->assertMultipleDocumentsMatch($expectedDocuments, $cursor);
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-6-encrypting-a-document-greater-than-the-maximum-errors
     * @dataProvider provideTypeAndRangeOpts
     */
    public function testCase6_EncryptingADocumentGreaterThanTheMaximumErrors(string $type, array $rangeOpts): void
    {
        if ($type === 'DecimalNoPrecision' || $type === 'DoubleNoPrecision') {
            $this->markTestSkipped('Test is not applicable to "NoPrecision" types');
        }

        $this->setUpWithTypeAndRangeOpts($type, $rangeOpts);

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts,
        ];

        $cast = self::getCastCallableForType($type);

        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('Value must be greater than or equal to the minimum value and less than or equal to the maximum value');
        $this->clientEncryption->encrypt($cast(201), $encryptOpts);
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-7-encrypting-a-value-of-a-different-type-errors
     * @dataProvider provideTypeAndRangeOpts
     */
    public function testCase7_EncryptingAValueOfADifferentTypeErrors(string $type, array $rangeOpts): void
    {
        if ($type === 'DecimalNoPrecision' || $type === 'DoubleNoPrecision') {
            /* Explicit encryption relies on min/max range options to check
             * types and "NoPrecision" intentionally omits those options. */
            $this->markTestSkipped('Test is not applicable to DoubleNoPrecision and DecimalNoPrecision');
        }

        $this->setUpWithTypeAndRangeOpts($type, $rangeOpts);

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts,
        ];

        $value = $type === 'Int' ? 6.0 : 6;

        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('expected matching \'min\' and value type');
        $this->clientEncryption->encrypt($value, $encryptOpts);
    }

    /**
     * @see https://github.com/mongodb/specifications/blob/master/source/client-side-encryption/tests/README.rst#case-8-setting-precision-errors-if-the-type-is-not-double-or-decimal128
     * @dataProvider provideTypeAndRangeOpts
     */
    public function testCase8_SettingPrecisionErrorsIfTheTypeIsNotDoubleOrDecimal128(string $type, array $rangeOpts): void
    {
        if ($type === 'DecimalNoPrecision' || $type === 'DecimalPrecision' || $type === 'DoubleNoPrecision' || $type === 'DoublePrecision') {
            $this->markTestSkipped('Test is not applicable to Double and Decimal types');
        }

        $this->setUpWithTypeAndRangeOpts($type, $rangeOpts);

        $encryptOpts = [
            'keyId' => $this->key1Id,
            'algorithm' => ClientEncryption::ALGORITHM_RANGE_PREVIEW,
            'contentionFactor' => 0,
            'rangeOpts' => $rangeOpts + ['precision' => 2],
        ];

        $cast = self::getCastCallableForType($type);

        $this->expectException(EncryptionException::class);
        $this->expectExceptionMessage('expected \'precision\' to be set with double or decimal128 index');
        $this->clientEncryption->encrypt($cast(6), $encryptOpts);
    }

    private function assertMultipleDocumentsMatch(array $expectedDocuments, Iterator $actualDocuments): void
    {
        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($expectedDocuments));
        $mi->attachIterator($actualDocuments);

        foreach ($mi as $documents) {
            [$expectedDocument, $actualDocument] = $documents;
            $this->assertNotNull($expectedDocument);
            $this->assertNotNull($actualDocument);

            $this->assertDocumentsMatch($expectedDocument, $actualDocument);
        }
    }

    private static function getCastCallableForType(string $type): callable
    {
        switch ($type) {
            case 'DecimalNoPrecision':
            case 'DecimalPrecision':
                return fn (int $value) => new Decimal128((string) $value);

            case 'DoubleNoPrecision':
            case 'DoublePrecision':
                return fn (int $value) => (double) $value;

            case 'Date':
                return fn (int $value) => new UTCDateTime($value);

            case 'Int':
                return fn (int $value) => $value;

            case 'Long':
                return fn (int $value) => new Int64($value);

            default:
                throw new LogicException('Unsupported type: ' . $type);
        }
    }
}
