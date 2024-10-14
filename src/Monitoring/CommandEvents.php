<?php

namespace MongoDB\Monitoring;

use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;

/** @see CommandSubscriber */
trait CommandEvents
{
    public function commandFailed(CommandFailedEvent $event): void
    {
    }

    public function commandStarted(CommandStartedEvent $event): void
    {
    }

    public function commandSucceeded(CommandSucceededEvent $event): void
    {
    }
}
