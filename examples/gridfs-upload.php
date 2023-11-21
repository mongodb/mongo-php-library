<?php

/**
 * This example demonstrates how to upload and download files from a stream with GridFS.
 */

declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\BSON\ObjectId;
use MongoDB\Client;

use function assert;
use function fclose;
use function fopen;
use function fwrite;
use function getenv;
use function rewind;

use const PHP_EOL;
use const STDOUT;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

$gridfs = $client->selectDatabase('test')->selectGridFSBucket();

// Create an in-memory stream, this can be any stream source like STDIN or php://input for web requests
$stream = fopen('php://temp', 'w+');
fwrite($stream, 'Hello world!');
rewind($stream);

// Upload to GridFS from the stream
$id = $gridfs->uploadFromStream('hello.txt', $stream);
assert($id instanceof ObjectId);
echo 'Inserted file with ID: ' . $id . PHP_EOL;
fclose($stream);

// Download the file and print the contents directly to STDOUT, chunk by chunk
echo 'File contents: ';
$gridfs->downloadToStreamByName('hello.txt', STDOUT);
echo PHP_EOL;

// Delete the file
$gridfs->delete($id);
