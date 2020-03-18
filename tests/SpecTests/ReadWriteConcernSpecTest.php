<?php

namespace MongoDB\Tests\SpecTests;

use stdClass;
use function basename;
use function dirname;
use function file_get_contents;
use function glob;

/**
 * @see https://github.com/mongodb/specifications/tree/master/source/read-write-concern
 */
class ReadWriteConcernSpecTest extends FunctionalTestCase
{
    /** @var array */
    private static $incompleteTests = [];

    /**
     * Assert that the expected and actual command documents match.
     *
     * @param stdClass $expected Expected command document
     * @param stdClass $actual   Actual command document
     */
    public static function assertCommandMatches(stdClass $expected, stdClass $actual)
    {
        foreach ($expected as $key => $value) {
            if ($value === null) {
                static::assertObjectNotHasAttribute($key, $actual);
                unset($expected->{$key});
            }
        }

        static::assertDocumentsMatch($expected, $actual);
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param stdClass $test           Individual "tests[]" document
     * @param array    $runOn          Top-level "runOn" array with server requirements
     * @param array    $data           Top-level "data" array to initialize collection
     * @param string   $databaseName   Name of database under test
     * @param string   $collectionName Name of collection under test
     */
    public function testReadWriteConcern(stdClass $test, array $runOn = null, array $data, $databaseName = null, $collectionName = null)
    {
        if (isset(self::$incompleteTests[$this->dataDescription()])) {
            $this->markTestIncomplete(self::$incompleteTests[$this->dataDescription()]);
        }

        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        if (isset($test->skipReason)) {
            $this->markTestSkipped($test->skipReason);
        }

        $databaseName = $databaseName ?? $this->getDatabaseName();
        $collectionName = $collectionName ?? $this->getCollectionName();

        $context = Context::fromReadWriteConcern($test, $databaseName, $collectionName);
        $this->setContext($context);

        $this->dropTestAndOutcomeCollections();
        $this->insertDataFixtures($data);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromReadWriteConcern($test->expectations);
            $commandExpectations->startMonitoring();
        }

        foreach ($test->operations as $operation) {
            Operation::fromReadWriteConcern($operation)->assert($this, $context);
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

        foreach (glob(__DIR__ . '/read-write-concern/operation/*.json') as $filename) {
            $json = $this->decodeJson(file_get_contents($filename));
            $group = basename(dirname($filename)) . '/' . basename($filename, '.json');
            $runOn = $json->runOn ?? null;
            $data = $json->data ?? [];
            $databaseName = $json->database_name ?? null;
            $collectionName = $json->collection_name ?? null;

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $runOn, $data, $databaseName, $collectionName];
            }
        }

        return $testArgs;
    }
}
