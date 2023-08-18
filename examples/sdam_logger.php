<?php
declare(strict_types=1);

namespace MongoDB\Examples;

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

use function get_class;
use function getenv;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toRelaxedExtendedJSON;
use function printf;

require __DIR__ . '/../vendor/autoload.php';

/** @param array|object $document */
function toJSON($document): string
{
    return toRelaxedExtendedJSON(fromPHP($document));
}

class SDAMLogger implements SDAMSubscriber
{
    public function serverChanged(ServerChangedEvent $event): void
    {
        printf(
            "serverChanged: %s:%d changed from %s to %s\n",
            $event->getHost(),
            $event->getPort(),
            $event->getPreviousDescription()->getType(),
            $event->getNewDescription()->getType(),
        );

        printf("previous hello response: %s\n", toJson($event->getPreviousDescription()->getHelloResponse()));
        printf("new hello response: %s\n", toJson($event->getNewDescription()->getHelloResponse()));
        echo "\n";
    }

    public function serverClosed(ServerClosedEvent $event): void
    {
        printf(
            "serverClosed: %s:%d was removed from topology %s\n",
            $event->getHost(),
            $event->getPort(),
            (string) $event->getTopologyId(),
        );
        echo "\n";
    }

    public function serverHeartbeatFailed(ServerHeartbeatFailedEvent $event): void
    {
        printf(
            "serverHeartbeatFailed: %s:%d heartbeat failed after %dµs\n",
            $event->getHost(),
            $event->getPort(),
            $event->getDurationMicros(),
        );

        $error = $event->getError();

        printf("error: %s(%d): %s\n", get_class($error), $error->getCode(), $error->getMessage());
        echo "\n";
    }

    public function serverHeartbeatStarted(ServerHeartbeatStartedEvent $event): void
    {
        printf(
            "serverHeartbeatStarted: %s:%d heartbeat started\n",
            $event->getHost(),
            $event->getPort(),
        );
        echo "\n";
    }

    public function serverHeartbeatSucceeded(ServerHeartbeatSucceededEvent $event): void
    {
        printf(
            "serverHeartbeatSucceeded: %s:%d heartbeat succeeded after %dµs\n",
            $event->getHost(),
            $event->getPort(),
            $event->getDurationMicros(),
        );

        printf("reply: %s\n", toJson($event->getReply()));
        echo "\n";
    }

    public function serverOpening(ServerOpeningEvent $event): void
    {
        printf(
            "serverOpening: %s:%d was added to topology %s\n",
            $event->getHost(),
            $event->getPort(),
            (string) $event->getTopologyId(),
        );
        echo "\n";
    }

    public function topologyChanged(TopologyChangedEvent $event): void
    {
        printf(
            "topologyChanged: %s changed from %s to %s\n",
            (string) $event->getTopologyId(),
            $event->getPreviousDescription()->getType(),
            $event->getNewDescription()->getType(),
        );
        echo "\n";
    }

    public function topologyClosed(TopologyClosedEvent $event): void
    {
        printf("topologyClosed: %s was closed\n", (string) $event->getTopologyId());
        echo "\n";
    }

    public function topologyOpening(TopologyOpeningEvent $event): void
    {
        printf("topologyOpening: %s was opened\n", (string) $event->getTopologyId());
        echo "\n";
    }
}

/* Note: TopologyClosedEvent can only be observed for non-persistent clients.
 * Persistent clients are destroyed in GSHUTDOWN, long after any PHP objects
 * (including subscribers) are freed. */
$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/', [], ['disableClientPersistence' => true]);

$client->getManager()->addSubscriber(new SDAMLogger());

$client->test->command(['ping' => 1]);

/* Events dispatched during mongoc_client_destroy can only be observed before
 * RSHUTDOWN. Observing TopologyClosedEvent requires using a non-persistent
 * client and freeing it before the script ends. */
unset($client);
