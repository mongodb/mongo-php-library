<?php

namespace MongoDB\Exception;

use MongoDB\Driver\Exception\ServerException;
use Throwable;

use function preg_match;

final class SearchNotSupportedException extends ServerException
{
    /** @internal */
    public static function create(ServerException $e): self
    {
        $message = match ($e->getCode()) {
            31082, 7501001 => $e->getMessage(),
            default => 'Using Atlas Search Database Commands and the $listSearchIndexes aggregation stage requires additional configuration. '
                . 'Please connect to Atlas or an AtlasCLI local deployment to enable. '
                . 'For more information on how to connect, see https://dochub.mongodb.org/core/atlas-cli-deploy-local-reqs',
        };

        return new self($message, $e->getCode(), $e);
    }

    /** @internal */
    public static function isSearchNotSupportedError(Throwable $exception): bool
    {
        if (! $exception instanceof ServerException) {
            return false;
        }

        return match ($exception->getCode()) {
            // MongoDB 8: Using Atlas Search Database Commands and the $listSearchIndexes aggregation stage requires additional configuration.
            31082 => true,
            // MongoDB 7: $listSearchIndexes stage is only allowed on MongoDB Atlas
            6047401 => true,
            // MongoDB 7-ent: Search index commands are only supported with Atlas.
            115 => true,
            // MongoDB 4 to 6, 7-community
            59 => preg_match('/^no such (command|cmd): \'?(createSearchIndexes|updateSearchIndex|dropSearchIndex)\'?$/', $exception->getMessage()) === 1,
            // MongoDB 4 to 6
            40324 => preg_match('/^Unrecognized pipeline stage name: \'?\$(listSearchIndexes|search|searchMeta|vectorSearch)\'?$/', $exception->getMessage()) === 1,
            // MongoDB 5 sharded cluster: $search not enabled! Enable Search by setting serverParameter mongotHost to a valid "host:port" string
            7501001 => true,
            // Not a Search error
            default => false,
        };
    }
}
