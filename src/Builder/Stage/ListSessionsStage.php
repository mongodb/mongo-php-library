<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;

use function array_is_list;
use function is_array;

/**
 * Lists all sessions that have been active long enough to propagate to the system.sessions collection.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSessions/
 * @internal
 */
final class ListSessionsStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$listSessions';
    public const PROPERTIES = ['users' => 'users', 'allUsers' => 'allUsers'];

    /** @var Optional|BSONArray|PackedArray|array $users Returns all sessions for the specified users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster to list sessions for other users. */
    public readonly Optional|PackedArray|BSONArray|array $users;

    /** @var Optional|bool $allUsers Returns all sessions for all users. If running with access control, the authenticated user must have privileges with listSessions action on the cluster. */
    public readonly Optional|bool $allUsers;

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
