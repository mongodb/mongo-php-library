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
use MongoDB\Exception\ResumeTokenException;
use MongoDB\Exception\ResumableException;
use IteratorIterator;
use Traversable;

/**
 * Iterator for change streams.
 *
 * @internal
 */
class ChangeStreamIterator extends IteratorIterator
{
    public function current()
    {
        $currentDocument = parent::current();
        if (isset($currentDocument)) {
            if (is_null($this->extract_resume_token($currentDocument))) {
                throw new ResumeTokenException("Cannot provide resume functionality when the resume token is missing");
            }
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
