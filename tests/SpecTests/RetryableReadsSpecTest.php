<?php

namespace MongoDB\Tests\SpecTests;

use stdClass;
use function basename;
use function file_get_contents;
use function glob;
use function is_object;
use function strpos;

/**
 * Retryable reads spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/retryable-reads
 */
class RetryableReadsSpecTest extends FunctionalTestCase
{
    /** @var array */
    private static $skippedOperations = [
        'listCollectionNames' => 'Not implemented',
        'listCollectionObjects' => 'Not implemented',
        'listDatabaseNames' => 'Not implemented',
        'listDatabaseObjects' => 'Not implemented',
        'listIndexNames' => 'Not implemented',
    ];

    /**
     * Assert that the expected and actual command documents match.
     *
     * Note: this method may modify the $expected object.
     *
     * @param stdClass $expected Expected command document
     * @param stdClass $actual   Actual command document
     */
    public static function assertCommandMatches(stdClass $expected, stdClass $actual)
    {
        static::assertDocumentsMatch($expected, $actual);
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param stdClass     $test           Individual "tests[]" document
     * @param array        $runOn          Top-level "runOn" array with server requirements
     * @param array|object $data           Top-level "data" array to initialize collection
     * @param string       $databaseName   Name of database under test
     * @param string|null  $collectionName Name of collection under test
     * @param string|null  $bucketName     Name of GridFS bucket under test
     */
    public function testRetryableReads(stdClass $test, array $runOn = null, $data, $databaseName, $collectionName, $bucketName)
    {
        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        foreach (self::$skippedOperations as $operation => $skipReason) {
            if (strpos($this->dataDescription(), $operation) === 0) {
                $this->markTestSkipped($skipReason);
            }
        }

        if (strpos($this->dataDescription(), 'changeStreams-') === 0) {
            $this->skipIfChangeStreamIsNotSupported();
        }

        $context = Context::fromRetryableReads($test, $databaseName, $collectionName, $bucketName);
        $this->setContext($context);

        $this->dropTestAndOutcomeCollections();

        if (is_object($data)) {
            foreach ($data as $collectionName => $documents) {
                $this->assertIsArray($documents);
                $this->insertDataFixtures($documents, $collectionName);
            }
        } else {
            $this->insertDataFixtures($data);
        }

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromRetryableReads($test->expectations);
            $commandExpectations->startMonitoring();
        }

        foreach ($test->operations as $operation) {
            Operation::fromRetryableReads($operation)->assert($this, $context);
        }

        if (isset($commandExpectations)) {
            $commandExpectations->stopMonitoring();
            $commandExpectations->assert($this, $context);
        }

        if (isset($test->outcome->collection->data)) {
            $this->assertOutcomeCollectionData($test->outcome->collection->data);
        }
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/retryable-reads/*.json') as $filename) {
            $json = $this->decodeJson(file_get_contents($filename));
            $group = basename($filename, '.json');
            $runOn = isset($json->runOn) ? $json->runOn : null;
            $data = isset($json->data) ? $json->data : [];
            $databaseName = isset($json->database_name) ? $json->database_name : null;
            $collectionName = isset($json->collection_name) ? $json->collection_name : null;
            $bucketName = isset($json->bucket_name) ? $json->bucket_name : null;

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $runOn, $data, $databaseName, $collectionName, $bucketName];
            }
        }

        return $testArgs;
    }
}
