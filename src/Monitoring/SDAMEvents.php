<?php

namespace MongoDB\Monitoring;

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

/** @see SDAMSubscriber */
trait SDAMEvents
{
    public function serverChanged(ServerChangedEvent $event): void
    {
    }

    public function serverClosed(ServerClosedEvent $event): void
    {
    }

    public function serverHeartbeatFailed(ServerHeartbeatFailedEvent $event): void
    {
    }

    public function serverHeartbeatStarted(ServerHeartbeatStartedEvent $event): void
    {
    }

    public function serverHeartbeatSucceeded(ServerHeartbeatSucceededEvent $event): void
    {
    }

    public function serverOpening(ServerOpeningEvent $event): void
    {
    }

    public function topologyChanged(TopologyChangedEvent $event): void
    {
    }

    public function topologyClosed(TopologyClosedEvent $event): void
    {
    }

    public function topologyOpening(TopologyOpeningEvent $event): void
    {
    }
}
