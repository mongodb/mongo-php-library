<?php

namespace MongoDB\Exception;

use MongoDB\Driver\Exception\ServerException;
use Throwable;

final class SearchNotSupportedException extends ServerException
{
    /** @internal */
    public static function create(ServerException $e): self
    {
        $message = $e->getCode() === 31082
            ? $e->getMessage()
            : 'Using Atlas Search Database Commands and the $listSearchIndexes aggregation stage requires additional configuration. '
                . 'Please connect to Atlas or an AtlasCLI local deployment to enable. '
                . 'For more information on how to connect, see https://dochub.mongodb.org/core/atlas-cli-deploy-local-reqs';

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
            59 => match ($exception->getMessage()) {
                'no such command: \'createSearchIndexes\'' => true,
                'no such command: createSearchIndexes' => true,
                'no such command: \'updateSearchIndex\'' => true,
                'no such command: updateSearchIndex' => true,
                'no such command: \'dropSearchIndex\'' => true,
                'no such command: dropSearchIndex' => true,
                default => false,
            },
            // MongoDB 4 to 6
            40324 => match ($exception->getMessage()) {
                'Unrecognized pipeline stage name: \'$listSearchIndexes\'' => true,
                'Unrecognized pipeline stage name: \'$search\'' => true,
                'Unrecognized pipeline stage name: \'$searchMeta\'' => true,
                'Unrecognized pipeline stage name: \'$vectorSearch\'' => true,
                default => false,
            },
            // Not a Search error
            default => false,
        };
    }
}
