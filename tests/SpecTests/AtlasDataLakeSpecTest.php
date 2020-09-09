<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Client;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use MongoDB\Tests\CommandObserver;
use stdClass;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use function basename;
use function current;
use function explode;
use function file_get_contents;
use function glob;
use function parse_url;

/**
 * Atlas Data Lake spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/atlas-data-lake-testing/tests
 */
class AtlasDataLakeSpecTest extends FunctionalTestCase
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

        if (! $this->isAtlasDataLake()) {
            $this->markTestSkipped('Server is not Atlas Data Lake');
        }
    }

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
    public function testAtlasDataLake(stdClass $test, array $runOn = null, array $data, $databaseName = null, $collectionName = null)
    {
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

        /* Note: Atlas Data Lake is read-only, so do not attempt to drop the
         * collection under test or insert data fixtures. Necesarry data
         * fixtures are already specified in the mongohoused configuration. */

        if (isset($test->failPoint)) {
            throw new LogicException('ADL tests are not expected to configure fail points');
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
            throw new LogicException('ADL tests are not expected to assert collection data');
        }
    }

    public function provideTests()
    {
        $testArgs = [];

        foreach (glob(__DIR__ . '/atlas_data_lake/*.json') as $filename) {
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
     * Prose test 1: Connect without authentication
     */
    public function testKillCursors()
    {
        $cursorId = null;
        $cursorNamespace = null;

        (new CommandObserver())->observe(
            function () {
                $client = new Client(static::getUri());
                $client->test->driverdata->find([], ['batchSize' => 2, 'limit' => 3]);
            },
            function (array $event) use (&$cursorId, &$cursorNamespace) {
                if ($event['started']->getCommandName() === 'find') {
                    $this->assertArrayHasKey('succeeded', $event);

                    $reply = $event['succeeded']->getReply();
                    $this->assertObjectHasAttribute('cursor', $reply);
                    $this->assertIsObject($reply->cursor);
                    $this->assertObjectHasAttribute('id', $reply->cursor);
                    $this->assertIsInt($reply->cursor->id);
                    $this->assertObjectHasAttribute('ns', $reply->cursor);
                    $this->assertIsString($reply->cursor->ns);

                    /* Note: MongoDB\Driver\CursorId is not used here; however,
                     * we shouldn't have to worry about encoutnering a 64-bit
                     * cursor IDs on a 32-bit platform mongohoused allocates IDs
                     * sequentially (starting from 1). */
                    $cursorId = $reply->cursor->id;
                    $cursorNamespace = $reply->cursor->ns;

                    return;
                }

                /* After the initial find command, expect that killCursors is
                 * next and that a cursor ID and namespace were collected. */
                $this->assertSame('killCursors', $event['started']->getCommandName());
                $this->assertIsInt($cursorId);
                $this->assertIsString($cursorNamespace);

                list($databaseName, $collectionName) = explode('.', $cursorNamespace, 2);
                $command = $event['started']->getCommand();

                /* Assert that the killCursors command uses the namespace and
                 * cursor ID from the find command reply. */
                $this->assertSame($databaseName, $event['started']->getDatabaseName());
                $this->assertSame($databaseName, $command->{'$db'});
                $this->assertObjectHasAttribute('killCursors', $command);
                $this->assertSame($collectionName, $command->killCursors);
                $this->assertObjectHasAttribute('cursors', $command);
                $this->assertIsArray($command->cursors);
                $this->assertArrayHasKey(0, $command->cursors);
                $this->assertSame($cursorId, $command->cursors[0]);

                /* Assert that the killCursors command reply indicates that the
                 * expected cursor ID was killed. */
                $reply = $event['succeeded']->getReply();
                $this->assertObjectHasAttribute('cursorsKilled', $reply);
                $this->assertIsArray($reply->cursorsKilled);
                $this->assertArrayHasKey(0, $reply->cursorsKilled);
                $this->assertSame($cursorId, $reply->cursorsKilled[0]);
            }
        );
    }

    /**
     * Prose test 2: Connect without authentication
     */
    public function testConnectWithoutAuth()
    {
        /* Parse URI to remove userinfo component. The query string is left
         * as-is and must not include authMechanism or credentials. */
        $parts = parse_url(static::getUri());
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        $uri = $parts['scheme'] . '://' . $parts['host'] . $port . $path . $query;

        $client = new Client($uri);
        $cursor = $client->selectDatabase($this->getDatabaseName())->command(['ping' => 1]);

        $this->assertInstanceOf(Cursor::class, $cursor);
        $this->assertCommandSucceeded(current($cursor->toArray()));
    }

    /**
     * Prose test 3: Connect with SCRAM-SHA-1 authentication
     */
    public function testConnectwithSCRAMSHA1()
    {
        $client = new Client(static::getUri(), ['authMechanism' => 'SCRAM-SHA-1']);
        $cursor = $client->selectDatabase($this->getDatabaseName())->command(['ping' => 1]);

        $this->assertInstanceOf(Cursor::class, $cursor);
        $this->assertCommandSucceeded(current($cursor->toArray()));
    }

    /**
     * Prose test 4: Connect with SCRAM-SHA-256 authentication
     */
    public function testConnectwithSCRAMSHA256()
    {
        $client = new Client(static::getUri(), ['authMechanism' => 'SCRAM-SHA-256']);
        $cursor = $client->selectDatabase($this->getDatabaseName())->command(['ping' => 1]);

        $this->assertInstanceOf(Cursor::class, $cursor);
        $this->assertCommandSucceeded(current($cursor->toArray()));
    }

    private function isAtlasDataLake() : bool
    {
        $cursor = $this->manager->executeCommand(
            $this->getDatabaseName(),
            new Command(['buildInfo' => 1])
        );

        $document = current($cursor->toArray());

        return ! empty($document->dataLake);
    }
}
