<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Server;
use MongoDB\Operation\DatabaseCommand;
use stdClass;

use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertObjectHasAttribute;

trait ManagesFailPointsTrait
{
    /** @var list<list{string, Server}> */
    private array $failPointsAndServers = [];

    public function configureFailPoint(stdClass $failPoint, Server $server): void
    {
        assertObjectHasAttribute('configureFailPoint', $failPoint);
        assertIsString($failPoint->configureFailPoint);
        assertObjectHasAttribute('mode', $failPoint);

        $operation = new DatabaseCommand('admin', $failPoint);
        $operation->execute($server);

        if ($failPoint->mode !== 'off') {
            $this->failPointsAndServers[] = [$failPoint->configureFailPoint, $server];
        }
    }

    public function disableFailPoints(): void
    {
        foreach ($this->failPointsAndServers as [$failPoint, $server]) {
            $operation = new DatabaseCommand('admin', ['configureFailPoint' => $failPoint, 'mode' => 'off']);
            $operation->execute($server);
        }

        $this->failPointsAndServers = [];
    }
}
