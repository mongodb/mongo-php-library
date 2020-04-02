<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Client;
use MongoDB\Driver\Exception\BulkWriteException;
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
    public function testCrud(stdClass $test, array $runOn = null, array $data, $databaseName = null, $collectionName = null)
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

        $context = Context::fromCrud($test, $databaseName, $collectionName);
        $this->setContext($context);

        $this->dropTestAndOutcomeCollections();
        $this->insertDataFixtures($data);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromCrud((array) $test->expectations);
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

    /**
     * Prose test 1: "errInfo" is propagated
     */
    public function testErrInfoIsPropagated()
    {
        $runOn = [(object) ['minServerVersion' => '4.0.0']];
        $this->checkServerRequirements($runOn);

        $errInfo = (object) [
            'writeConcern' => (object) [
                'w' => 2,
                'wtimeout' => 0,
                'provenance' => 'clientSupplied',
            ],
        ];

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'failCommands' => ['insert'],
                'writeConcernError' => [
                    'code' => 100,
                    'codeName' => 'UnsatisfiableWriteConcern',
                    'errmsg' => 'Not enough data-bearing nodes',
                    'errInfo' => $errInfo,
                ],
            ],
        ]);

        $client = new Client(static::getUri());

        try {
            $client->selectCollection($this->getDatabaseName(), $this->getCollectionName())->insertOne(['fail' => 1]);
            $this->fail('Expected insert command to fail');
        } catch (BulkWriteException $e) {
            self::assertEquals($errInfo, $e->getWriteResult()->getWriteConcernError()->getInfo());
        }
    }
}
