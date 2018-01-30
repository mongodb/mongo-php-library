<?php
/*
 * Copyright 2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB;

use MongoDB\BSON\Serializable;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Exception\ResumeTokenException;
use IteratorIterator;
use Iterator;

/**
 * Iterator for the changeStream command.
 *
 * @api
 * @see \MongoDB\Collection::changeStream()
 * @see http://docs.mongodb.org/manual/reference/command/changeStream/
 */
class ChangeStream implements Iterator
{
    private $resumeToken;
    private $resumeCallable;
    private $csIt;
    private $key;

    const CURSOR_NOT_FOUND = 43;

    /**
     * @param Cursor $cursor
     * @param callable $resumeCallable
     */
    public function __construct(Cursor $cursor, callable $resumeCallable)
    {
        $this->resumeCallable = $resumeCallable;
        $this->csIt = new IteratorIterator($cursor);

        $this->key = 0;
    }

    /**
     * @see http://php.net/iterator.current
     * @return mixed
     */
    public function current()
    {
        return $this->csIt->current();
    }

    /**
     * @return \MongoDB\Driver\CursorId
     */
    public function getCursorId()
    {
        return $this->csIt->getInnerIterator()->getId();
    }

    /**
     * @see http://php.net/iterator.key
     * @return mixed
     */
    public function key()
    {
        if ($this->valid()) {
            return $this->key;
        }
        return null;
    }

    /**
     * @see http://php.net/iterator.next
     * @return void
     */
    public function next()
    {
        $resumable = false;
        try {
            $this->csIt->next();
            if ($this->valid()) {
                $this->extractResumeToken($this->csIt->current());
                $this->key++;
            }
        } catch (RuntimeException $e) {
            if (strpos($e->getMessage(), "not master") !== false) {
                $resumable = true;
            }
            if ($e->getCode() === self::CURSOR_NOT_FOUND) {
               $resumable = true;
            }
            if ($e instanceof ConnectionTimeoutException) {
                $resumable = true;
            }
        }
        if ($resumable) {
            $this->resume();
        }
    }

    /**
     * @see http://php.net/iterator.rewind
     * @return void
     */
    public function rewind()
    {
        $this->csIt->rewind();
    }

    /**
     * @see http://php.net/iterator.valid
     * @return boolean
     */
    public function valid()
    {
        return $this->csIt->valid();
    }

    /**
     * Extracts the resumeToken (_id) of the input document.
     *
     * @return void
     * @throws ResumeTokenException if the document has no _id.
     */
    private function extractResumeToken($document)
    {
        if ($document === null) {
            throw new ResumeTokenException("Cannot extract a resumeToken from an empty document");
        }
        if ($document instanceof Serializable) {
            $this->extractResumeToken($document->bsonSerialize());
            return;
        }
        if (isset($document->_id)) {
            $this->resumeToken = is_array($document) ? $document['_id'] : $document->_id;
        } else {
             throw new ResumeTokenException("Cannot provide resume functionality when the resume token is missing");
        }
    }

    /**
     * Creates a new changeStream after a resumable server error.
     *
     * @return void
     */
    private function resume()
    {
        $newChangeStream = call_user_func($this->resumeCallable, $this->resumeToken);
        $this->csIt = $newChangeStream->csIt;
        $this->csIt->rewind();
    }
}
