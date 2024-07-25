<?php

namespace MongoDB\Tests\Model;

use MongoDB\BSON\Int64;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Driver\CursorId;
use MongoDB\Model\CodecCursor;
use MongoDB\Tests\FunctionalTestCase;

use function restore_error_handler;
use function set_error_handler;

use const E_DEPRECATED;
use const E_USER_DEPRECATED;

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

    public function testGetIdReturnTypeWithoutArgument(): void
    {
        $collection = self::createTestClient()->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $cursor = $collection->find();

        $codecCursor = CodecCursor::fromCursor($cursor, $this->createMock(DocumentCodec::class));

        $deprecations = [];

        try {
            $previousErrorHandler = set_error_handler(
                function (...$args) use (&$previousErrorHandler, &$deprecations) {
                    $deprecations[] = $args;

                    return true;
                },
                E_USER_DEPRECATED | E_DEPRECATED,
            );

            $cursorId = $codecCursor->getId();
        } finally {
            restore_error_handler();
        }

        self::assertInstanceOf(CursorId::class, $cursorId);

        // Expect 2 deprecations: 1 from CodecCursor, one from Cursor
        self::assertCount(2, $deprecations);
        self::assertSame(
            'The method "MongoDB\Model\CodecCursor::getId" will no longer return a "MongoDB\Driver\CursorId" instance in the future. Pass "true" as argument to change to the new behavior and receive a "MongoDB\BSON\Int64" instance instead.',
            $deprecations[0][1],
        );
        self::assertSame(
            'MongoDB\Driver\Cursor::getId(): The method "MongoDB\Driver\Cursor::getId" will no longer return a "MongoDB\Driver\CursorId" instance in the future. Pass "true" as argument to change to the new behavior and receive a "MongoDB\BSON\Int64" instance instead.',
            $deprecations[1][1],
        );
    }

    public function testGetIdReturnTypeWithArgument(): void
    {
        $collection = self::createTestClient()->selectCollection($this->getDatabaseName(), $this->getCollectionName());
        $cursor = $collection->find();

        $codecCursor = CodecCursor::fromCursor($cursor, $this->createMock(DocumentCodec::class));

        $deprecations = [];

        try {
            $previousErrorHandler = set_error_handler(
                function (...$args) use (&$previousErrorHandler, &$deprecations) {
                    $deprecations[] = $args;

                    return true;
                },
                E_USER_DEPRECATED | E_DEPRECATED,
            );

            $cursorId = $codecCursor->getId(true);
        } finally {
            restore_error_handler();
        }

        self::assertInstanceOf(Int64::class, $cursorId);
        self::assertCount(0, $deprecations);
    }
}
