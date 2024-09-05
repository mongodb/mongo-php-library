<?php
/*
 * Copyright 2019-present MongoDB, Inc.
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

namespace MongoDB\Model;

use Iterator;
use IteratorIterator;
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSubscriber;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;
use MongoDB\Driver\Server;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\ResumeTokenException;
use MongoDB\Exception\UnexpectedValueException;
use ReturnTypeWillChange;

use function assert;
use function count;
use function is_array;
use function is_object;
use function MongoDB\Driver\Monitoring\addSubscriber;
use function MongoDB\Driver\Monitoring\removeSubscriber;
use function MongoDB\is_document;

/**
 * ChangeStreamIterator wraps a change stream's tailable cursor.
 *
 * This iterator tracks the size of each batch in order to determine when the
 * postBatchResumeToken is applicable. It also ensures that initial calls to
 * rewind() do not execute getMore commands.
 *
 * @internal
 * @template TValue of array|object
 * @template-extends IteratorIterator<int, TValue, CursorInterface<int, TValue>&Iterator<int, TValue>>
 */
class ChangeStreamIterator extends IteratorIterator implements CommandSubscriber
{
    private int $batchPosition = 0;

    private int $batchSize;

    private bool $isRewindNop;

    private bool $isValid = false;

    private array|object|null $resumeToken = null;

    private Server $server;

    /**
     * @see https://php.net/iteratoriterator.current
     * @return array|object|null
     * @psalm-return TValue|null
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->valid() ? parent::current() : null;
    }

    /**
     * Necessary to let psalm know that we're always expecting a cursor as inner
     * iterator. This could be side-stepped due to the class not being final,
     * but it's very much an invalid use-case. This method can be dropped in 2.0
     * once the class is final.
     *
     * @return CursorInterface<int, TValue>&Iterator<int, TValue>
     */
    final public function getInnerIterator(): Iterator
    {
        $cursor = parent::getInnerIterator();
        assert($cursor instanceof CursorInterface);
        assert($cursor instanceof Iterator);

        return $cursor;
    }

    /**
     * Returns the resume token for the iterator's current position.
     *
     * Null may be returned if no change documents have been iterated and the
     * server did not include a postBatchResumeToken in its aggregate or getMore
     * command response.
     *
     * @return array|object|null
     */
    public function getResumeToken()
    {
        return $this->resumeToken;
    }

    /**
     * Returns the server the cursor is running on.
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @see https://php.net/iteratoriterator.key
     * @return int|null
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->valid() ? parent::key() : null;
    }

    /** @see https://php.net/iteratoriterator.rewind */
    public function next(): void
    {
        /* Determine if advancing the iterator will execute a getMore command
         * (i.e. we are already positioned at the end of the current batch). If
         * so, rely on the APM callbacks to reset $batchPosition and update
         * $batchSize. Otherwise, we can forgo APM and manually increment
         * $batchPosition after calling next(). */
        $getMore = $this->isAtEndOfBatch();

        if ($getMore) {
            addSubscriber($this);
        }

        try {
            parent::next();

            $this->onIteration(! $getMore);
        } finally {
            if ($getMore) {
                removeSubscriber($this);
            }
        }
    }

    /** @see https://php.net/iteratoriterator.rewind */
    public function rewind(): void
    {
        if ($this->isRewindNop) {
            return;
        }

        parent::rewind();

        $this->onIteration(false);
    }

    /**
     * @see https://php.net/iteratoriterator.valid
     * @psalm-assert-if-true TValue $this->current()
     */
    public function valid(): bool
    {
        return $this->isValid;
    }

    /**
     * @internal
     * @psalm-param CursorInterface<int, TValue>&Iterator<int, TValue> $cursor
     */
    public function __construct(CursorInterface $cursor, int $firstBatchSize, array|object|null $initialResumeToken, private ?object $postBatchResumeToken = null)
    {
        if (! $cursor instanceof Iterator) {
            throw InvalidArgumentException::invalidType(
                '$cursor',
                $cursor,
                CursorInterface::class . '&' . Iterator::class,
            );
        }

        if (isset($initialResumeToken) && ! is_document($initialResumeToken)) {
            throw InvalidArgumentException::expectedDocumentType('$initialResumeToken', $initialResumeToken);
        }

        parent::__construct($cursor);

        $this->batchSize = $firstBatchSize;
        $this->isRewindNop = ($firstBatchSize === 0);
        $this->resumeToken = $initialResumeToken;
        $this->server = $cursor->getServer();
    }

