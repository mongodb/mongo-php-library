<?php

namespace MongoDB\Tests\Model;

use MongoDB\Exception\BadMethodCallException;
use MongoDB\Model\DatabaseInfo;
use MongoDB\Tests\TestCase;

class DatabaseInfoTest extends TestCase
{
    public function testGetName(): void
    {
        $info = new DatabaseInfo(['name' => 'foo']);
        $this->assertSame('foo', $info->getName());
    }

    public function testGetSizeOnDisk(): void
    {
        $info = new DatabaseInfo(['sizeOnDisk' => 1048576]);
        $this->assertSame(1048576, $info->getSizeOnDisk());
    }

    public function testIsEmpty(): void
    {
        $info = new DatabaseInfo(['empty' => false]);
        $this->assertFalse($info->isEmpty());

        $info = new DatabaseInfo(['empty' => true]);
        $this->assertTrue($info->isEmpty());
    }

    public function testDebugInfo(): void
    {
        $expectedInfo = [
            'name' => 'foo',
            'sizeOnDisk' => 1048576,
            'empty' => false,
        ];

        $info = new DatabaseInfo($expectedInfo);
        $this->assertSame($expectedInfo, $info->__debugInfo());
    }

    public function testImplementsArrayAccess(): void
    {
        $info = new DatabaseInfo(['name' => 'foo']);
        $this->assertInstanceOf('ArrayAccess', $info);
        $this->assertArrayHasKey('name', $info);
        $this->assertSame('foo', $info['name']);
    }

    public function testOffsetSetCannotBeCalled(): void
    {
        $info = new DatabaseInfo(['name' => 'foo', 'sizeOnDisk' => 1048576, 'empty' => false]);
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(DatabaseInfo::class . ' is immutable');
        $info['empty'] = true;
    }

    public function testOffsetUnsetCannotBeCalled(): void
    {
        $info = new DatabaseInfo(['name' => 'foo', 'sizeOnDisk' => 1048576, 'empty' => false]);
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(DatabaseInfo::class . ' is immutable');
        unset($info['empty']);
    }
}
