<?php
/*
 * Copyright 2025-present MongoDB, Inc.
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

namespace MongoDB\Operation;

use MongoDB\Driver\BulkWriteCommand;
use MongoDB\Driver\BulkWriteCommandResult;
use MongoDB\Driver\Exception\RuntimeException as DriverRuntimeException;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Driver\WriteConcern;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;

use function array_filter;
use function count;

/**
 * Operation for executing multiple write operations via the bulkWrite command.
 *
 * @see \MongoDB\Client::bulkWrite()
 */
final class ClientBulkWriteCommand
{
    /**
     * Constructs a client-level bulk write operation.
     *
     * Most options for the bulkWrite command and its write operations are
     * specified when building the extension's BulkWriteCommand object. This
     * operation only accepts options for Server::executeBulkWriteCommand().
     *
     * Supported options:
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): Write concern.
     *
     * @param BulkWriteCommand $bulkWriteCommand Assembled bulk write command
     * @param array            $options          Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct(
        private BulkWriteCommand $bulkWriteCommand,
        /** @param array{session: ?Session, writeConcern: ?WriteConcern} */
        private array $options = [],
    ) {
        if (count($bulkWriteCommand) === 0) {
            throw new InvalidArgumentException('$bulkWriteCommand is empty');
        }

        if (isset($this->options['session']) && ! $this->options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $this->options['session'], Session::class);
        }

        if (isset($this->options['writeConcern']) && ! $this->options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $this->options['writeConcern'], WriteConcern::class);
        }

        if (isset($this->options['writeConcern']) && $this->options['writeConcern']->isDefault()) {
            unset($this->options['writeConcern']);
        }
    }

    /**
     * Execute the operation.
     *
     * @throws UnsupportedException if write concern is used and unsupported
     * @throws DriverRuntimeException for other driver errors (e.g. connection errors)
     */
    public function execute(Server $server): BulkWriteCommandResult
    {
        $inTransaction = isset($this->options['session']) && $this->options['session']->isInTransaction();
        if ($inTransaction && isset($this->options['writeConcern'])) {
            throw UnsupportedException::writeConcernNotSupportedInTransaction();
        }

        $options = array_filter($this->options, fn ($value) => $value !== null);

        return $server->executeBulkWriteCommand($this->bulkWriteCommand, $options);
    }
}
