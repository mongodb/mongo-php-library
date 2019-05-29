<?php

namespace MongoDB\Tests\SpecTests;

use MongoDB\BSON\Timestamp;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use ArrayIterator;
use LogicException;
use MultipleIterator;
use stdClass;

/**
 * Spec test CommandStartedEvent expectations.
 */
class CommandExpectations implements CommandSubscriber
{
    private $commandStartedEvents = [];
    private $expectedCommandStartedEvents = [];

    public static function fromTransactions(array $expectedEvents)
    {
        $o = new self;

        foreach ($expectedEvents as $expectedEvent) {
            if (!isset($expectedEvent->command_started_event)) {
                throw new LogicException('$expectedEvent->command_started_event field is not set');
            }

            $o->expectedCommandStartedEvents[] = $expectedEvent->command_started_event;
        }

        return $o;
    }

    /**
     * Not used.
     *
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php
     */
    public function commandFailed(CommandFailedEvent $event)
    {
    }

    /**
     * Tracks outgoing commands for spec test APM assertions.
     *
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandstarted.php
     */
    public function commandStarted(CommandStartedEvent $event)
    {
        $this->commandStartedEvents[] = $event;
    }

    /**
     * Not used.
     *
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandsucceeded.php
     */
    public function commandSucceeded(CommandSucceededEvent $event)
    {
    }

    /**
     * Start command monitoring.
     */
    public function startMonitoring()
    {
        \MongoDB\Driver\Monitoring\addSubscriber($this);
    }

    /**
     * Stop command monitoring.
     */
    public function stopMonitoring()
    {
        \MongoDB\Driver\Monitoring\removeSubscriber($this);
    }

    /**
     * Assert that the command expectations match the monitored events.
     *
     * @param FunctionalTestCase $test    Test instance
     * @param Context            $context Execution context
     */
    public function assert(FunctionalTestCase $test, Context $context)
    {
        $test->assertCount(count($this->expectedCommandStartedEvents), $this->commandStartedEvents);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($this->expectedCommandStartedEvents));
        $mi->attachIterator(new ArrayIterator($this->commandStartedEvents));

        foreach ($mi as $events) {
            list($expectedEvent, $actualEvent) = $events;
            $test->assertInternalType('object', $expectedEvent);
            $test->assertInstanceOf(CommandStartedEvent::class, $actualEvent);

            if (isset($expectedEvent->command_name)) {
                $test->assertSame($expectedEvent->command_name, $actualEvent->getCommandName());
            }

            if (isset($expectedEvent->database_name)) {
                $test->assertSame($expectedEvent->database_name, $actualEvent->getDatabaseName());
            }

            if (isset($expectedEvent->command)) {
                $expectedCommand = $expectedEvent->command;
                $context->replaceCommandSessionPlaceholder($expectedCommand);
                $test->assertSameCommand($expectedCommand, $actualEvent->getCommand());
            }
        }
    }

    private function __construct()
    {
    }
}
