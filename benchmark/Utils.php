<?php

namespace MongoDB\Benchmark;

use MongoDB\Client;
use MongoDB\Collection;

use function getenv;

final class Utils
{
    private static ?Client $client;
    private static ?Collection $collection;

    public static function getClient(): Client
    {
        return self::$client ??= self::createClient();
    }

    public static function getCollection(): Collection
    {
        return self::$collection ??= self::createCollection();
    }

    public static function getUri(): string
    {
        return getenv('MONGODB_URI') ?: 'mongodb://localhost:27017/';
    }

    public static function getDatabase(): string
    {
        return getenv('MONGODB_DATABASE') ?: 'phplib_test';
    }

    private static function createClient(): Client
    {
        return new Client(self::getUri());
    }

    private static function createCollection(): Collection
    {
        return self::getClient()->selectCollection(self::getDatabase(), 'perftest');
    }
}
