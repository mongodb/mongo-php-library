<?php

namespace MongoDB\Tests\SpecTests;

use stdClass;
use function basename;
use function file_get_contents;
use function glob;

/**
 * Crud spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud
 */
class CrudSpecTest extends FunctionalTestCase
{
    /**
     * Assert that the expected and actual command documents match.
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
     * @param stdClass $test           Individual "tests[]" document
     * @param array    $runOn          Top-level "runOn" array with server requirements
     * @param array    $data           Top-level "data" array to initialize collection
     * @param string   $databaseName   Name of database under test
     * @param string   $collectionName Name of collection under test
     */
    public function testCrud(stdClass $test, array $runOn = null, array $data, $databaseName = null, $collectionName = null)
    {
        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        if (isset($test->skipReason)) {
            $this->markTestSkipped($test->skipReason);
        }

        $databaseName = isset($databaseName) ? $databaseName : $this->getDatabaseName();
        $collectionName = isset($collectionName) ? $collectionName : $this->getCollectionName();

        $context = Context::fromCrud($test, $databaseName, $collectionName);
        $this->setContext($context);

        $this->dropTestAndOutcomeCollections();
        $this->insertDataFixtures($data);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromCrud($test->expectations);
            $commandExpectations->startMonitoring();
        }

        foreach ($test->operations as $operation) {
            Operation::fromCrud($operation)->assert($this, $context);
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

        foreach (glob(__DIR__ . '/crud/*.json') as $filename) {
            $json = $this->decodeJson(file_get_contents($filename));
            $group = basename($filename, '.json');
            $runOn = isset($json->runOn) ? $json->runOn : null;
            $data = isset($json->data) ? $json->data : [];
            $databaseName = isset($json->database_name) ? $json->database_name : null;
            $collectionName = isset($json->collection_name) ? $json->collection_name : null;

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $runOn, $data, $databaseName, $collectionName];
            }
        }

        return $testArgs;
    }
}
