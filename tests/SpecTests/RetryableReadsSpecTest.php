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
 * @group serverless
 */
class RetryableReadsSpecTest extends FunctionalTestCase
{
    /** @var array */
    private static $skippedOperations = [
        'listCollectionObjects' => 'Not implemented',
        'listDatabaseObjects' => 'Not implemented',
        'listIndexNames' => 'Not implemented',
    ];

    /** @var array */
    private static $incompleteTests = ['mapReduce: MapReduce succeeds with retry on' => 'PHPLIB-715'];

    /**
     * Assert that the expected and actual command documents match.
     *
     * @param stdClass $expected Expected command document
     * @param stdClass $actual   Actual command document
     */
    public static function assertCommandMatches(stdClass $expected, stdClass $actual): void
    {
        static::assertDocumentsMatch($expected, $actual);
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @param stdClass     $test           Individual "tests[]" document
     * @param array        $runOn          Top-level "runOn" array with server requirements
     * @param array|object $data           Top-level "data" array to initialize collection
     * @param string       $databaseName   Name of database under test
     * @param string|null  $collectionName Name of collection under test
     * @param string|null  $bucketName     Name of GridFS bucket under test
     *
     * @dataProvider provideTests
     * @group matrix-testing-exclude-server-4.4-driver-4.2
     * @group matrix-testing-exclude-server-5.0-driver-4.2
     */
    public function testRetryableReads(stdClass $test, ?array $runOn, $data, string $databaseName, ?string $collectionName, ?string $bucketName): void
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

        if (isset(self::$incompleteTests[$this->dataDescription()])) {
            $this->markTestIncomplete(self::$incompleteTests[$this->dataDescription()]);
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
            $runOn = $json->runOn ?? null;
            $data = $json->data ?? [];
            // phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            $databaseName = $json->database_name ?? null;
            $collectionName = $json->collection_name ?? null;
            $bucketName = $json->bucket_name ?? null;
            // phpcs:enable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $runOn, $data, $databaseName, $collectionName, $bucketName];
            }
        }

        return $testArgs;
    }
}
