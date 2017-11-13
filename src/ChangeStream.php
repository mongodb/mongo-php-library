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

use MongoDB\ChangeStreamIterator;
use MongoDB\Driver\Manager;
use MongoDB\Exception\ResumableException;
use MongoDB\Operation\ChangeStreamCommand;

/**
 * Operation for the changeStream command.
 *
 * @api
 * @see \MongoDB\Collection::changeStream()
 * @see http://docs.mongodb.org/manual/reference/command/changeStream/
 */
class ChangeStream
{
    private $databaseName;
    private $collectionName;
    private $pipeline;
    private $options;
    private $resumeToken;
    private $manager;
    private $csIt;

    public function __construct($cursor, $databaseName, $collectionName, array $pipeline, array $options = [], $resumeToken, Manager $manager)
    {
        $this->databaseName = (string) $databaseName;
        $this->collectionName = (string) $collectionName;
        $this->pipeline = $pipeline;
        $this->options = $options;
        $this->resumeToken = $resumeToken;
        $this->manager = $manager;

        $this->csIt = new ChangeStreamIterator($cursor);
    }

    public function current()
    {
        return $this->csIt->current();
    }

    public function getId()
    {
        return $this->csIt->getId();
    }

    public function next()
    {
        try {
            $this->csIt->next();
            $this->resumeToken = $this->csIt->extract_resume_token($this->csIt->current());
        } catch (ResumableException $e) {
            $this->resume();
        }
    }

    public function resume()
    {
        $this->options['resumeAfter'] = $this->resumeToken;
        array_shift($this->pipeline);

        $server = $this->manager->selectServer($this->options['readPreference']);

        $command = new ChangeStreamCommand($this->databaseName, $this->collectionName, $this->pipeline, $this->options, $this->manager);
        $server = $this->manager->selectServer($this->options['readPreference']);
        $cursor = $command->resume($server, $this->pipeline);

        $this->csIt = new ChangeStreamIterator($cursor);
        $this->csIt->rewind();
    }

    public function rewind()
    {
        $this->csIt->rewind();
    }
}
