<?php

namespace MongoDB\Exception;

class GridFSFileNotFoundException extends \MongoDB\Driver\Exception\RuntimeException implements Exception
{
	public function __construct($fname, $bucketName, $databaseName){
        parent::__construct(sprintf('Unable to find file by: %s in %s.%s', $fname,$databaseName, $bucketName));
	}
}
