<?php

namespace MongoDB\Benchmark\Fixtures;

use function file_get_contents;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class Data
{
    public const LARGE_FILE_PATH = __DIR__ . '/data/large_doc.json';
    public const SMALL_FILE_PATH = __DIR__ . '/data/small_doc.json';
    public const TWEET_FILE_PATH = __DIR__ . '/data/tweet.json';

    public static function readJsonFile(string $path): array
    {
        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }
}
