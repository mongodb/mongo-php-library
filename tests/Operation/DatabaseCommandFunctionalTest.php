<?php

namespace MongoDB\Tests\Operation;

use MongoDB\BSON\Document;
use MongoDB\Driver\Command;
use MongoDB\Model\BSONDocument;
use MongoDB\Operation\DatabaseCommand;
use MongoDB\Tests\CommandObserver;

class DatabaseCommandFunctionalTest extends FunctionalTestCase
{
    /** @dataProvider provideCommandDocuments */
    public function testCommandDocuments($command): void
    {
        (new CommandObserver())->observe(
            function () use ($command): void {
                $operation = new DatabaseCommand(
                    $this->getDatabaseName(),
                    $command,
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertEquals(1, $event['started']->getCommand()->ping ?? null);
            },
        );
    }

    public function provideCommandDocuments(): array
    {
        return [
            'array' => [['ping' => 1]],
            'object' => [(object) ['ping' => 1]],
            'Serializable' => [new BSONDocument(['ping' => 1])],
            'Document' => [Document::fromPHP(['ping' => 1])],
            'Command:array' => [new Command(['ping' => 1])],
            'Command:object' => [new Command((object) ['ping' => 1])],
            'Command:Serializable' => [new Command(new BSONDocument(['ping' => 1]))],
            'Command:Document' => [new Command(Document::fromPHP(['ping' => 1]))],
        ];
    }

    public function testSessionOption(): void
    {
        (new CommandObserver())->observe(
            function (): void {
                $operation = new DatabaseCommand(
                    $this->getDatabaseName(),
                    ['ping' => 1],
                    ['session' => $this->createSession()],
                );

                $operation->execute($this->getPrimaryServer());
            },
            function (array $event): void {
                $this->assertObjectHasAttribute('lsid', $event['started']->getCommand());
            },
        );
    }
}
