<?php

namespace MongoDB\Tests\Command;

use MongoDB\Command\ListDatabases;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Tests\TestCase;

class ListDatabasesTest extends TestCase
{
    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListDatabases($options);
    }

    public function provideInvalidConstructorOptions()
    {
        return $this->createOptionDataProvider([
            'authorizedDatabases' => $this->getInvalidBooleanValues(),
            'filter' => $this->getInvalidDocumentValues(),
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'nameOnly' => $this->getInvalidBooleanValues(),
            'session' => $this->getInvalidSessionValues(),
        ]);
    }
}
