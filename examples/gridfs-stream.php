<?php

/**
 * This example demonstrates how to use GridFS streams.
 */

declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Client;

use function fclose;
use function feof;
use function fread;
use function fwrite;
use function getenv;
use function strlen;

use const PHP_EOL;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$bucket = $client->test->selectGridFSBucket(['disableMD5' => true]);

// Open a stream for writing, similar to fopen with mode 'w'
$stream = $bucket->openUploadStream('hello.txt');

for ($i = 0; $i < 1_000_000; $i++) {
    fwrite($stream, 'Hello line ' . $i . PHP_EOL);
}

// Last data are flushed to the server when the stream is closed
fclose($stream);

// Open a stream for reading, similar to fopen with mode 'r'
$stream = $bucket->openDownloadStreamByName('hello.txt');

$size = 0;
while (! feof($stream)) {
    $data = fread($stream, 2 ** 10);
    $size += strlen($data);
}

echo 'Read a total of ' . $size . ' bytes' . PHP_EOL;
