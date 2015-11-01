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

    protected function getInvalidArrayValues()
    {
        return [123, 3.14, 'foo', true, new stdClass];
    }

    protected function getInvalidBooleanValues()
    {
        return [123, 3.14, 'foo', [], new stdClass];
    }

    protected function getInvalidDocumentValues()
    {
        return [123, 3.14, 'foo', true];
    }

    protected function getInvalidIntegerValues()
    {
        return [3.14, 'foo', true, [], new stdClass];
    }

    protected function getInvalidStringValues()
    {
        return [123, 3.14, true, [], new stdClass];
    }

    protected function getInvalidReadPreferenceValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass];
    }

    protected function getInvalidWriteConcernValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass];
    }

    protected function wrapValuesForDataProvider(array $values)
    {
        return array_map(function($value) { return [$value]; }, $values);
    }
}
