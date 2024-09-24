<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Model\BSONArray;

use function array_filter;
use function array_flip;
use function microtime;
use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertNotEmpty;

/**
 * EventCollector handles "storeEventsAsEntities" for client entities.
 *
 * Unlike EventObserver, this does not support ignoring command monitoring
 * events for specific commands. That said, internal/security commands that
 * bypass command monitoring will still be ignored.
 */
final class EventCollector implements CommandSubscriber
{
    private static array $supportedEvents = [
        'PoolCreatedEvent' => null,
        'PoolReadyEvent' => null,
        'PoolClearedEvent' => null,
        'PoolClosedEvent' => null,
        'ConnectionCreatedEvent' => null,
        'ConnectionReadyEvent' => null,
        'ConnectionClosedEvent' => null,
        'ConnectionCheckOutStartedEvent' => null,
        'ConnectionCheckOutFailedEvent' => null,
        'ConnectionCheckedOutEvent' => null,
        'ConnectionCheckedInEvent' => null,
        'CommandStartedEvent' => CommandStartedEvent::class,
        'CommandSucceededEvent' => CommandSucceededEvent::class,
        'CommandFailedEvent' => CommandFailedEvent::class,
    ];

    private array $collectEvents = [];

    public function __construct(private BSONArray $eventList, array $collectEvents, private string $clientId, private Context $context)
    {
        assertNotEmpty($collectEvents);

        foreach ($collectEvents as $event) {
            assertIsString($event);
            assertArrayHasKey($event, self::$supportedEvents);

            /* CMAP events are "supported" only in the sense that we recognize
             * them in the test format; however, PHPC does not implement
             * connection pooling so these events cannot be collected. */
            if (self::$supportedEvents[$event] !== null) {
                $this->collectEvents[self::$supportedEvents[$event]] = 1;
            }
        }
    }

    /** @see https://php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandfailed.php */
    public function commandFailed(CommandFailedEvent $event): void
    {
        $this->handleCommandMonitoringEvent($event);
    }

    /** @see https://php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandstarted.php */
    public function commandStarted(CommandStartedEvent $event): void
    {
        $this->handleCommandMonitoringEvent($event);
    }

    /** @see https://php.net/manual/en/mongodb-driver-monitoring-commandsubscriber.commandsucceeded.php */
    public function commandSucceeded(CommandSucceededEvent $event): void
    {
        $this->handleCommandMonitoringEvent($event);
    }

    public function start(): void
    {
        addSubscriber($this);
    }

    public function stop(): void
    {
        removeSubscriber($this);
    }

    private function handleCommandMonitoringEvent(CommandStartedEvent|CommandSucceededEvent|CommandFailedEvent $event): void
    {
        assertIsObject($event);

        if (! $this->context->isActiveClient($this->clientId)) {
            return;
        }

        if (! isset($this->collectEvents[$event::class])) {
            return;
        }

        $log = [
            'name' => self::getEventName($event),
            'observedAt' => microtime(true),
            'commandName' => $event->getCommandName(),
            'databaseName' => $event->getDatabaseName(),
            'operationId' => $event->getOperationId(),
            'requestId' => $event->getRequestId(),
            'server' => $event->getHost() . ':' . $event->getPort(),
            'serverConnectionId' => $event->getServerConnectionId(),
            'serviceId' => $event->getServiceId(),
        ];

        /* Note: CommandStartedEvent.command and CommandSucceededEvent.reply can
         * be omitted from logged events. */

        if ($event instanceof CommandSucceededEvent) {
            $log['duration'] = $event->getDurationMicros();
        }

        if ($event instanceof CommandFailedEvent) {
            $log['failure'] = $event->getError()->getMessage();
            $log['duration'] = $event->getDurationMicros();
        }

        $this->eventList[] = $log;
    }

    private static function getEventName(object $event): string
    {
        static $eventNamesByClass = null;

        if ($eventNamesByClass === null) {
            $eventNamesByClass = array_flip(array_filter(self::$supportedEvents));
        }

        return $eventNamesByClass[$event::class];
    }
}
