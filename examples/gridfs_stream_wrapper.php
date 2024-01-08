<?php

/**
 * For applications that need to interact with GridFS using only a filename string,
 * a bucket can be registered with an alias. Files can then be accessed using the
 * following pattern: gridfs://<bucket-alias>/<filename>
 */

declare(strict_types=1);

namespace MongoDB\Examples;

use MongoDB\Client;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getenv;
use function stream_context_create;

require __DIR__ . '/../vendor/autoload.php';

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1/');
// Disable MD5 computation for faster uploads, this feature is deprecated
$bucket = $client->test->selectGridFSBucket(['disableMD5' => true]);
$bucket->drop();

// Register the alias "mybucket" for default bucket of the "test" database
$bucket->registerGlobalStreamWrapperAlias('mybucket');

echo 'File exists: ';
echo file_exists('gridfs://mybucket/hello.txt') ? 'yes' : 'no';
echo "\n";

echo 'Writing file';
file_put_contents('gridfs://mybucket/hello.txt', 'Hello, GridFS!');
echo "\n";

echo 'File exists: ';
echo file_exists('gridfs://mybucket/hello.txt') ? 'yes' : 'no';
echo "\n";

echo 'Reading file: ';
echo file_get_contents('gridfs://mybucket/hello.txt');
echo "\n";

echo 'Writing new version of the file';
file_put_contents('gridfs://mybucket/hello.txt', 'Hello, GridFS! (v2)');
echo "\n";

echo 'Reading new version of the file: ';
echo file_get_contents('gridfs://mybucket/hello.txt');
echo "\n";

echo 'Reading previous version of the file: ';
$context = stream_context_create(['gridfs' => ['revision' => -2]]);
echo file_get_contents('gridfs://mybucket/hello.txt', false, $context);
echo "\n";
