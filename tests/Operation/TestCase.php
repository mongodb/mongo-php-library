<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Tests\TestCase as BaseTestCase;
use stdClass;

/**
 * Base class for Operation unit tests.
 */
abstract class TestCase extends BaseTestCase
{
    public function provideInvalidDocumentValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidDocumentValues());
    }

    public function provideInvalidBooleanValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidBooleanValues());
    }

    protected function getInvalidBooleanValues()
    {
        return array(123, 3.14, 'foo', array(), new stdClass);
    }

    protected function getInvalidDocumentValues()
    {
        return array(123, 3.14, 'foo', true);
    }

    protected function getInvalidIntegerValues()
    {
        return array(3.14, 'foo', true, array(), new stdClass);
    }

    protected function getInvalidStringValues()
    {
        return array(123, 3.14, true, array(), new stdClass);
    }

    protected function getInvalidWriteConcernValues()
    {
        return array(123, 3.14, 'foo', true, array(), new stdClass);
    }

    protected function wrapValuesForDataProvider(array $values)
    {
        return array_map(function($value) { return array($value); }, $values);
    }
}
