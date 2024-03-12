<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Driver\Cursor;
use MongoDB\Tests\CommandObserver;

use function current;
use function explode;
use function parse_url;

/**
 * Atlas Data Lake spec tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/atlas-data-lake-testing/tests
 * @group atlas-data-lake
 */
class AtlasDataLakeSpecTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (! $this->isAtlasDataLake()) {
            $this->markTestSkipped('Server is not Atlas Data Lake');
        }
    }

    /**
     * Prose test 1: killCursors command
     */
    public function testKillCursors(): void
    {
        $cursorId = null;
        $cursorNamespace = null;

        (new CommandObserver())->observe(
            function (): void {
                $client = static::createTestClient();
                $client->test->driverdata->find([], ['batchSize' => 2, 'limit' => 3]);
            },
            function (array $event) use (&$cursorId, &$cursorNamespace): void {
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

                [$databaseName, $collectionName] = explode('.', $cursorNamespace, 2);
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
            },
        );
    }

    /**
     * Prose test 2: Connect without authentication
     */
    public function testConnectWithoutAuth(): void
    {
        /* Parse URI to remove userinfo component. The query string is left
         * as-is and must not include authMechanism or credentials. */
        $parts = parse_url(static::getUri());
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        $uri = $parts['scheme'] . '://' . $parts['host'] . $port . $path . $query;

        $client = static::createTestClient($uri);
        $cursor = $client->selectDatabase($this->getDatabaseName())->command(['ping' => 1]);

        $this->assertInstanceOf(Cursor::class, $cursor);
        $this->assertCommandSucceeded(current($cursor->toArray()));
    }

    /**
     * Prose test 3: Connect with SCRAM-SHA-1 authentication
     */
    public function testConnectwithSCRAMSHA1(): void
    {
        $client = static::createTestClient(null, ['authMechanism' => 'SCRAM-SHA-1']);
        $cursor = $client->selectDatabase($this->getDatabaseName())->command(['ping' => 1]);

        $this->assertInstanceOf(Cursor::class, $cursor);
        $this->assertCommandSucceeded(current($cursor->toArray()));
    }

    /**
     * Prose test 4: Connect with SCRAM-SHA-256 authentication
     */
    public function testConnectwithSCRAMSHA256(): void
    {
        $client = static::createTestClient(null, ['authMechanism' => 'SCRAM-SHA-256']);
        $cursor = $client->selectDatabase($this->getDatabaseName())->command(['ping' => 1]);

        $this->assertInstanceOf(Cursor::class, $cursor);
        $this->assertCommandSucceeded(current($cursor->toArray()));
    }
}
