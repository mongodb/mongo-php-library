<?php
/*
 * Copyright 2017-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB;

use Iterator;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Exception\ServerException;
use MongoDB\Exception\BadMethodCallException;
use MongoDB\Exception\ResumeTokenException;
use MongoDB\Model\ChangeStreamIterator;

use function assert;
use function call_user_func;
use function in_array;

/**
 * Iterator for a change stream.
 *
 * @see \MongoDB\Collection::watch()
 * @see https://mongodb.com/docs/manual/reference/method/db.watch/#mongodb-method-db.watch
 *
 * @psalm-type ResumeCallable = callable(array|object|null, bool): ChangeStreamIterator
 * @template-implements Iterator<int, array|object>
 */
class ChangeStream implements Iterator
{
    private const CURSOR_NOT_FOUND = 43;

    private const RESUMABLE_ERROR_CODES = [
        6, // HostUnreachable
        7, // HostNotFound
        89, // NetworkTimeout
        91, // ShutdownInProgress
        189, // PrimarySteppedDown
        262, // ExceededTimeLimit
        9001, // SocketException
        10107, // NotPrimary
        11600, // InterruptedAtShutdown
        11602, // InterruptedDueToReplStateChange
        13435, // NotPrimaryNoSecondaryOk
        13436, // NotPrimaryOrSecondary
        63, // StaleShardVersion
        150, // StaleEpoch
        13388, // StaleConfig
        234, // RetryChangeStream
        133, // FailedToSatisfyReadPreference
    ];

    private const WIRE_VERSION_FOR_RESUMABLE_CHANGE_STREAM_ERROR = 9;

    /** @var ResumeCallable|null */
    private $resumeCallable;

    private int $key = 0;

    /**
     * Whether the change stream has advanced to its first result. This is used
     * to determine whether $key should be incremented after an iteration event.
     */
    private bool $hasAdvanced = false;

    /** @see https://php.net/iterator.current */
    public function current(): array|object|null
    {
        $value = $this->iterator->current();

        if (! $this->codec) {
            return $value;
        }

        assert($value instanceof Document);

        return $this->codec->decode($value);
    }

    public function getCursorId(): Int64
    {
        return $this->iterator->getInnerIterator()->getId();
    }

    /**
     * Returns the resume token for the iterator's current position.
     *
     * Null may be returned if no change documents have been iterated and the
     * server did not include a postBatchResumeToken in its aggregate or getMore
     * command response.
     */
    public function getResumeToken(): array|object|null
    {
        return $this->iterator->getResumeToken();
    }

    /** @see https://php.net/iterator.key */
    public function key(): ?int
    {
        if ($this->valid()) {
            return $this->key;
        }

        return null;
    }

    /**
     * @see https://php.net/iterator.next
     * @throws ResumeTokenException
     */
    public function next(): void
    {
        try {
            $this->iterator->next();
            $this->onIteration($this->hasAdvanced);
        } catch (RuntimeException $e) {
            $this->resumeOrThrow($e);
        }
    }

    /**
     * @see https://php.net/iterator.rewind
     * @throws ResumeTokenException
     */
    public function rewind(): void
    {
        try {
            $this->iterator->rewind();
            /* Unlike next() and resume(), the decision to increment the key
             * does not depend on whether the change stream has advanced. This
             * ensures that multiple calls to rewind() do not alter state. */
            $this->onIteration(false);
        } catch (RuntimeException $e) {
            $this->resumeOrThrow($e);
        }
    }

    /** @see https://php.net/iterator.valid */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * @internal
     *
     * @param ResumeCallable $resumeCallable
     */
    public function __construct(private ChangeStreamIterator $iterator, callable $resumeCallable, private ?DocumentCodec $codec = null)
    {
        $this->resumeCallable = $resumeCallable;

        if ($codec) {
            $this->iterator->getInnerIterator()->setTypeMap(['root' => 'bson']);
        }
    }

    /**
     * Determines if an exception is a resumable error.
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/change-streams/change-streams.rst#resumable-error
     */
    private function isResumableError(RuntimeException $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        if (! $exception instanceof ServerException) {
            return false;
        }

        if ($exception->getCode() === self::CURSOR_NOT_FOUND) {
            return true;
        }

        if (server_supports_feature($this->iterator->getServer(), self::WIRE_VERSION_FOR_RESUMABLE_CHANGE_STREAM_ERROR)) {
            return $exception->hasErrorLabel('ResumableChangeStreamError');
        }

        return in_array($exception->getCode(), self::RESUMABLE_ERROR_CODES);
    }

    /**
     * Perform housekeeping after an iteration event.
     *
     * @param boolean $incrementKey Increment $key if there is a current result
     * @throws ResumeTokenException
     */
    private function onIteration(bool $incrementKey): void
    {
        /* If the cursorId is 0, the server has invalidated the cursor and we
         * will never perform another getMore nor need to resume since any
         * remaining results (up to and including the invalidate event) will
         * have been received in the last response. Therefore, we can unset the
         * resumeCallable. This will free any reference to Watch as well as the
         * only reference to any implicit session created therein. */
        // Use a type-unsafe comparison to compare with Int64 instances
        if ($this->getCursorId() == 0) {
            $this->resumeCallable = null;
        }

        /* Return early if there is not a current result. Avoid any attempt to
         * increment the iterator's key. */
        if (! $this->valid()) {
            return;
        }

        if ($incrementKey) {
            $this->key++;
        }

        $this->hasAdvanced = true;
    }

    /**
     * Recreates the ChangeStreamIterator after a resumable server error.
     */
    private function resume(): void
    {
        if ($this->resumeCallable === null) {
            throw new BadMethodCallException('Cannot resume a closed change stream.');
        }

        $this->iterator = call_user_func($this->resumeCallable, $this->getResumeToken(), $this->hasAdvanced);

        $this->iterator->rewind();

        if ($this->codec) {
            $this->iterator->getInnerIterator()->setTypeMap(['root' => 'bson']);
        }

        $this->onIteration($this->hasAdvanced);
    }

    /**
     * Either resumes after a resumable error or re-throws the exception.
     *
     * @throws RuntimeException
     */
    private function resumeOrThrow(RuntimeException $exception): void
    {
        if ($this->isResumableError($exception)) {
            $this->resume();

            return;
        }

        throw $exception;
    }
}
