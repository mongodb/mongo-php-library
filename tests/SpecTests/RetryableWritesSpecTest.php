<?php

namespace MongoDB\Tests\SpecTests;

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
     * @param string $name  Test name
     * @param array  $test  Individual "tests[]" document
     * @param array  $runOn Top-level "runOn" document
     * @param array  $data  Top-level "data" array to initialize collection
     */
    public function testRetryableWrites($name, array $test, array $runOn = null, array $data)
    {
        $this->setName($name);

        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        // TODO: Remove this once retryWrites=true by default (see: PHPC-1324)
        $test['clientOptions']['retryWrites'] = true;

        $this->initTestSubjects($test);
        $this->initOutcomeCollection($test);
        $this->initDataFixtures($data);

        if (isset($test['failPoint'])) {
            $this->configureFailPoint($test['failPoint']);
        }

        $this->assertOperation($test['operation'], $test['outcome']);

        if (isset($test['outcome']['collection']['data'])) {
            $this->assertOutcomeCollectionData($test['outcome']['collection']['data']);
        }
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/retryable-writes/*.json') as $filename) {
            $json = json_decode(file_get_contents($filename), true);
            $group = basename($filename, '.json');
            $runOn = isset($json['runOn']) ? $json['runOn'] : null;
            $data = isset($json['data']) ? $json['data'] : [];

            foreach ($json['tests'] as $test) {
                $name = $group . ': ' . $test['description'];
                $testArgs[] = [$name, $test, $runOn, $data];
            }
        }

        return $testArgs;
    }
}
