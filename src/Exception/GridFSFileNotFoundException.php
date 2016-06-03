<?php

namespace MongoDB\Exception;

class GridFSFileNotFoundException extends \MongoDB\Driver\Exception\RuntimeException implements Exception
{
    /**
     * @param string  $filename
     * @param integer $namespace
     */
    public function __construct($filename, $namespace)
    {
        parent::__construct(sprintf('Unable to find file "%s" in namespace "%s"', $filename, $namespace));
    }
}
