<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\BSON\Unserializable;
use stdClass;
use UnexpectedValueException;
use function in_array;
use function is_array;
use function is_string;
use function version_compare;

class RunOnRequirement implements Unserializable
{
    const TOPOLOGY_SINGLE = 'single';
    const TOPOLOGY_REPLICASET = 'replicaset';
    const TOPOLOGY_SHARDED = 'sharded';
    const TOPOLOGY_SHARDED_REPLICASET = 'sharded-replicaset';

    private $minServerVersion;
    private $maxServerVersion;
    private $topologies;

    public function __construct(stdClass $data)
    {
        $this->bsonUnserialize((array) $data);
    }

    /**
     * @see https://www.php.net/manual/en/mongodb-bson-unserializable.bsonunserialize.php
     */
    public function bsonUnserialize(array $data)
    {
        $this->minServerVersion = $data['minServerVersion'] ?? null;
        $this->maxServerVersion = $data['maxServerVersion'] ?? null;
        $this->topologies = $data['topologies'] ?? null;

        $this->validate();
    }

    /**
     * Checks if the requirements are satisfied.
     *
     * @param string $serverVersion
     * @param string $topology
     * @return boolean
     */
    public function isSatisfied(string $serverVersion, string $topology)
    {
        if (isset($this->minServerVersion) && version_compare($serverVersion, $this->minServerVersion, '<')) {
            return false;
        }

        if (isset($this->maxServerVersion) && version_compare($serverVersion, $this->maxServerVersion, '>')) {
            return false;
        }

        if (isset($this->topologies)) {
            if (in_array($topology, $this->topologies)) {
                return true;
            }

            /* Ensure "sharded-replicaset" is also accepted for topologies that
             * only include "sharded" (agnostic about the shard topology) */
            if ($topology === self::TOPOLOGY_SHARDED_REPLICASET && in_array(self::TOPOLOGY_SHARDED, $this->topologies)) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * @throws UnexpectedValueException if a property is invalid
     */
    private function validate()
    {
        if (isset($this->minServerVersion) && ! is_string($this->minServerVersion)) {
            throw new UnexpectedValueException('minServerVersion is not a string');
        }

        if (isset($this->maxServerVersion) && ! is_string($this->maxServerVersion)) {
            throw new UnexpectedValueException('maxServerVersion is not a string');
        }

        if (! isset($this->topologies)) {
            return;
        }

        if (! is_array($this->topologies)) {
            throw new UnexpectedValueException('topologies is not an array');
        }

        foreach ($this->topologies as $topology) {
            if (! is_string($topology)) {
                throw new UnexpectedValueException('topologies is not an array of strings');
            }
        }
    }
}
