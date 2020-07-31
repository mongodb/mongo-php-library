<?php

$uriField = isset($argv[1]) ? $argv[1] : 'mongodb_uri';
$uriSuffix = isset($argv[2]) ? $argv[2] : '';

echo json_decode(file_get_contents("php://stdin"))->$uriField, $uriSuffix;
