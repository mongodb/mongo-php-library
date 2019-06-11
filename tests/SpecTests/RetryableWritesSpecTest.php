<?php

namespace MongoDB\Tests\SpecTests;

use LogicException;
use stdClass;

/**
 * Retryable writes spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/retryable-writes
 */
class RetryableWritesSpecTest extends FunctionalTestCase
{
    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param string   $name  Test name
     * @param stdClass $test  Individual "tests[]" document
     * @param array    $runOn Top-level "runOn" array with server requirements
     * @param array    $data  Top-level "data" array to initialize collection
     */
    public function testRetryableWrites($name, stdClass $test, array $runOn = null, array $data)
    {
        $this->setName($name);

        // TODO: Revise this once a test environment with multiple mongos nodes is available (see: PHPLIB-430)
        if (isset($test->useMultipleMongoses) && $test->useMultipleMongoses && $this->isShardedCluster()) {
            $this->markTestSkipped('"useMultipleMongoses" is not supported');
        }

        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        $context = Context::fromRetryableWrites($test, $this->getDatabaseName(), $this->getCollectionName());
        $this->setContext($context);

        $this->dropTestAndOutcomeCollections();
        $this->insertDataFixtures($data);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        Operation::fromRetryableWrites($test->operation, $test->outcome)->assert($this, $context);

        if (isset($test->outcome->collection->data)) {
            $this->assertOutcomeCollectionData($test->outcome->collection->data);
        }
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/retryable-writes/*.json') as $filename) {
            $json = $this->decodeJson(file_get_contents($filename));
            $group = basename($filename, '.json');
            $runOn = isset($json->runOn) ? $json->runOn : null;
            $data = isset($json->data) ? $json->data : [];

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[] = [$name, $test, $runOn, $data];
            }
        }

        return $testArgs;
    }
}
