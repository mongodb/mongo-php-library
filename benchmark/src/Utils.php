<?php

namespace MongoDB\Benchmark;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

use function getenv;

final class Utils
{
    private static ?Client $client;
    private static ?Database $database;
    private static ?Collection $collection;

    public static function getClient(): Client
    {
        return self::$client ??= new Client(self::getUri(), [], ['disableClientPersistence' => true]);
    }

    public static function getDatabase(): Database
    {
        return self::$database ??= self::getClient()->selectDatabase(self::getDatabaseName());
    }

    public static function getCollection(): Collection
    {
        return self::$collection ??= self::getDatabase()->selectCollection(self::getCollectionName());
    }

    public static function getUri(): string
    {
        return getenv('MONGODB_URI') ?: 'mongodb://localhost:27017/';
    }

    public static function getDatabaseName(): string
    {
        return getenv('MONGODB_DATABASE') ?: 'phplib_test';
    }

    public static function getCollectionName(): string
    {
        return 'perftest';
    }

    public static function reset(): void
    {
        self::$client = null;
        self::$database = null;
        self::$collection = null;
    }
}
