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
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Exception\ResumeException;
use MongoDB\Exception\ResumableException;
use IteratorIterator;
use Traversable;

/**
 * Iterator for wrapping a Traversable and caching its results.
 *
 * By caching results, this iterators allows a Traversable to be counted and
 * rewound multiple times, even if the wrapped object does not natively support
 * those operations (e.g. MongoDB\Driver\Cursor).
 *
 * @internal
 */
class ChangeStreamIterator extends IteratorIterator
{
    private $databaseName;
    private $collectionName;
    private $pipeline;
    private $options;
    private $lastResumeToken;

    public function __construct(Traversable $traversable, $databaseName, $collectionName, $pipeline, $options, $resumeToken)
    {
        $this->databaseName = $databaseName;
        $this->collectionName = $collectionName;
        $this->pipeline = $pipeline;
        $this->options = $options;
        if (isset($resumeToken))
        {
            $this->lastResumeToken = $resumeToken;
        }

        parent::__construct($traversable);
    }

    public function current()
    {
        $currentDocument = parent::current();
        if (isset($currentDocument)) {
            if (is_null($this->extract_resume_token($currentDocument))) {
                throw new ResumeException("Cannot provide resume functionality when the resume token is missing");
            }
            $this->lastResumeToken = $this->extract_resume_token($currentDocument);
       }
        return $currentDocument;
    }

    public function extract_resume_token($document)
    {
        if (is_null($document)) {
            return;
        }
        if ($document instanceof Serializable) {
            return $this->extract_resume_token($document->bsonSerialize());
        }
        return is_array($document) ? $document['_id'] : $document->_id;
    }

    public function next()
    {
        try {
            return parent::next();
        } catch (RuntimeException $e) {
            throw new ResumableException($e);
        }
    }
}
