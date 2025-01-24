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

    /** @var Optional|bool $allUsers */
    public readonly Optional|bool $allUsers;

    /** @var Optional|bool $idleConnections */
    public readonly Optional|bool $idleConnections;

    /** @var Optional|bool $idleCursors */
    public readonly Optional|bool $idleCursors;

    /** @var Optional|bool $idleSessions */
    public readonly Optional|bool $idleSessions;

    /** @var Optional|bool $localOps */
    public readonly Optional|bool $localOps;

    /**
     * @param Optional|bool $allUsers
     * @param Optional|bool $idleConnections
     * @param Optional|bool $idleCursors
     * @param Optional|bool $idleSessions
     * @param Optional|bool $localOps
     */
    public function __construct(
        Optional|bool $allUsers = Optional::Undefined,
        Optional|bool $idleConnections = Optional::Undefined,
        Optional|bool $idleCursors = Optional::Undefined,
        Optional|bool $idleSessions = Optional::Undefined,
        Optional|bool $localOps = Optional::Undefined,
    ) {
        $this->allUsers = $allUsers;
        $this->idleConnections = $idleConnections;
        $this->idleCursors = $idleCursors;
        $this->idleSessions = $idleSessions;
        $this->localOps = $localOps;
    }
}
