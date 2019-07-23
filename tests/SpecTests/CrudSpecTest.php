<?php

namespace MongoDB\Tests\SpecTests;

use stdClass;

/**
 * Crud spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/crud
 */
class CrudSpecTest extends FunctionalTestCase
{
    /* These should all pass before the driver can be considered compatible with
     * MongoDB 4.2. */
    private static $incompleteTests = [
        'aggregate-merge: Aggregate with $merge' => 'PHPLIB-438',
        'aggregate-merge: Aggregate with $merge and batch size of 0' => 'PHPLIB-438',
        'aggregate-merge: Aggregate with $merge and majority readConcern' => 'PHPLIB-438',
        'aggregate-merge: Aggregate with $merge and local readConcern' => 'PHPLIB-438',
        'aggregate-merge: Aggregate with $merge and available readConcern' => 'PHPLIB-438',
        'aggregate-out-readConcern: readConcern majority with out stage' => 'PHPLIB-431',
        'aggregate-out-readConcern: readConcern local with out stage' => 'PHPLIB-431',
        'aggregate-out-readConcern: readConcern available with out stage' => 'PHPLIB-431',
        'aggregate-out-readConcern: readConcern linearizable with out stage' => 'PHPLIB-431',
        'aggregate-out-readConcern: invalid readConcern with out stage' => 'PHPLIB-431',
        'bulkWrite-arrayFilters: BulkWrite with arrayFilters' => 'Fails due to command assertions',
        'updateWithPipelines: UpdateOne using pipelines' => 'PHPLIB-418',
        'updateWithPipelines: UpdateMany using pipelines' => 'PHPLIB-418',
        'updateWithPipelines: FindOneAndUpdate using pipelines' => 'PHPLIB-418',
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
     * @param string   $name           Test name
     * @param stdClass $test           Individual "tests[]" document
     * @param array    $runOn          Top-level "runOn" array with server requirements
     * @param array    $data           Top-level "data" array to initialize collection
     * @param string   $databaseName   Name of database under test
     * @param string   $collectionName Name of collection under test
     */
    public function testCrud($name, stdClass $test, array $runOn = null, array $data, $databaseName = null, $collectionName = null)
    {
        if (isset(self::$incompleteTests[$name])) {
            $this->markTestIncomplete(self::$incompleteTests[$name]);
        }

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
                $testArgs[$name] = [$name, $test, $runOn, $data, $databaseName, $collectionName];
            }
        }

        return $testArgs;
    }
}
