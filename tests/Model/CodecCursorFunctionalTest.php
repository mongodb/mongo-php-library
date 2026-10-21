<?php

namespace MongoDB\Tests\Model;

use MongoDB\BSON\Int64;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Model\CodecCursor;
use MongoDB\Tests\FunctionalTestCase;

use const E_USER_WARNING;

class CodecCursorFunctionalTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->dropCollection($this->getDatabaseName(), $this->getCollectionName());
    }

    public function testSetTypeMap(): void
    {
        $collection = self::createTestClient()->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $cursor = $collection->find();

        $codecCursor = CodecCursor::fromCursor($cursor, $this->createMock(DocumentCodec::class));

        $this->assertError(E_USER_WARNING, fn () => $codecCursor->setTypeMap(['root' => 'array']));
    }

    public function testGetIdReturnTypeWithArgument(): void
    {
        $collection = self::createTestClient()->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $cursor = $collection->find();

        $codecCursor = CodecCursor::fromCursor($cursor, $this->createMock(DocumentCodec::class));

        self::assertInstanceOf(Int64::class, $codecCursor->getId());
    }
}
