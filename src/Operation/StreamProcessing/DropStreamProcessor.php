<?php
/*
 * Copyright 2026-present MongoDB, Inc.
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

namespace MongoDB\Operation\StreamProcessing;

use MongoDB\Driver\Command;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;

/**
 * Operation for the dropStreamProcessor command. A dropped processor cannot be recovered.
 */
final class DropStreamProcessor
{
    public function __construct(private string $name)
    {
    }

    /** @throws DriverRuntimeException for driver errors (e.g. connection errors) */
    public function execute(Server $server): void
    {
        $server->executeCommand('admin', new Command(['dropStreamProcessor' => $this->name]));
    }
}
