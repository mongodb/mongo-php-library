<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Server;
use MongoDB\Operation\DatabaseCommand;
use stdClass;

use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertObjectHasProperty;

trait ManagesFailPointsTrait
{
    /** @var list<list{string, Server}> */
    private array $failPointsAndServers = [];

    public function configureFailPoint(stdClass $failPoint, Server $server): void
    {
        assertObjectHasProperty('configureFailPoint', $failPoint);
        assertIsString($failPoint->configureFailPoint);
        assertObjectHasProperty('mode', $failPoint);

        $operation = new DatabaseCommand('admin', $failPoint);
        $operation->execute($server);

        if ($failPoint->mode !== 'off') {
            $this->failPointsAndServers[] = [$failPoint->configureFailPoint, $server];
        }
    }

    public function disableFailPoints(): void
    {
        foreach ($this->failPointsAndServers as [$failPoint, $server]) {
            try {
                $operation = new DatabaseCommand('admin', ['configureFailPoint' => $failPoint, 'mode' => 'off']);
                $operation->execute($server);
            } catch (ConnectionException) {
                // Retry once in case the connection was dropped by the last operation
                $operation = new DatabaseCommand('admin', ['configureFailPoint' => $failPoint, 'mode' => 'off']);
                $operation->execute($server);
            }
        }

        $this->failPointsAndServers = [];
    }
}
