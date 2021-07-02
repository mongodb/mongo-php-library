<?php

namespace MongoDB\Tests;

use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use Throwable;

use function call_user_func;
use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;

/**
 * Observes command documents using the driver's monitoring API.
 */
class CommandObserver implements CommandSubscriber
{
    /** @var array */
    private $commands = [];

    public function observe(callable $execution, callable $commandCallback): void
    {
        $this->commands = [];

        addSubscriber($this);

        try {
            call_user_func($execution);
        } catch (Throwable $executionException) {
        }

        removeSubscriber($this);

        foreach ($this->commands as $command) {
            call_user_func($commandCallback, $command);
        }

        if (isset($executionException)) {
            throw $executionException;
        }
    }

    public function commandStarted(CommandStartedEvent $event): void
    {
        $this->commands[$event->getRequestId()]['started'] = $event;
    }

    public function commandSucceeded(CommandSucceededEvent $event): void
    {
        $this->commands[$event->getRequestId()]['succeeded'] = $event;
    }

    public function commandFailed(CommandFailedEvent $event): void
    {
        $this->commands[$event->getRequestId()]['failed'] = $event;
    }
}
