<?php

namespace MongoDB\Tests\Model;

use MongoDB\Codec\DocumentCodec;
use MongoDB\Model\CodecCursor;
use MongoDB\Tests\FunctionalTestCase;

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

        $this->expectWarning();
        $this->expectWarningMessage('Discarding type map for MongoDB\Model\CodecCursor::setTypeMap');

        $codecCursor->setTypeMap(['root' => 'array']);
    }
}
