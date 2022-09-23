<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Client;
use MongoDB\Driver\Exception\CommandException;
use MongoDB\Driver\ReadPreference;

use function array_key_exists;

final class ServerParameterHelper
{
    /** @var Client */
    private $client;

    /** @var array<string|mixed> */
    private $parameters = [];

    /** @var bool */
    private $fetchAllParametersFailed = false;

    /** @var bool */
    private $allParametersFetched = false;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /** @return mixed */
    public function __get(string $parameter)
    {
        if (! array_key_exists($parameter, $this->parameters)) {
            $this->fetchParameter($parameter);
        }

        return $this->parameters[$parameter];
    }

    private function fetchParameter(string $parameter): void
    {
        // Try fetching all parameters once
        if (! $this->allParametersFetched && ! $this->fetchAllParametersFailed) {
            $this->fetchAllParameters();
        }

        if (array_key_exists($parameter, $this->parameters)) {
            return;
        }

        // If fetching all parameters failed, or the parameter was not part of
        // the list, fetch the single parameter as fallback
        $this->fetchSingleParameter($parameter);
    }

    private function fetchAllParameters(): void
    {
        try {
            $database = $this->client->selectDatabase('admin');
            $cursor = $database->command(
                ['getParameter' => '*'],
                [
                    'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
                    'typeMap' => [
                        'root' => 'array',
                        'document' => 'array',
                        'array' => 'array',
                    ],
                ]
            );

            $this->parameters = $cursor->toArray()[0];
            $this->allParametersFetched = true;
        } catch (CommandException $e) {
            $this->fetchAllParametersFailed = true;
        }
    }

    private function fetchSingleParameter(string $parameter): void
    {
        $database = $this->client->selectDatabase('admin');
        $cursor = $database->command(
            [
                'getParameter' => 1,
                $parameter => 1,
            ],
            [
                'readPreference' => new ReadPreference(ReadPreference::PRIMARY),
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array',
                    'array' => 'array',
                ],
            ]
        );

        $this->parameters[$parameter] = $cursor->toArray()[0][$parameter];
    }
}
