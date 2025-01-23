<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;

/**
 * Returns information on active and/or dormant operations for the MongoDB deployment. To run, use the db.aggregate() method.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/currentOp/
 * @internal
 */
final class CurrentOpStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$currentOp';

    public const PROPERTIES = [
        'allUsers' => 'allUsers',
        'idleConnections' => 'idleConnections',
        'idleCursors' => 'idleCursors',
        'idleSessions' => 'idleSessions',
        'localOps' => 'localOps',
    ];

    /** @var Optional|bool|string $allUsers */
    public readonly Optional|bool|string $allUsers;

    /** @var Optional|bool|string $idleConnections */
    public readonly Optional|bool|string $idleConnections;

    /** @var Optional|bool|string $idleCursors */
    public readonly Optional|bool|string $idleCursors;

    /** @var Optional|bool|string $idleSessions */
    public readonly Optional|bool|string $idleSessions;

    /** @var Optional|bool|string $localOps */
    public readonly Optional|bool|string $localOps;

    /**
     * @param Optional|bool|string $allUsers
     * @param Optional|bool|string $idleConnections
     * @param Optional|bool|string $idleCursors
     * @param Optional|bool|string $idleSessions
     * @param Optional|bool|string $localOps
     */
    public function __construct(
        Optional|bool|string $allUsers = Optional::Undefined,
        Optional|bool|string $idleConnections = Optional::Undefined,
        Optional|bool|string $idleCursors = Optional::Undefined,
        Optional|bool|string $idleSessions = Optional::Undefined,
        Optional|bool|string $localOps = Optional::Undefined,
    ) {
        $this->allUsers = $allUsers;
        $this->idleConnections = $idleConnections;
        $this->idleCursors = $idleCursors;
        $this->idleSessions = $idleSessions;
        $this->localOps = $localOps;
    }
}
