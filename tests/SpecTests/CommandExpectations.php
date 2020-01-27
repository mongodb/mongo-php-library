<?php

namespace MongoDB\Tests\SpecTests;

use ArrayIterator;
use LogicException;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MultipleIterator;
use function count;
use function in_array;
use function key;
use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;

/**
 * Spec test CommandStartedEvent expectations.
 */
class CommandExpectations implements CommandSubscriber
{
    /** @var array */
    private $actualEvents = [];

    /** @var array */
    private $expectedEvents = [];

    /** @var boolean */
    private $ignoreCommandFailed = false;

    /** @var boolean */
    private $ignoreCommandStarted = false;

    /** @var boolean */
    private $ignoreCommandSucceeded = false;

    /** @var boolean */
    private $ignoreExtraEvents = false;

    /** @var string[] */
    private $ignoredCommandNames = [];

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
        $o->ignoreExtraEvents = true;

        return $o;
    }

    public static function fromClientSideEncryption(array $expectedEvents)
    {
        $o = new self($expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        return $o;
    }

    public static function fromCommandMonitoring(array $expectedEvents)
    {
        return new self($expectedEvents);
    }

    public static function fromCrud(array $expectedEvents)
    {
        $o = new self($expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        return $o;
    }

    public static function fromRetryableReads(array $expectedEvents)
    {
        $o = new self($expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        /* Retryable read spec tests don't include extra commands, e.g. the
         * killCursors command issued when a change stream is garbage collected.
         * We ignore any extra events for that reason. \*/
        $o->ignoreExtraEvents = true;

        return $o;
    }

    public static function fromTransactions(array $expectedEvents)
    {
        $o = new self($expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        /* Ignore the buildInfo and getParameter commands as they are used to
         * check for the availability of configureFailPoint and are not expected
         * to be called by any spec tests.
         * configureFailPoint needs to be ignored as the targetedFailPoint
         * operation will be caught by command monitoring and is also not
         * present in the expected commands in spec tests. */
        $o->ignoredCommandNames = ['buildInfo', 'getParameter', 'configureFailPoint'];

        return $o;
    }

    /**
     * Not used.
     *
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php
     */
    public function commandFailed(CommandFailedEvent $event)
    {
        if ($this->ignoreCommandFailed || $this->isEventIgnored($event)) {
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
        if ($this->ignoreCommandStarted || $this->isEventIgnored($event)) {
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
        if ($this->ignoreCommandSucceeded || $this->isEventIgnored($event)) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    /**
     * Start command monitoring.
     */
    public function startMonitoring()
    {
        addSubscriber($this);
    }

    /**
     * Stop command monitoring.
     */
    public function stopMonitoring()
    {
        removeSubscriber($this);
    }

    /**
     * Assert that the command expectations match the monitored events.
     *
     * @param FunctionalTestCase $test    Test instance
     * @param Context            $context Execution context
     */
    public function assert(FunctionalTestCase $test, Context $context)
    {
        $test->assertCount(count($this->expectedEvents), $this->actualEvents);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($this->expectedEvents));
        $mi->attachIterator(new ArrayIterator($this->actualEvents));

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

    private function isEventIgnored($event)
    {
        return ($this->ignoreExtraEvents && count($this->actualEvents) === count($this->expectedEvents))
            || in_array($event->getCommandName(), $this->ignoredCommandNames);
    }
}
