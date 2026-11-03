<?php

namespace MongoDB\Tests\Model;

use MongoDB\Exception\BadMethodCallException;
use MongoDB\Model\CollectionInfo;
use MongoDB\Tests\TestCase;

class CollectionInfoTest extends TestCase
{
    public function testGetBasicInformation(): void
    {
        $viewInfo = new CollectionInfo([
            'name' => 'foo',
            'type' => 'view',
            'options' => ['capped' => true, 'size' => 1_048_576],
            'info' => ['readOnly' => true],
            'idIndex' => ['idIndex' => true], // Dummy option
        ]);

        $this->assertSame('foo', $viewInfo->getName());
        $this->assertSame('foo', $viewInfo['name']);

        $this->assertTrue($viewInfo->isView());
        $this->assertSame('view', $viewInfo['type']);

        $this->assertSame(['capped' => true, 'size' => 1_048_576], $viewInfo->getOptions());
        $this->assertSame(['capped' => true, 'size' => 1_048_576], $viewInfo['options']);

        $this->assertSame(['readOnly' => true], $viewInfo->getInfo());
        $this->assertSame(['readOnly' => true], $viewInfo['info']);

        $this->assertSame(['idIndex' => true], $viewInfo->getIdIndex());
        $this->assertSame(['idIndex' => true], $viewInfo['idIndex']);

        $collectionInfo = new CollectionInfo([
            'name' => 'bar',
            'type' => 'collection',
        ]);

        $this->assertFalse($collectionInfo->isView());
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

        $info = new CollectionInfo(['name' => 'foo', 'options' => ['capped' => true, 'size' => 1_048_576]]);
        $this->assertTrue($info->isCapped());
        $this->assertNull($info->getCappedMax());
        $this->assertSame(1_048_576, $info->getCappedSize());

        $info = new CollectionInfo(['name' => 'foo', 'options' => ['capped' => true, 'size' => 1_048_576, 'max' => 100]]);
        $this->assertTrue($info->isCapped());
        $this->assertSame(100, $info->getCappedMax());
        $this->assertSame(1_048_576, $info->getCappedSize());
    }

    public function testDebugInfo(): void
    {
        $expectedInfo = [
            'name' => 'foo',
            'options' => ['capped' => true, 'size' => 1_048_576],
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
        $info = new CollectionInfo(['name' => 'foo', 'options' => ['capped' => true, 'size' => 1_048_576]]);
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(CollectionInfo::class . ' is immutable');
        $info['options'] = ['capped' => false];
    }

    public function testOffsetUnsetCannotBeCalled(): void
    {
        $info = new CollectionInfo(['name' => 'foo', 'options' => ['capped' => true, 'size' => 1_048_576]]);
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(CollectionInfo::class . ' is immutable');
        unset($info['options']);
    }
}
