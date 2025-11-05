<?php

namespace MongoDB\Exception;

use MongoDB\Driver\Exception\ServerException;
use Throwable;

use function in_array;

final class SearchNotSupportedException extends ServerException
{
    public static function create(ServerException $e): self
    {
        return new self('Using Atlas Search Database Commands and the $listSearchIndexes aggregation stage requires additional configuration. Please connect to Atlas or an AtlasCLI local deployment to enable. For more information on how to connect, see https://dochub.mongodb.org/core/atlas-cli-deploy-local-reqs', $e->getCode(), $e);
    }

    public static function isSearchNotSupportedError(Throwable $e): bool
    {
        if (! $e instanceof ServerException) {
            return false;
        }

        return in_array($e->getCode(), [
            59,      // MongoDB 4 to 6, 7-community: no such command: 'createSearchIndexes'
            40324,   // MongoDB 4 to 6: Unrecognized pipeline stage name: '$listSearchIndexes'
            115,     // MongoDB 7-ent: Search index commands are only supported with Atlas.
            6047401, // MongoDB 7: $listSearchIndexes stage is only allowed on MongoDB Atlas
            31082,   // MongoDB 8: Using Atlas Search Database Commands and the $listSearchIndexes aggregation stage requires additional configuration.
        ], true);
    }
}
