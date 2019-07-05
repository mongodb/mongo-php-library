<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\Collection;
use MongoDB\Driver\Exception\ServerException;

/**
 * Change Streams spec prose tests.
 *
 * @see https://github.com/mongodb/specifications/tree/master/source/change-streams
 */
class ChangeStreamsProseTest extends FunctionalTestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $this->skipIfChangeStreamIsNotSupported();

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
        $this->dropCollection();
    }

    public function tearDown()
    {
        if (!$this->hasFailed()) {
            $this->dropCollection();
        }

        parent::tearDown();
    }

    /**
     * ChangeStream will not attempt to resume after encountering error code
     * 11601 (Interrupted), 136 (CappedPositionLost), or 237 (CursorKilled)
     * while executing a getMore command.
     *
     * @dataProvider provideNonResumableErrorCodes
     */
    public function testProseTest5($errorCode)
    {
        if (version_compare($this->getServerVersion(), '4.0.0', '<')) {
            $this->markTestSkipped('failCommand is not supported');
        }

        $this->configureFailPoint([
            'configureFailPoint' => 'failCommand',
            'mode' => ['times' => 1],
            'data' => ['failCommands' => ['getMore'], 'errorCode' => $errorCode],
        ]);

        $this->createCollection();
        $changeStream = $this->collection->watch();
        $changeStream->rewind();

        $this->expectException(ServerException::class);
        $this->expectExceptionCode($errorCode);
        $changeStream->next();
    }

    public function provideNonResumableErrorCodes()
    {
        return [
            [136], // CappedPositionLost
            [237], // CursorKilled
            [11601], // Interrupted
        ];
    }
}
