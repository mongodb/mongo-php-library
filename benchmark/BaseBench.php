<?php

namespace MongoDB\Benchmark;

use MongoDB\Client;
use MongoDB\Collection;

use function getenv;

abstract class BaseBench
{
    protected const LARGE_FILE_PATH = __DIR__ . '/Fixtures/data/large_doc.json';
    protected const TWEET_FILE_PATH = __DIR__ . '/Fixtures/data/tweet.json';

    private static ?Collection $collection;

    protected static function getCollection(): Collection
    {
        return self::$collection ??= self::createCollection();
    }

    public static function createClient(array $options = [], array $driverOptions = []): Client
    {
        return new Client(self::getUri(), $options, $driverOptions);
    }

    public static function createCollection(): Collection
    {
        $client = self::createClient();

        return $client->selectCollection(self::getDatabase(), 'perftest');
    }

    public static function getUri(): string
    {
        return getenv('MONGODB_URI') ?: 'mongodb://localhost:27017/';
    }

    public static function getDatabase(): string
    {
        return getenv('MONGODB_DATABASE') ?: 'phplib_test';
    }
}
