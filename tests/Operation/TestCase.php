<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Tests\TestCase as BaseTestCase;
use stdClass;

/**
 * Base class for Operation unit tests.
 */
abstract class TestCase extends BaseTestCase
{
    public function provideInvalidDocumentArguments()
    {
        return array(
            array(null),
            array(123),
            array('foo'),
            array(true),
        );
    }

    public function provideInvalidBooleanArguments()
    {
        return array(
            array(null),
            array(123),
            array('foo'),
            array(array()),
            array(new stdClass()),
        );
    }
}
