<?php

namespace MongoDB\Tests\Command;

use MongoDB\Command\ListCollections;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Tests\TestCase;

class ListCollectionsTest extends TestCase
{
    /** @dataProvider provideInvalidConstructorOptions */
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ListCollections($this->getDatabaseName(), $options);
    }

    public function provideInvalidConstructorOptions(): array
    {
        return $this->createOptionDataProvider([
            'authorizedCollections' => $this->getInvalidBooleanValues(),
            'filter' => $this->getInvalidDocumentValues(),
            'maxTimeMS' => $this->getInvalidIntegerValues(),
            'session' => $this->getInvalidSessionValues(),
        ]);
    }
}
