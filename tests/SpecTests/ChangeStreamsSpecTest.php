<?php

namespace MongoDB\Tests\SpecTests;

use ArrayIterator;
use LogicException;
use MongoDB\ChangeStream;
use MongoDB\Driver\Exception\Exception;
use MongoDB\Model\BSONDocument;
use MultipleIterator;
use stdClass;
use function basename;
use function count;
use function file_get_contents;
use function glob;

/**
 * Change Streams spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/change-streams
 */
class ChangeStreamsSpecTest extends FunctionalTestCase
{
    /** @var array */
    private static $incompleteTests = ['change-streams-errors: Change Stream should error when _id is projected out' => 'PHPC-1419'];

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
     * Assert that the expected and actual documents match.
     *
     * @param array $expectedDocuments Expected documents
     * @param array $actualDocuments   Actual documents
     */
    public static function assertResult(array $expectedDocuments, array $actualDocuments)
    {
        static::assertCount(count($expectedDocuments), $actualDocuments);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($expectedDocuments));
        $mi->attachIterator(new ArrayIterator($actualDocuments));

        foreach ($mi as $documents) {
            list($expectedDocument, $actualDocument) = $documents;

            $constraint = new DocumentsMatchConstraint($expectedDocument, true, true, ['42']);

            static::assertThat($actualDocument, $constraint);
        }
    }

    /**
     * Execute an individual test case from the specification.
     *
     * @dataProvider provideTests
     * @param stdClass $test            Individual "tests[]" document
     * @param string   $databaseName    Name of database under test
     * @param string   $collectionName  Name of collection under test
     * @param string   $database2Name   Name of alternate database under test
     * @param string   $collection2Name Name of alternate collection under test
     */
    public function testChangeStreams(stdClass $test, $databaseName = null, $collectionName = null, $database2Name = null, $collection2Name = null)
    {
        if (isset(self::$incompleteTests[$this->dataDescription()])) {
            $this->markTestIncomplete(self::$incompleteTests[$this->dataDescription()]);
        }

        $this->checkServerRequirements($this->createRunOn($test));

        if (! isset($databaseName, $collectionName, $database2Name, $collection2Name)) {
            $this->fail('Required database and collection names are unset');
        }

        $context = Context::fromChangeStreams($test, $databaseName, $collectionName);
        $this->setContext($context);

        $this->dropDatabasesAndCreateCollection($databaseName, $collectionName);
        $this->dropDatabasesAndCreateCollection($database2Name, $collection2Name);

        if (isset($test->failPoint)) {
            $this->configureFailPoint($test->failPoint);
        }

        if (isset($test->expectations)) {
            $commandExpectations = CommandExpectations::fromChangeStreams($test->expectations);
            $commandExpectations->startMonitoring();
        }

        $errorExpectation = ErrorExpectation::fromChangeStreams($test->result);
        $resultExpectation = ResultExpectation::fromChangeStreams($test->result, [$this, 'assertResult']);

        $result = null;
        $exception = null;

        try {
            $changeStream = $this->createChangeStream($test);
        } catch (Exception $e) {
            $exception = $e;
        }

        if (isset($commandExpectations)) {
            $commandExpectations->stopMonitoring();
        }

        foreach ($test->operations as $operation) {
            Operation::fromChangeStreams($operation)->assert($this, $context);
        }

        if (isset($commandExpectations)) {
            $commandExpectations->startMonitoring();
        }

        /* If the change stream was successfully created (i.e. $exception is
         * null), attempt to iterate up to the expected number of results. It's
         * possible that some errors (e.g. projecting out _id) will only be
         * thrown during iteration, so we must also try/catch here. */
        try {
            if (isset($changeStream)) {
                $limit = isset($test->result->success) ? count($test->result->success) : 0;
                $result = $this->iterateChangeStream($changeStream, $limit);
            }
        } catch (Exception $e) {
            $this->assertNull($exception);
            $exception = $e;
        }

        $errorExpectation->assert($this, $exception);
        $resultExpectation->assert($this, $result);

        if (isset($commandExpectations)) {
            $commandExpectations->stopMonitoring();
            $commandExpectations->assert($this, $context);
        }
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/change-streams/*.json') as $filename) {
            $json = $this->decodeJson(file_get_contents($filename));
            $group = basename($filename, '.json');
            $databaseName = isset($json->database_name) ? $json->database_name : null;
            $database2Name = isset($json->database2_name) ? $json->database2_name : null;
            $collectionName = isset($json->collection_name) ? $json->collection_name : null;
            $collection2Name = isset($json->collection2_name) ? $json->collection2_name : null;

            foreach ($json->tests as $test) {
                $name = $group . ': ' . $test->description;
                $testArgs[$name] = [$test, $databaseName, $collectionName, $database2Name, $collection2Name];
            }
        }

        return $testArgs;
    }

    /**
     * Create a change stream.
     *
     * @param stdClass $test
     * @return ChangeStream
     * @throws LogicException if the target is unsupported
     */
    private function createChangeStream(stdClass $test)
    {
        $context = $this->getContext();
        $pipeline = isset($test->changeStreamPipeline) ? $test->changeStreamPipeline : [];
        $options = isset($test->changeStreamOptions) ? (array) $test->changeStreamOptions : [];

        switch ($test->target) {
            case 'client':
                return $context->getClient()->watch($pipeline, $options);
            case 'database':
                return $context->getDatabase()->watch($pipeline, $options);
            case 'collection':
                return $context->getCollection()->watch($pipeline, $options);
            default:
                throw new LogicException('Unsupported target: ' . $test->target);
        }
    }

    /**
     * Convert the server requirements to a standard "runOn" array used by other
     * specifications.
     *
     * @param stdClass $test
     * @return array
     */
    private function createRunOn(stdClass $test)
    {
        $req = new stdClass();

        /* Append ".99" as patch version, since command monitoring tests expect
         * the minor version to be an inclusive upper bound. */
        if (isset($test->maxServerVersion)) {
            $req->maxServerVersion = $test->maxServerVersion;
        }

        if (isset($test->minServerVersion)) {
            $req->minServerVersion = $test->minServerVersion;
        }

        if (isset($test->topology)) {
            $req->topology = $test->topology;
        }

        return [$req];
    }

    /**
     * Drop the database and create the collection.
     *
     * @param string $databaseName
     * @param string $collectionName
     */
    private function dropDatabasesAndCreateCollection($databaseName, $collectionName)
    {
        $context = $this->getContext();

        $database = $context->getClient()->selectDatabase($databaseName);
        $database->drop($context->defaultWriteOptions);
        $database->createCollection($collectionName, $context->defaultWriteOptions);
    }

    /**
     * Iterate a change stream.
     *
     * @param ChangeStream $changeStream
     * @param integer      $limit
     * @return BSONDocument[]
     */
    private function iterateChangeStream(ChangeStream $changeStream, $limit = 0)
    {
        if ($limit < 0) {
            throw new LogicException('$limit is negative');
        }

        /* Limit iterations to guard against an infinite loop should a test fail
         * to return as many results as are expected. Require at least one
         * iteration to allow next() a chance to throw for error tests. */
        $maxIterations = $limit + 1;
        $events = [];

        for ($i = 0, $changeStream->rewind(); $i < $maxIterations; $i++, $changeStream->next()) {
            if (! $changeStream->valid()) {
                continue;
            }

            $event = $changeStream->current();
            $this->assertInstanceOf(BSONDocument::class, $event);
            $events[] = $event;

            if (count($events) >= $limit) {
                break;
            }
        }

        return $events;
    }
}
