<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Lists all active sessions recently in use on the currently connected mongos or mongod instance. These sessions may have not yet propagated to the system.sessions collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listLocalSessions/
 */
class ListLocalSessionsStage implements StageInterface
{
    public const NAME = '$listLocalSessions';
    public const ENCODE = Encode::Object;

    /** @param Optional|BSONArray|PackedArray|array $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users. */
    public Optional|PackedArray|BSONArray|array $users;

    /** @param Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster. */
    public Optional|bool $allUsers;

    /**
     * @param Optional|BSONArray|PackedArray|array $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users.
     * @param Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster.
     */
    public function __construct(
        Optional|PackedArray|BSONArray|array $users = Optional::Undefined,
        Optional|bool $allUsers = Optional::Undefined,
    ) {
        if (is_array($users) && ! array_is_list($users)) {
            throw new InvalidArgumentException('Expected $users argument to be a list, got an associative array.');
        }

        $this->users = $users;
        $this->allUsers = $allUsers;
    }
}