    /** @internal */
    final public function commandFailed(CommandFailedEvent $event): void
    {
    }

    /** @internal */
    final public function commandStarted(CommandStartedEvent $event): void
    {
        if ($event->getCommandName() !== 'getMore') {
            return;
        }

        $this->batchPosition = 0;
        $this->batchSize = 0;
        $this->postBatchResumeToken = null;
    }

    /** @internal */
    final public function commandSucceeded(CommandSucceededEvent $event): void
    {
        if ($event->getCommandName() !== 'getMore') {
            return;
        }

        $reply = $event->getReply();

        if (! isset($reply->cursor->nextBatch) || ! is_array($reply->cursor->nextBatch)) {
            throw new UnexpectedValueException('getMore command did not return a "cursor.nextBatch" array');
        }

        $this->batchSize = count($reply->cursor->nextBatch);

        if (isset($reply->cursor->postBatchResumeToken) && is_object($reply->cursor->postBatchResumeToken)) {
            $this->postBatchResumeToken = $reply->cursor->postBatchResumeToken;
        }
    }

    /**
     * Extracts the resume token (i.e. "_id" field) from a change document.
     *
     * @param array|object $document Change document
     * @return array|object
     * @throws InvalidArgumentException
     * @throws ResumeTokenException if the resume token is not found or invalid
     */
    private function extractResumeToken(array|object $document)
    {
        if (! is_document($document)) {
            throw InvalidArgumentException::expectedDocumentType('$document', $document);
        }

        if ($document instanceof Serializable) {
            return $this->extractResumeToken($document->bsonSerialize());
        }

        if ($document instanceof Document) {
            $resumeToken = $document->get('_id');

            if ($resumeToken instanceof Document) {
                $resumeToken = $resumeToken->toPHP();
            }
        } else {
            $resumeToken = is_array($document)
                ? ($document['_id'] ?? null)
                : ($document->_id ?? null);
        }

        if (! isset($resumeToken)) {
            $this->isValid = false;

            throw ResumeTokenException::notFound();
        }

        if (! is_array($resumeToken) && ! is_object($resumeToken)) {
            $this->isValid = false;

            throw ResumeTokenException::invalidType($resumeToken);
        }

        return $resumeToken;
    }

    /**
     * Return whether the iterator is positioned at the end of the batch.
     */
    private function isAtEndOfBatch(): bool
    {
        return $this->batchPosition + 1 >= $this->batchSize;
    }

    /**
     * Perform housekeeping after an iteration event.
     *
     * @see https://github.com/mongodb/specifications/blob/master/source/change-streams/change-streams.rst#updating-the-cached-resume-token
     */
    private function onIteration(bool $incrementBatchPosition): void
    {
        $this->isValid = parent::valid();

        /* Disable rewind()'s NOP behavior once we advance to a valid position.
         * This will allow the driver to throw a LogicException if rewind() is
         * called after the cursor has advanced past its first element. */
        if ($this->isRewindNop && $this->valid()) {
            $this->isRewindNop = false;
        }

        if ($incrementBatchPosition && $this->valid()) {
            $this->batchPosition++;
        }

        /* If the iterator is positioned at the end of the batch, apply the
         * postBatchResumeToken if it's available. This handles both the case
         * where the current batch is empty (since onIteration() will be called
         * after a successful getMore) and when the iterator has advanced to the
         * last document in its current batch. Otherwise, extract a resume token
         * from the current document if possible. */
        if ($this->isAtEndOfBatch() && $this->postBatchResumeToken !== null) {
            $this->resumeToken = $this->postBatchResumeToken;
        } elseif ($this->valid()) {
            $this->resumeToken = $this->extractResumeToken($this->current());
        }
    }
}
