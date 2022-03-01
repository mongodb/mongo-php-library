<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Operation\Count;
use MongoDB\Operation\CreateIndexes;
use MongoDB\Operation\InsertMany;
use MongoDB\Tests\CommandObserver;

class CountFunctionalTest extends FunctionalTestCase
{
    public function testDefaultReadConcernIsOmitted(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Count(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    ['readConcern' => $this->createDefaultReadConcern()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectNotHasAttribute('readConcern', $event['started']->getCommand());
            }
        );
    }

    public function testHintOption(): void
    {
        $insertMany = new InsertMany($this->getDatabaseName(), $this->getCollectionName(), [
            ['x' => 1],
            ['x' => 2],
            ['y' => 3],
        ]);
        $insertMany->execute($this->getPrimaryServer());

        $createIndexes = new CreateIndexes($this->getDatabaseName(), $this->getCollectionName(), [
            ['key' => ['x' => 1], 'sparse' => true, 'name' => 'sparse_x'],
            ['key' => ['y' => 1]],
        ]);
        $createIndexes->execute($this->getPrimaryServer());

        $hintsUsingSparseIndex = [
            ['x' => 1],
            'sparse_x',
        ];

        foreach ($hintsUsingSparseIndex as $hint) {
            $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), [], ['hint' => $hint]);
            $this->assertSame(2, $operation->execute($this->getPrimaryServer()));
        }

        $hintsNotUsingSparseIndex = [
            ['_id' => 1],
            ['y' => 1],
            'y_1',
        ];

        foreach ($hintsNotUsingSparseIndex as $hint) {
            $operation = new Count($this->getDatabaseName(), $this->getCollectionName(), [], ['hint' => $hint]);
            $this->assertSame(3, $operation->execute($this->getPrimaryServer()));
        }
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new Count(
                    $this->getDatabaseName(),
                    $this->getCollectionName(),
                    [],
                    ['session' => $this->createSession()]
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            }
        );
    }
}
