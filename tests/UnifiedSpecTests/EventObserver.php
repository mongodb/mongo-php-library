<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use ArrayIterator;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Tests\UnifiedSpecTests\Constraint\Matches;
use MultipleIterator;
use PHPUnit\Framework\Assert;
use stdClass;
use function array_fill_keys;
use function array_reverse;
use function count;
use function current;
use function get_class;
use function is_object;
use function key;
use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertObjectHasAttribute;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertThat;
use function sprintf;

class EventObserver implements CommandSubscriber
{
    /** @var array */
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

    /** @var array */
    private static $supportedEvents = [
        'commandStartedEvent' => CommandStartedEvent::class,
        'commandSucceededEvent' => CommandSucceededEvent::class,
        'commandFailedEvent' => CommandFailedEvent::class,
    ];

    /** @var array */
    private $actualEvents = [];

    /** @var string */
    private $clientId;

    /** @var Context */
    private $context;

    /** @var array */
    private $ignoreCommands = [];

    /** @var array */
    private $observeEvents = [];

    public function __construct(array $observeEvents, array $ignoreCommands, string $clientId, Context $context)
    {
        assertNotEmpty($observeEvents);

        foreach ($observeEvents as $event) {
            assertIsString($event);
            assertArrayHasKey($event, self::$supportedEvents);
            $this->observeEvents[self::$supportedEvents[$event]] = 1;
        }

        $this->ignoreCommands = array_fill_keys(self::$defaultIgnoreCommands, 1);

        foreach ($ignoreCommands as $command) {
            assertIsString($command);
            $this->ignoreCommands[$command] = 1;
        }

        $this->clientId = $clientId;
        $this->context = $context;
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php
     */
    public function commandFailed(CommandFailedEvent $event)
    {
        $this->handleEvent($event);
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandstarted.php
     */
    public function commandStarted(CommandStartedEvent $event)
    {
        $this->handleEvent($event);
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandsucceeded.php
     */
    public function commandSucceeded(CommandSucceededEvent $event)
    {
        $this->handleEvent($event);
    }

    public function start()
    {
        addSubscriber($this);
    }

    public function stop()
    {
        removeSubscriber($this);
    }

    public function getLsidsOnLastTwoCommands() : array
    {
        $lsids = [];

        foreach (array_reverse($this->actualEvents) as $event) {
            if (! $event instanceof CommandStartedEvent) {
                continue;
            }

            $command = $event->getCommand();
            assertObjectHasAttribute('lsid', $command);
            $lsids[] = $command->lsid;

            if (count($lsids) === 2) {
                return $lsids;
            }
        }

        Assert::fail('Not enough CommandStartedEvents observed');
    }

    public function assert(array $expectedEvents)
    {
        assertCount(count($expectedEvents), $this->actualEvents);

        $mi = new MultipleIterator(MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new ArrayIterator($expectedEvents));
        $mi->attachIterator(new ArrayIterator($this->actualEvents));

        foreach ($mi as $keys => $events) {
            list($expectedEvent, $actualEvent) = $events;

            assertIsObject($expectedEvent);
            $expectedEvent = (array) $expectedEvent;
            assertCount(1, $expectedEvent);

            $type = key($expectedEvent);
            assertArrayHasKey($type, self::$supportedEvents);
            $data = current($expectedEvent);
            assertIsObject($data);

            // Message is used for actual event assertions (not test structure)
            $message = sprintf('%s event[%d]', $this->clientId, $keys[0]);

            assertInstanceOf(self::$supportedEvents[$type], $actualEvent, $message . ': type matches');
            $this->assertEvent($actualEvent, $data, $message);
        }
    }

    private function assertEvent($actual, stdClass $expected, string $message)
    {
        assertIsObject($actual);

        switch (get_class($actual)) {
            case CommandStartedEvent::class:
                return $this->assertCommandStartedEvent($actual, $expected, $message);
            case CommandSucceededEvent::class:
                return $this->assertCommandSucceededEvent($actual, $expected, $message);
            case CommandFailedEvent::class:
                return $this->assertCommandFailedEvent($actual, $expected, $message);
            default:
                Assert::fail($message . ': Unsupported event type: ' . get_class($actual));
        }
    }

    private function assertCommandStartedEvent(CommandStartedEvent $actual, stdClass $expected, string $message)
    {
        Util::assertHasOnlyKeys($expected, ['command', 'commandName', 'databaseName']);

        if (isset($expected->command)) {
            assertIsObject($expected->command);
            $constraint = new Matches($expected->command, $this->context->getEntityMap());
            assertThat($actual->getCommand(), $constraint, $message . ': command matches');
        }

        if (isset($expected->commandName)) {
            assertIsString($expected->commandName);
            assertSame($actual->getCommandName(), $expected->commandName, $message . ': commandName matches');
        }

        if (isset($expected->databaseName)) {
            assertIsString($expected->databaseName);
            assertSame($actual->getDatabaseName(), $expected->databaseName, $message . ': databaseName matches');
        }
    }

    private function assertCommandSucceededEvent(CommandSucceededEvent $actual, stdClass $expected, string $message)
    {
        Util::assertHasOnlyKeys($expected, ['reply', 'commandName']);

        if (isset($expected->reply)) {
            assertIsObject($expected->reply);
            $constraint = new Matches($expected->reply, $this->context->getEntityMap());
            assertThat($actual->getReply(), $constraint, $message . ': reply matches');
        }

        if (isset($expected->commandName)) {
            assertIsString($expected->commandName);
            assertSame($actual->getCommandName(), $expected->commandName, $message . ': commandName matches');
        }
    }

    private function assertCommandFailedEvent(CommandFailedEvent $actual, stdClass $expected, string $message)
    {
        Util::assertHasOnlyKeys($expected, ['commandName']);

        if (isset($expected->commandName)) {
            assertIsString($expected->commandName);
            assertSame($actual->getCommandName(), $expected->commandName, $message . ': commandName matches');
        }
    }

    /** @param CommandStartedEvent|CommandSucceededEvent|CommandFailedEvent $event */
    private function handleEvent($event)
    {
        if (! $this->context->isActiveClient($this->clientId)) {
            return;
        }

        if (! is_object($event)) {
            return;
        }

        if (! isset($this->observeEvents[get_class($event)])) {
            return;
        }

        if (isset($this->ignoreCommands[$event->getCommandName()])) {
            return;
        }

        $this->actualEvents[] = $event;
    }
}
