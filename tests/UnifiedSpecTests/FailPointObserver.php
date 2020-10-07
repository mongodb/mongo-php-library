<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Operation\DatabaseCommand;
use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;

class FailPointObserver implements CommandSubscriber
{
    /** @var array */
    private $failPointsAndServers = [];

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php
     */
    public function commandFailed(CommandFailedEvent $event)
    {
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandstarted.php
     */
    public function commandStarted(CommandStartedEvent $event)
    {
        $command = $event->getCommand();

        if (! isset($command->configureFailPoint)) {
            return;
        }

        if (isset($command->mode) && $command->mode === 'off') {
            return;
        }

        $this->failPointsAndServers[] = [$command->configureFailPoint, $event->getServer()];
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandsucceeded.php
     */
    public function commandSucceeded(CommandSucceededEvent $event)
    {
    }

    public function disableFailPoints()
    {
        foreach ($this->failPointsAndServers as list($failPoint, $server)) {
            $operation = new DatabaseCommand('admin', ['configureFailPoint' => $failPoint, 'mode' => 'off']);
            $operation->execute($server);
        }

        $this->failPointsAndServers = [];
    }

    public function start()
    {
        addSubscriber($this);
    }

    public function stop()
    {
        removeSubscriber($this);
    }
}
