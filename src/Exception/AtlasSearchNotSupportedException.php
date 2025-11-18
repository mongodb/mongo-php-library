<?php

namespace MongoDB\Exception;

use MongoDB\Driver\Exception\ServerException;
use Throwable;

final class AtlasSearchNotSupportedException extends ServerException
{
    /** @internal */
    public static function create(ServerException $e): self
    {
        $message = $e->getCode() === 31082 ? $e->getMessage() : 'Using Atlas Search Database Commands and the $listSearchIndexes aggregation stage requires additional configuration. Please connect to Atlas or an AtlasCLI local deployment to enable. For more information on how to connect, see https://dochub.mongodb.org/core/atlas-cli-deploy-local-reqs';

        return new self($message, $e->getCode(), $e);
    }

    /** @internal */
    public static function isAtlasSearchNotSupportedError(Throwable $e): bool
    {
        if (! $e instanceof ServerException) {
            return false;
        }

        return match ($e->getCode()) {
            // MongoDB 8: Using Atlas Search Database Commands and the $listSearchIndexes aggregation stage requires additional configuration.
            31082 => true,
            // MongoDB 7: $listSearchIndexes stage is only allowed on MongoDB Atlas
            6047401 => true,
            // MongoDB 7-ent: Search index commands are only supported with Atlas.
            115 => true,
            // MongoDB 4 to 6, 7-community
            59 => 'no such command: \'createSearchIndexes\'' === $e->getMessage(),
            // MongoDB 4 to 6
            40324 => 'Unrecognized pipeline stage name: \'$listSearchIndexes\'' === $e->getMessage(),
            // Not an Atlas Search error
            default => false,
        };
    }
}
