<?php

namespace MongoDB\Tests;

use ReflectionClass;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Return the test collection name.
     *
     * @return string
     */
    protected function getCollectionName()
    {
         $class = new ReflectionClass($this);

         return sprintf('%s.%s', $class->getShortName(), hash('crc32b', $this->getName()));
    }

    /**
     * Return the test database name.
     *
     * @return string
     */
    protected function getDatabaseName()
    {
        return getenv('MONGODB_DATABASE') ?: 'phplib_test';
    }

    /**
     * Return the test namespace.
     *
     * @return string
     */
    protected function getNamespace()
    {
         return sprintf('%s.%s', $this->getDatabaseName(), $this->getCollectionName());
    }

    /**
     * Return the connection URI.
     *
     * @return string
     */
    protected function getUri()
    {
        return getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017';
    }
}
