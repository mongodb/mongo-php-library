<?php

namespace MongoDB\Tests\SpecTests;

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
    private $actualEvents = [];
    private $expectedEvents = [];
    private $ignoreCommandFailed = false;
    private $ignoreCommandStarted = false;
    private $ignoreCommandSucceeded = false;
    private $ignoreExtraEvents = false;

    private function __construct(array $events)
    {
        foreach ($events as $event) {
            switch (key($event)) {
                case 'command_failed_event':
                    $this->expectedEvents[] = [$event->command_failed_event, CommandFailedEvent::class];
                    break;

                case 'command_started_event':
                    $this->expectedEvents[] = [$event->command_started_event, CommandStartedEvent::class];
                    break;

                case 'command_succeeded_event':
                    $this->expectedEvents[] = [$event->command_succeeded_event, CommandSucceededEvent::class];
                    break;

                default:
                    throw new LogicException('Unsupported event type: ' . key($event));
            }
        }
    }

    public static function fromChangeStreams(array $expectedEvents)
    {
        $o = new self($expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;
        /* Change Streams spec tests do not include getMore commands in the
         * list of expected events, so ignore any observed events beyond the
         * number that are expected. */
        $o->ignoreExtraEvents = true;;

        return $o;
    }

    public static function fromCommandMonitoring(array $expectedEvents)
    {
        return new self($expectedEvents);
    }

    public static function fromTransactions(array $expectedEvents)
    {
        $o = new self($expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        return $o;
    }

    /**
     * Not used.
     *
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php
     */
    public function commandFailed(CommandFailedEvent $event)
    {
        if ($this->ignoreCommandFailed) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    /**
     * Tracks outgoing commands for spec test APM assertions.
     *
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandstarted.php
     */
    public function commandStarted(CommandStartedEvent $event)
    {
        if ($this->ignoreCommandStarted) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    /**
     * Not used.
     *
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandsucceeded.php
     */
    public function commandSucceeded(CommandSucceededEvent $event)
    {
        if ($this->ignoreCommandSucceeded) {
            return;
        }

        $this->actualEvents[] = $event;
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
        $actualEvents = $this->ignoreExtraEvents
            ? array_slice($this->actualEvents, 0, count($this->expectedEvents))
            : $this->actualEvents;

        $test->assertCount(count($this->expectedEvents), $actualEvents);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($this->expectedEvents));
        $mi->attachIterator(new ArrayIterator($actualEvents));

        foreach ($mi as $events) {
            list($expectedEventAndClass, $actualEvent) = $events;
            list($expectedEvent, $expectedClass) = $expectedEventAndClass;

            $test->assertInstanceOf($expectedClass, $actualEvent);

            if (isset($expectedEvent->command_name)) {
                $test->assertSame($expectedEvent->command_name, $actualEvent->getCommandName());
            }

            if (isset($expectedEvent->database_name)) {
                $test->assertSame($expectedEvent->database_name, $actualEvent->getDatabaseName());
            }

            if (isset($expectedEvent->command)) {
                $test->assertInstanceOf(CommandStartedEvent::class, $actualEvent);
                $expectedCommand = $expectedEvent->command;
                $context->replaceCommandSessionPlaceholder($expectedCommand);
                $test->assertCommandMatches($expectedCommand, $actualEvent->getCommand());
            }

            if (isset($expectedEvent->reply)) {
                $test->assertInstanceOf(CommandSucceededEvent::class, $actualEvent);
                $test->assertCommandReplyMatches($expectedEvent->reply, $actualEvent->getReply());
            }
        }
    }
}
