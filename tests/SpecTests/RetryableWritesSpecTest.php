<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Driver\Exception\BulkWriteException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use stdClass;

use function basename;
use function file_get_contents;
use function glob;

/**
 * Retryable writes spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/retryable-writes
 * @group serverless
 */
class RetryableWritesSpecTest extends FunctionalTestCase
{
    public const NOT_PRIMARY = 10107;
    public const SHUTDOWN_IN_PROGRESS = 91;

    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param stdClass $test  Individual "tests[]" document
     * @param array    $runOn Top-level "runOn" array with server requirements
     * @param array    $data  Top-level "data" array to initialize collection
     */
    public function testRetryableWrites(stdClass $test, ?array $runOn, array $data): void
    {
        if (isset($runOn)) {
            $this->checkServerRequirements($runOn);
        }

        // Serverless uses a load balancer fronting a single proxy (PHPLIB-757)
        $useMultipleMongoses = $this->isMongos() || ($this->isLoadBalanced() && ! $this->isServerless())
            ? ($test->useMultipleMongoses ?? false)
            : false;

        $context = Context::fromRetryableWrites($test, $this->getDatabaseName(), $this->getCollectionName(), $useMultipleMongoses);
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
            $runOn = $json->runOn ?? null;
            $data = $json->data ?? [];

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $runOn, $data];
            }
        }

        return $testArgs;
    }

    /**
     * Prose test 1: when encountering a NoWritesPerformed error after an error with a RetryableWriteError label
     */
    public function testNoWritesPerformedErrorReturnsOriginalError(): void
    {
        if (! $this->isReplicaSet()) {
            $this->markTestSkipped('Test only applies to replica sets');
        }

        $this->skipIfServerVersion('<', '4.4.0', 'NoWritesPerformed error label is only supported on MongoDB 4.4+');

        $client = self::createTestClient(null, ['retryWrites' => true]);

        // Step 2: Configure a fail point with error code 91
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => [
                'writeConcernError' => [
                    'code' => self::SHUTDOWN_IN_PROGRESS,
                    'errorLabels' => ['RetryableWriteError'],
                ],
                'failCommands' => ['insert'],
            ],
        ]);

        $subscriber = new class ($this) implements CommandSubscriber {
            private RetryableWritesSpecTest $testCase;

            public function __construct(RetryableWritesSpecTest $testCase)
            {
                $this->testCase = $testCase;
            }

            public function commandStarted(CommandStartedEvent $event): void
            {
            }

            public function commandSucceeded(CommandSucceededEvent $event): void
            {
                if ($event->getCommandName() === 'insert') {
                    // Step 3: Configure a fail point with code 10107
                    $this->testCase->configureFailPoint([
                        'configureFailPoint' => 'failCommand',
                        'mode' => ['times' => 1],
                        'data' => [
                            'errorCode' => RetryableWritesSpecTest::NOT_PRIMARY,
                            'errorLabels' => ['RetryableWriteError', 'NoWritesPerformed'],
                            'failCommands' => ['insert'],
                        ],
                    ]);
                }
            }

            public function commandFailed(CommandFailedEvent $event): void
            {
            }
        };

        $client->getManager()->addSubscriber($subscriber);

        // Step 4: Run insertOne
        try {
            $client->selectCollection('db', 'retryable_writes')->insertOne(['write' => 1]);
        } catch (BulkWriteException $e) {
            $writeConcernError = $e->getWriteResult()->getWriteConcernError();
            $this->assertNotNull($writeConcernError);

            // Assert that the write concern error is from the first failpoint
            $this->assertSame(self::SHUTDOWN_IN_PROGRESS, $writeConcernError->getCode());
        }

        // Step 5: Disable the fail point
        $client->getManager()->removeSubscriber($subscriber);
        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => 'off',
        ]);
    }
}
