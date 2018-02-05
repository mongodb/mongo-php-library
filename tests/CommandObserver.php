<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use Exception;

/**
 * Observes command documents using the driver's monitoring API.
 */
class CommandObserver implements CommandSubscriber
{
    private $commands = [];

    public function observe(callable $execution, callable $commandCallback)
    {
        $this->commands = [];

        \MongoDB\Driver\Monitoring\addSubscriber($this);

        try {
            call_user_func($execution);
        } catch (Exception $executionException) {}

        \MongoDB\Driver\Monitoring\removeSubscriber($this);

        foreach ($this->commands as $command) {
            call_user_func($commandCallback, $command);
        }

        if (isset($executionException)) {
            throw $executionException;
        }
    }

    public function commandStarted(CommandStartedEvent $event)
    {
        $this->commands[] = $event->getCommand();
    }

    public function commandSucceeded(CommandSucceededEvent $event)
    {
    }

    public function commandFailed(CommandFailedEvent $event)
    {
    }
}
