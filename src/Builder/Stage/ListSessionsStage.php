<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Optional;
use MongoDB\Model\BSONArray;

class ListSessionsStage implements StageInterface
{
    public const NAME = '$listSessions';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed> $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users. */
    public PackedArray|Optional|BSONArray|array $users;

    /** @param Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster. */
    public Optional|bool $allUsers;

    /**
     * @param BSONArray|Optional|PackedArray|list<ExpressionInterface|mixed> $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users.
     * @param Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster.
     */
    public function __construct(
        PackedArray|Optional|BSONArray|array $users = Optional::Undefined,
        Optional|bool $allUsers = Optional::Undefined,
    ) {
        if (\is_array($users) && ! \array_is_list($users)) {
            throw new \InvalidArgumentException('Expected $users argument to be a list, got an associative array.');
        }
        $this->users = $users;
        $this->allUsers = $allUsers;
    }
}
