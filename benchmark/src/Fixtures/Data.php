<?php

namespace MongoDB\Benchmark\Fixtures;

use function file_get_contents;
use function fopen;
use function fwrite;
use function json_decode;
use function rewind;
use function str_repeat;

use const JSON_THROW_ON_ERROR;

final class Data
{
    public const DEEP_BSON_PATH = __DIR__ . '/data/deep_bson.json';
    public const FLAT_BSON_PATH = __DIR__ . '/data/flat_bson.json';
    public const FULL_BSON_PATH = __DIR__ . '/data/full_bson.json';
    public const LARGE_FILE_PATH = __DIR__ . '/data/large_doc.json';
    public const SMALL_FILE_PATH = __DIR__ . '/data/small_doc.json';
    public const TWEET_FILE_PATH = __DIR__ . '/data/tweet.json';
    public const LDJSON_FILE_PATH = __DIR__ . '/data/ldjson.json';

    public static function readJsonFile(string $path): array
    {
        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Generates an in-memory stream of the given size.
     *
     * @return resource
     */
    public static function getStream(int $size)
    {
        $stream = fopen('php://memory', 'w+');
        fwrite($stream, str_repeat("\0", $size));
        rewind($stream);

        return $stream;
    }
}
