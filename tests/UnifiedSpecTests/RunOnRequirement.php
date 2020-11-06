<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use stdClass;
use function in_array;
use function PHPUnit\Framework\assertContainsOnly;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function version_compare;

class RunOnRequirement
{
    const TOPOLOGY_SINGLE = 'single';
    const TOPOLOGY_REPLICASET = 'replicaset';
    const TOPOLOGY_SHARDED = 'sharded';
    const TOPOLOGY_SHARDED_REPLICASET = 'sharded-replicaset';

    const VERSION_PATTERN = '/^[0-9]+(\\.[0-9]+){1,2}$/';

    /** @var string */
    private $minServerVersion;

    /** @var string */
    private $maxServerVersion;

    /** @var array */
    private $topologies;

    public function __construct(stdClass $o)
    {
        if (isset($o->minServerVersion)) {
            assertIsString($o->minServerVersion);
            assertMatchesRegularExpression(self::VERSION_PATTERN, $o->minServerVersion);
            $this->minServerVersion = $o->minServerVersion;
        }

        if (isset($o->maxServerVersion)) {
            assertIsString($o->maxServerVersion);
            assertMatchesRegularExpression(self::VERSION_PATTERN, $o->maxServerVersion);
            $this->maxServerVersion = $o->maxServerVersion;
        }

        if (isset($o->topologies)) {
            assertIsArray($o->topologies);
            assertContainsOnly('string', $o->topologies);
            $this->topologies = $o->topologies;
        }
    }

    public function isSatisfied(string $serverVersion, string $topology) : bool
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
}
