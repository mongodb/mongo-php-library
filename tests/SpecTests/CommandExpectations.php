<?php

namespace MongoDB\Tests\SpecTests;

use ArrayIterator;
use LogicException;
use MongoDB\Client;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MultipleIterator;

use function count;
use function in_array;
use function key;

/**
 * Spec test CommandStartedEvent expectations.
 */
class CommandExpectations implements CommandSubscriber
{
    private array $actualEvents = [];

    private array $expectedEvents = [];

    private bool $ignoreCommandFailed = false;

    private bool $ignoreCommandStarted = false;

    private bool $ignoreCommandSucceeded = false;

    private bool $ignoreExtraEvents = false;

    private bool $ignoreKeyVaultListCollections = false;

    /** @var list<string> */
    private array $ignoredCommandNames = [];

    private function __construct(private Client $observedClient, array $events)
    {
        foreach ($events as $event) {
            $this->expectedEvents[] = match (key((array) $event)) {
                // phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
                'command_failed_event' => [$event->command_failed_event, CommandFailedEvent::class],
                // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
                'command_started_event' => [$event->command_started_event, CommandStartedEvent::class],
                // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
                'command_succeeded_event' => [$event->command_succeeded_event, CommandSucceededEvent::class],
                default => throw new LogicException('Unsupported event type: ' . key($event)),
            };
        }
    }

    public static function fromClientSideEncryption(Client $client, array $expectedEvents)
    {
        $o = new self($client, $expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;
        $o->ignoreKeyVaultListCollections = true;

        return $o;
    }

    public static function fromCrud(Client $client, array $expectedEvents)
    {
        $o = new self($client, $expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        return $o;
    }

    public static function fromReadWriteConcern(Client $client, array $expectedEvents)
    {
        $o = new self($client, $expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        return $o;
    }

    public static function fromRetryableReads(Client $client, array $expectedEvents)
    {
        $o = new self($client, $expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        /* Retryable read spec tests don't include extra commands, e.g. the
         * killCursors command issued when a change stream is garbage collected.
         * We ignore any extra events for that reason. \*/
        $o->ignoreExtraEvents = true;

        return $o;
    }

    public static function fromTransactions(Client $client, array $expectedEvents)
    {
        $o = new self($client, $expectedEvents);

        $o->ignoreCommandFailed = true;
        $o->ignoreCommandSucceeded = true;

        /* Ignore the buildInfo and getParameter commands as they are used to
         * check for the availability of configureFailPoint and are not expected
         * to be called by any spec tests.
         * configureFailPoint needs to be ignored as the targetedFailPoint
         * operation will be caught by command monitoring and is also not
         * present in the expected commands in spec tests. */
        $o->ignoredCommandNames = ['buildInfo', 'getParameter', 'configureFailPoint', 'listCollections', 'listIndexes'];

        return $o;
    }

    /**
     * Not used.
     *
     * @see https://php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php
     */
    public function commandFailed(CommandFailedEvent $event): void
    {
        if ($this->ignoreCommandFailed || $this->isEventIgnored($event)) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    /**
     * Tracks outgoing commands for spec test APM assertions.
     *
     * @see https://php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandstarted.php
     */
    public function commandStarted(CommandStartedEvent $event): void
    {
        if ($this->ignoreCommandStarted || $this->isEventIgnored($event)) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    /**
     * Not used.
     *
     * @see https://php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandsucceeded.php
     */
    public function commandSucceeded(CommandSucceededEvent $event): void
    {
        if ($this->ignoreCommandSucceeded || $this->isEventIgnored($event)) {
            return;
        }

        $this->actualEvents[] = $event;
    }

    /**
     * Start command monitoring.
     */
    public function startMonitoring(): void
    {
        $this->observedClient->getManager()->addSubscriber($this);
    }

    /**
     * Stop command monitoring.
     */
    public function stopMonitoring(): void
    {
        $this->observedClient->getManager()->removeSubscriber($this);
    }

    /**
     * Assert that the command expectations match the monitored events.
     */
    public function assert(FunctionalTestCase $test, Context $context): void
    {
        $test->assertCount(count($this->expectedEvents), $this->actualEvents);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($this->expectedEvents));
        $mi->attachIterator(new ArrayIterator($this->actualEvents));

        foreach ($mi as $events) {
            [$expectedEventAndClass, $actualEvent] = $events;
            [$expectedEvent, $expectedClass] = $expectedEventAndClass;

            $test->assertInstanceOf($expectedClass, $actualEvent);

            // phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            if (isset($expectedEvent->command_name)) {
                $test->assertSame($expectedEvent->command_name, $actualEvent->getCommandName());
            }

            if (isset($expectedEvent->database_name)) {
                $test->assertSame($expectedEvent->database_name, $actualEvent->getDatabaseName());
            }

            // phpcs:enable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

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
        if ($this->ignoreExtraEvents && count($this->actualEvents) === count($this->expectedEvents)) {
            return true;
        }

        if (in_array($event->getCommandName(), $this->ignoredCommandNames)) {
            return true;
        }

        /* Note: libmongoc does not use a separate MongoClient to query for
         * CSFLE metadata (DRIVERS-1459). Since the tests do not expect this
         * command, we must ignore it. */
        if (
            $this->ignoreKeyVaultListCollections && $event instanceof CommandStartedEvent &&
            $event->getCommandName() === 'listCollections' && $event->getDatabaseName() === 'keyvault'
        ) {
            return true;
        }

        return false;
    }
}
