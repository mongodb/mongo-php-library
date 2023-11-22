<?php

use MongoDB\Client;

require __DIR__ . '/../../../vendor/autoload.php';

$client = new Client(getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017/?serverSelectionTimeoutMS=100');
$database = $client->selectDatabase(getenv('MONGODB_DATABASE') ?: 'phplib_test');
$gridfs = $database->selectGridFSBucket();
$stream = $gridfs->openUploadStream('hello.txt');
fwrite($stream, 'Hello MongoDB!');

// The WriteStream must be closed and the file inserted automatically
