<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use stdClass;
use function in_array;
use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;

/**
 * Observes whether a session is used in an command that encounters a network
 * error. This is primarily used to infer whether a sesson will be marked dirty
 * in libmongoc.
 *
 * TODO: Remove this once CDRIVER-3780 and PHPLIB-528 are implemented.
 */
class DirtySessionObserver implements CommandSubscriber
{
    /** @var stdClass */
    private $lsid;

    /** @var array */
    private $requestIds = [];

    /** @var bool */
    private $observedNetworkError = false;

    public function __construct(stdClass $lsid)
    {
        $this->lsid = $lsid;
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php
     */
    public function commandFailed(CommandFailedEvent $event)
    {
        if (! in_array($event->getRequestId(), $this->requestIds)) {
            return;
        }

        if ($event->getError() instanceof ConnectionException) {
            $this->observedNetworkError = true;
        }
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandstarted.php
     */
    public function commandStarted(CommandStartedEvent $event)
    {
        if ($this->lsid == ($event->getCommand()->lsid ?? null)) {
            $this->requestIds[] = $event->getRequestId();
        }
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandsucceeded.php
     */
    public function commandSucceeded(CommandSucceededEvent $event)
    {
    }

    public function observedNetworkError() : bool
    {
        return $this->observedNetworkError;
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
