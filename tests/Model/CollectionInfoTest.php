<?php

namespace MongoDB\Tests\Model;

use MongoDB\Exception\BadMethodCallException;
use MongoDB\Model\CollectionInfo;
use MongoDB\Tests\TestCase;

class CollectionInfoTest extends TestCase
{
    public function testGetBasicInformation(): void
    {
        $info = new CollectionInfo([
            'name' => 'foo',
            'type' => 'view',
            'options' => ['capped' => true, 'size' => 1048576],
            'info' => ['readOnly' => true],
            'idIndex' => ['idIndex' => true], // Dummy option
        ]);

        $this->assertSame('foo', $info->getName());
        $this->assertSame('foo', $info['name']);

        $this->assertSame('view', $info->getType());
        $this->assertSame('view', $info['type']);

        $this->assertSame(['capped' => true, 'size' => 1048576], $info->getOptions());
        $this->assertSame(['capped' => true, 'size' => 1048576], $info['options']);

        $this->assertSame(['readOnly' => true], $info->getInfo());
        $this->assertSame(['readOnly' => true], $info['info']);

        $this->assertSame(['idIndex' => true], $info->getIdIndex());
        $this->assertSame(['idIndex' => true], $info['idIndex']);
    }

    public function testMissingFields(): void
    {
        $info = new CollectionInfo([
            'name' => 'foo',
            'type' => 'view',
        ]);

        $this->assertSame([], $info->getOptions());
        $this->assertArrayNotHasKey('options', $info);

        $this->assertSame([], $info->getInfo());
        $this->assertArrayNotHasKey('info', $info);

        $this->assertSame([], $info->getIdIndex());
        $this->assertArrayNotHasKey('idIndex', $info);
    }

    public function testCappedCollectionMethods(): void
    {
        $info = new CollectionInfo(['name' => 'foo']);
        $this->assertFalse($info->isCapped());
        $this->assertNull($info->getCappedMax());
        $this->assertNull($info->getCappedSize());

        $info = new CollectionInfo(['name' => 'foo', 'options' => ['capped' => true, 'size' => 1048576]]);
        $this->assertTrue($info->isCapped());
        $this->assertNull($info->getCappedMax());
        $this->assertSame(1048576, $info->getCappedSize());

        $info = new CollectionInfo(['name' => 'foo', 'options' => ['capped' => true, 'size' => 1048576, 'max' => 100]]);
        $this->assertTrue($info->isCapped());
        $this->assertSame(100, $info->getCappedMax());
        $this->assertSame(1048576, $info->getCappedSize());
    }

    public function testDebugInfo(): void
    {
        $expectedInfo = [
            'name' => 'foo',
            'options' => ['capped' => true, 'size' => 1048576],
        ];

        $info = new CollectionInfo($expectedInfo);
        $this->assertSame($expectedInfo, $info->__debugInfo());
    }

    public function testImplementsArrayAccess(): void
    {
        $info = new CollectionInfo(['name' => 'foo']);
        $this->assertInstanceOf('ArrayAccess', $info);
        $this->assertArrayHasKey('name', $info);
        $this->assertSame('foo', $info['name']);
    }

    public function testOffsetSetCannotBeCalled(): void
    {
        $info = new CollectionInfo(['name' => 'foo', 'options' => ['capped' => true, 'size' => 1048576]]);
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(CollectionInfo::class . ' is immutable');
        $info['options'] = ['capped' => false];
    }

    public function testOffsetUnsetCannotBeCalled(): void
    {
        $info = new CollectionInfo(['name' => 'foo', 'options' => ['capped' => true, 'size' => 1048576]]);
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(CollectionInfo::class . ' is immutable');
        unset($info['options']);
    }
}
