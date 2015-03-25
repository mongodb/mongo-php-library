<?php

namespace MongoDB\Tests;

use ReflectionClass;
use ReflectionMethod;

class CollectionTest extends TestCase
{
    public function testMethodOrder()
    {
        $class = new ReflectionClass('MongoDB\Collection');

        $filters = array(
            'public' => ReflectionMethod::IS_PUBLIC,
            'protected' => ReflectionMethod::IS_PROTECTED,
            'private' => ReflectionMethod::IS_PRIVATE,
        );

        foreach ($filters as $visibility => $filter) {
            $methods = array_map(
                function(ReflectionMethod $method) { return $method->getName(); },
                $class->getMethods($filter)
            );

            $sortedMethods = $methods;
            sort($sortedMethods);

            $this->assertEquals($methods, $sortedMethods, sprintf('%s methods are declared alphabetically', ucfirst($visibility)));
        }
    }
}
