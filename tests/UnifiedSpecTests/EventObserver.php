<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;

class EventObserver implements CommandSubscriber
{
    private static $defaultIgnoreCommands = [
        // failPoint and targetedFailPoint operations
        'configureFailPoint',
        // See: https://github.com/mongodb/specifications/blob/master/source/command-monitoring/command-monitoring.rst#security
        'authenticate',
        'saslStart',
        'saslContinue',
        'getnonce',
        'createUser',
        'updateUser',
        'copydbgetnonce',
        'copydbsaslstart',
        'copydb',
        'isMaster',
    ];

    private $actualEvents = [];
    private $ignoreCommands = [];
    private $observeEvents = [];

    private static $commandMonitoringEvents = [
        'commandStartedEvent' => CommandStartedEvent::class,
        'commandSucceededEvent' => CommandSucceededEvent::class,
        'commandFailedEvent' => CommandFailedEvent::class,
    ];

    public function __construct(array $observeEvents, array $ignoreCommands)
    {
        assertNotEmpty($observeEvents);

        foreach ($observeEvents as $event) {
            assertInternalType('string', $event);
            assertArrayHasKey($event, self::$commandMonitoringEvents);
            $this->observeEvents[self::$commandMonitoringEvents[$event]] = 1;
        }

        $this->ignoreCommands = array_fill_keys(self::$defaultIgnoreCommands, 1);

        foreach ($ignoreCommands as $command) {
            assertInternalType('string', $command);
            $this->ignoreCommands[$command] = 1;
        }
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php
     */
    public function commandFailed(CommandFailedEvent $event)
    {
        if (! isset($this->observeEvents[CommandFailedEvent::class])) {
            return;
        }

        if (isset($this->ignoreCommands[$event->getCommandName()])) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandstarted.php
     */
    public function commandStarted(CommandStartedEvent $event)
    {
        if (! isset($this->observeEvents[CommandStartedEvent::class])) {
            return;
        }

        if (isset($this->ignoreCommands[$event->getCommandName()])) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandsucceeded.php
     */
    public function commandSucceeded(CommandSucceededEvent $event)
    {
        if (! isset($this->observeEvents[CommandSucceededEvent::class])) {
            return;
        }

        if (isset($this->ignoreCommands[$event->getCommandName()])) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    public function getActualEvents()
    {
        return $this->actualEvents;
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
