<?php
declare(strict_types=1);

namespace MongoDB\Examples;

use Closure;
use Exception;
use MongoDB\BSON\Document;
use MongoDB\Client;
use MongoDB\Driver\Monitoring\SDAMSubscriber;
use MongoDB\Driver\Monitoring\ServerChangedEvent;
use MongoDB\Driver\Monitoring\ServerClosedEvent;
use MongoDB\Driver\Monitoring\ServerHeartbeatFailedEvent;
use MongoDB\Driver\Monitoring\ServerHeartbeatStartedEvent;
use MongoDB\Driver\Monitoring\ServerHeartbeatSucceededEvent;
use MongoDB\Driver\Monitoring\ServerOpeningEvent;
use MongoDB\Driver\Monitoring\TopologyChangedEvent;
use MongoDB\Driver\Monitoring\TopologyClosedEvent;
use MongoDB\Driver\Monitoring\TopologyOpeningEvent;

use function getenv;
use function printf;

require __DIR__ . '/../vendor/autoload.php';

function toJSON(array|object $document): string
{
    return Document::fromPHP($document)->toRelaxedExtendedJSON();
}

class SDAMLogger implements SDAMSubscriber
{
    /** @param Closure(object):void $handleOutput */
    public function __construct(private readonly Closure $handleOutput)
    {
    }

    public function serverChanged(ServerChangedEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }

    public function serverClosed(ServerClosedEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }

    public function serverHeartbeatFailed(ServerHeartbeatFailedEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }

    public function serverHeartbeatStarted(ServerHeartbeatStartedEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }

    public function serverHeartbeatSucceeded(ServerHeartbeatSucceededEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }

    public function serverOpening(ServerOpeningEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }

    public function topologyChanged(TopologyChangedEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }

    public function topologyClosed(TopologyClosedEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }

    public function topologyOpening(TopologyOpeningEvent $event): void
    {
        $this->handleOutput->__invoke($event);
    }
}

/* Note: TopologyClosedEvent can only be observed for non-persistent clients.
 * Persistent clients are destroyed in GSHUTDOWN, long after any PHP objects
 * (including subscribers) are freed. */
$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/', [], ['disableClientPersistence' => true]);

$handleOutput = function (object $event): void {
    switch ($event::class) {
        case ServerChangedEvent::class:
            printf(
                "serverChanged: %s:%d changed from %s to %s\n",
                $event->getHost(),
                $event->getPort(),
                $event->getPreviousDescription()->getType(),
                $event->getNewDescription()->getType(),
            );

            printf("previous hello response: %s\n", toJson($event->getPreviousDescription()->getHelloResponse()));
            printf("new hello response: %s\n", toJson($event->getNewDescription()->getHelloResponse()));
            break;
        case ServerClosedEvent::class:
            printf(
                "serverClosed: %s:%d was removed from topology %s\n",
                $event->getHost(),
                $event->getPort(),
                $event->getTopologyId()->__toString(),
            );
            break;
        case ServerHeartbeatFailedEvent::class:
            printf(
                "serverHeartbeatFailed: %s:%d heartbeat failed after %dµs\n",
                $event->getHost(),
                $event->getPort(),
                $event->getDurationMicros(),
            );

            $error = $event->getError();

            printf("error: %s(%d): %s\n", $error::class, $error->getCode(), $error->getMessage());
            break;
        case ServerHeartbeatStartedEvent::class:
            printf(
                "serverHeartbeatStarted: %s:%d heartbeat started\n",
                $event->getHost(),
                $event->getPort(),
            );
            break;
        case ServerHeartbeatSucceededEvent::class:
            printf(
                "serverHeartbeatSucceeded: %s:%d heartbeat succeeded after %dµs\n",
                $event->getHost(),
                $event->getPort(),
                $event->getDurationMicros(),
            );

            printf("reply: %s\n", toJson($event->getReply()));
            break;
        case ServerOpeningEvent::class:
            printf(
                "serverOpening: %s:%d was added to topology %s\n",
                $event->getHost(),
                $event->getPort(),
                $event->getTopologyId()->__toString(),
            );
            break;
        case TopologyChangedEvent::class:
            printf(
                "topologyChanged: %s changed from %s to %s\n",
                $event->getTopologyId()->__toString(),
                $event->getPreviousDescription()->getType(),
                $event->getNewDescription()->getType(),
            );
            break;
        case TopologyClosedEvent::class:
            printf("topologyClosed: %s was closed\n", $event->getTopologyId()->__toString());
            break;
        case TopologyOpeningEvent::class:
            printf("topologyOpening: %s was opened\n", $event->getTopologyId()->__toString());
            break;
        default:
            throw new Exception('Event type not supported');
    }

    echo "\n";
};

$client->getManager()->addSubscriber(new SDAMLogger($handleOutput));

$client->test->command(['ping' => 1]);

/* Events dispatched during mongoc_client_destroy can only be observed before
 * RSHUTDOWN. Observing TopologyClosedEvent requires using a non-persistent
 * client and freeing it before the script ends. */
unset($client);
