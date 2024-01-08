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
use function stream_get_contents;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');

// Disable MD5 computation for faster uploads, this feature is deprecated
$gridfs = $client->test->selectGridFSBucket(['disableMD5' => true]);

// Create an in-memory stream, this can be any stream source like STDIN or php://input for web requests
$stream = fopen('php://temp', 'w+');
fwrite($stream, 'Hello world!');
rewind($stream);

// Upload to GridFS from the stream
$id = $gridfs->uploadFromStream('hello.txt', $stream);
assert($id instanceof ObjectId);
echo 'Inserted file with ID: ', $id, "\n";
fclose($stream);

// Download the file and print the contents directly to an in-memory stream, chunk by chunk
$stream = fopen('php://temp', 'w+');
$gridfs->downloadToStreamByName('hello.txt', $stream);
rewind($stream);
echo 'File contents: ', stream_get_contents($stream), "\n";

// Delete the file
$gridfs->delete($id);

echo 'Deleted file with ID: ', $id, "\n";
