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

use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\RuntimeException;
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

    public function __construct(Cursor $cursor, Callable $resumeCallable)
    {
        $this->resumeCallable = $resumeCallable;
        $this->csIt = new IteratorIterator($cursor);
    }

    public function current()
    {
        return $this->csIt->current();
    }

    public function getId()
    {
        return $this->csIt->getInnerIterator()->getId();
    }

    public function key()
    {

    }

    public function next()
    {
        try {
            $this->csIt->next();
            $this->resumeToken = $this->extractResumeToken($this->csIt->current());
        } catch (RuntimeException $e) {
            $this->resume();
        }
    }

    public function resume()
    {
        $newChangeStream = call_user_func($this->resumeCallable, $this->resumeToken);
        $this->csIt = $newChangeStream->csIt;
        $this->csIt->rewind();
    }

    public function rewind()
    {
        $this->csIt->rewind();
    }

    public function valid()
    {

    }

    private function extractResumeToken($document)
    {
        if ($document === null) {
            throw new ResumeTokenException("Cannot extract a resumeToken from an empty document");
        }
        if ($document instanceof Serializable) {
            return $this->extractResumeToken($document->bsonSerialize());
        }
        $lastResumeToken = is_array($document) ? $document['_id'] : $document->_id;
        if ($lastResumeToken === null) {
            throw new ResumeTokenException("Cannot provide resume functionality when the resume token is missing");
        }
        $this->resumeToken = $lastResumeToken;
    }
}
