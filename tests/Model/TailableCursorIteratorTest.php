<?php

namespace MongoDB\Tests\Model;

use MongoDB\Collection;
use MongoDB\Driver\Exception\LogicException;
use MongoDB\Model\TailableCursorIterator;
use MongoDB\Operation\Find;
use MongoDB\Operation\CreateCollection;
use MongoDB\Operation\DropCollection;
use MongoDB\Tests\CommandObserver;
use MongoDB\Tests\FunctionalTestCase;

class TailableCursorIteratorTest extends FunctionalTestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $operation = new DropCollection($this->getDatabaseName(), $this->getCollectionName());
        $operation->execute($this->getPrimaryServer());

        $operation = new CreateCollection($this->getDatabaseName(), $this->getCollectionName(), ['capped' => true, 'size' => 8192]);
        $operation->execute($this->getPrimaryServer());

        $this->collection = new Collection($this->manager, $this->getDatabaseName(), $this->getCollectionName());
    }

    public function testFirstBatchIsEmpty()
    {
        $this->collection->insertOne(['x' => 1]);

        $cursor = $this->collection->find(['x' => ['$gt' => 1]], ['cursorType' => Find::TAILABLE]);
        $iterator = new TailableCursorIterator($cursor, true);

        $this->assertNoCommandExecuted(function() use ($iterator) { $iterator->rewind(); });
        $this->assertFalse($iterator->valid());

        $this->collection->insertOne(['x' => 2]);

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertMatchesDocument(['x' => 2], $iterator->current());

        $this->expectException(LogicException::class);
        $iterator->rewind();
    }

    public function testFirstBatchIsNotEmpty()
    {
        $this->collection->insertOne(['x' => 1]);

        $cursor = $this->collection->find([], ['cursorType' => Find::TAILABLE]);
        $iterator = new TailableCursorIterator($cursor, false);

        $this->assertNoCommandExecuted(function() use ($iterator) { $iterator->rewind(); });
        $this->assertTrue($iterator->valid());
        $this->assertMatchesDocument(['x' => 1], $iterator->current());

        $this->collection->insertOne(['x' => 2]);

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertMatchesDocument(['x' => 2], $iterator->current());

        $this->expectException(LogicException::class);
        $iterator->rewind();
    }

    private function assertNoCommandExecuted(callable $callable)
    {
        $commands = [];

        (new CommandObserver)->observe(
            $callable,
            function(array $event) use (&$commands) {
                $this->fail(sprintf('"%s" command was executed', $event['started']->getCommandName()));
            }
        );

        $this->assertEmpty($commands);
    }
}
