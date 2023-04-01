<?php

namespace MongoDB\Tests\Exception;

use Exception;
use MongoDB\Exception\CreateEncryptedCollectionException;
use MongoDB\Tests\TestCase;

class CreateEncryptedCollectionExceptionTest extends TestCase
{
    public function testGetEncryptedFields(): void
    {
        $encryptedFields = ['fields' => []];

        $e = new CreateEncryptedCollectionException(new Exception(), $encryptedFields);
        $this->assertSame($encryptedFields, $e->getEncryptedFields());
    }

    public function testGetPrevious(): void
    {
        $previous = new Exception();

        $e = new CreateEncryptedCollectionException($previous, ['fields' => []]);
        $this->assertSame($previous, $e->getPrevious());
    }
}
