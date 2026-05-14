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

namespace MongoDB;

use MongoDB\Driver\Exception\InvalidArgumentException as DriverInvalidArgumentException;
use MongoDB\Driver\Manager;
use MongoDB\Exception\InvalidArgumentException;
use Stringable;

use function preg_match;
use function strtolower;

/**
 * Client for an Atlas Stream Processing workspace.
 *
 * Workspace endpoints share the mongodb:// URI scheme with standard MongoDB
 * clusters but follow a distinct hostname pattern:
 *
 *     mongodb://atlas-stream-<workspaceId>-<suffix>.<region>.a.query.mongodb.net/
 *
 * Per the ASP spec, TLS is required and authSource defaults to "admin".
 */
final class StreamProcessingClient implements Stringable
{
    /**
     * Pattern used to detect a workspace endpoint hostname.
     *
     * Matches hosts that start with "atlas-stream-" and end with
     * ".a.query.mongodb.net" (case-insensitive). Either marker is sufficient
     * for detection but both together provide a strong signal.
     */
    private const WORKSPACE_HOSTNAME_PATTERN = '#^mongodb://[^/?]*atlas-stream-[^/?]*\.a\.query\.mongodb\.net(?::\d+)?(/|$|\?)#i';

    private Manager $manager;

    /**
     * @param string $uri           Workspace connection string
     * @param array  $uriOptions    Additional connection string options
     * @param array  $driverOptions Driver-specific options
     * @throws InvalidArgumentException       if TLS is disabled or the URI is not a workspace endpoint
     * @throws DriverInvalidArgumentException for parameter/option parsing errors in the driver
     */
    public function __construct(private string $uri, array $uriOptions = [], array $driverOptions = [])
    {
        if (! self::isWorkspaceUri($uri)) {
            throw new InvalidArgumentException(
                'StreamProcessingClient requires a workspace endpoint URI (atlas-stream-*.a.query.mongodb.net). '
                . 'For standard MongoDB clusters use MongoDB\\Client instead.',
            );
        }

        // mongodb+srv:// is not applicable for workspace endpoints
        if (preg_match('#^mongodb\+srv://#i', $uri)) {
            throw new InvalidArgumentException('mongodb+srv:// is not supported for workspace endpoints; use mongodb://');
        }

        $uriOptions = self::applyWorkspaceDefaults($uriOptions);

        $this->manager = new Manager($uri, $uriOptions, $driverOptions);
    }

    /**
     * Return the workspace URI.
     */
    public function __toString(): string
    {
        return $this->uri;
    }

    public function __debugInfo(): array
    {
        return [
            'manager' => $this->manager,
            'uri' => $this->uri,
        ];
    }

    /**
     * Return the underlying Manager for advanced uses (e.g. runCommand).
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * Return a handle for managing stream processors in this workspace.
     */
    public function streamProcessors(): StreamProcessors
    {
        return new StreamProcessors($this->manager);
    }

    /**
     * Return true if the supplied URI targets an Atlas Stream Processing workspace endpoint.
     */
    public static function isWorkspaceUri(string $uri): bool
    {
        return (bool) preg_match(self::WORKSPACE_HOSTNAME_PATTERN, $uri);
    }

    /**
     * Apply workspace defaults to user-supplied URI options.
     *
     * Per spec, TLS is required and MUST NOT be disabled. authSource defaults
     * to "admin" but MAY be overridden by the caller.
     *
     * @throws InvalidArgumentException if the caller has explicitly disabled TLS
     */
    private static function applyWorkspaceDefaults(array $uriOptions): array
    {
        foreach ($uriOptions as $key => $value) {
            if (strtolower((string) $key) === 'tls' && $value === false) {
                throw new InvalidArgumentException('TLS cannot be disabled for an Atlas Stream Processing workspace connection');
            }

            if (strtolower((string) $key) === 'ssl' && $value === false) {
                throw new InvalidArgumentException('TLS cannot be disabled for an Atlas Stream Processing workspace connection');
            }
        }

        $uriOptions['tls'] ??= true;
        $uriOptions['authSource'] ??= 'admin';

        return $uriOptions;
    }
}
