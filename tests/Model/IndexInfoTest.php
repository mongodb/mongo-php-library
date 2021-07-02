<?php

namespace MongoDB\Tests\Model;

use MongoDB\Exception\BadMethodCallException;
use MongoDB\Model\IndexInfo;
use MongoDB\Tests\TestCase;

class IndexInfoTest extends TestCase
{
    public function testBasicIndex(): void
    {
        $info = new IndexInfo([
            'v' => 1,
            'key' => ['x' => 1],
            'name' => 'x_1',
            'ns' => 'foo.bar',
        ]);

        $this->assertSame(1, $info->getVersion());
        $this->assertSame(['x' => 1], $info->getKey());
        $this->assertSame('x_1', $info->getName());
        $this->assertSame('foo.bar', $info->getNamespace());
        $this->assertFalse($info->is2dSphere());
        $this->assertFalse($info->isGeoHaystack());
        $this->assertFalse($info->isSparse());
        $this->assertFalse($info->isText());
        $this->assertFalse($info->isTtl());
        $this->assertFalse($info->isUnique());
    }

    public function testSparseIndex(): void
    {
        $info = new IndexInfo([
            'v' => 1,
            'key' => ['y' => 1],
            'name' => 'y_sparse',
            'ns' => 'foo.bar',
            'sparse' => true,
        ]);

        $this->assertSame(1, $info->getVersion());
        $this->assertSame(['y' => 1], $info->getKey());
        $this->assertSame('y_sparse', $info->getName());
        $this->assertSame('foo.bar', $info->getNamespace());
        $this->assertFalse($info->is2dSphere());
        $this->assertFalse($info->isGeoHaystack());
        $this->assertTrue($info->isSparse());
        $this->assertFalse($info->isText());
        $this->assertFalse($info->isTtl());
        $this->assertFalse($info->isUnique());
    }

    public function testUniqueIndex(): void
    {
        $info = new IndexInfo([
            'v' => 1,
            'key' => ['z' => 1],
            'name' => 'z_unique',
            'ns' => 'foo.bar',
            'unique' => true,
        ]);

        $this->assertSame(1, $info->getVersion());
        $this->assertSame(['z' => 1], $info->getKey());
        $this->assertSame('z_unique', $info->getName());
        $this->assertSame('foo.bar', $info->getNamespace());
        $this->assertFalse($info->is2dSphere());
        $this->assertFalse($info->isGeoHaystack());
        $this->assertFalse($info->isSparse());
        $this->assertFalse($info->isText());
        $this->assertFalse($info->isTtl());
        $this->assertTrue($info->isUnique());
    }

    public function testTtlIndex(): void
    {
        $info = new IndexInfo([
            'v' => 1,
            'key' => ['z' => 1],
            'name' => 'z_unique',
            'ns' => 'foo.bar',
            'expireAfterSeconds' => 100,
        ]);

        $this->assertSame(1, $info->getVersion());
        $this->assertSame(['z' => 1], $info->getKey());
        $this->assertSame('z_unique', $info->getName());
        $this->assertSame('foo.bar', $info->getNamespace());
        $this->assertFalse($info->is2dSphere());
        $this->assertFalse($info->isGeoHaystack());
        $this->assertFalse($info->isSparse());
        $this->assertFalse($info->isText());
        $this->assertTrue($info->isTtl());
        $this->assertFalse($info->isUnique());
        $this->assertArrayHasKey('expireAfterSeconds', $info);
        $this->assertSame(100, $info['expireAfterSeconds']);
    }

    public function testDebugInfo(): void
    {
        $expectedInfo = [
            'v' => 1,
            'key' => ['x' => 1],
            'name' => 'x_1',
            'ns' => 'foo.bar',
        ];

        $info = new IndexInfo($expectedInfo);
        $this->assertSame($expectedInfo, $info->__debugInfo());
    }

    public function testImplementsArrayAccess(): void
    {
        $info = new IndexInfo([
            'v' => 1,
            'key' => ['x' => 1],
            'name' => 'x_1',
            'ns' => 'foo.bar',
        ]);

        $this->assertInstanceOf('ArrayAccess', $info);
        $this->assertArrayHasKey('name', $info);
        $this->assertSame('x_1', $info['name']);
    }

    public function testOffsetSetCannotBeCalled(): void
    {
        $info = new IndexInfo([
            'v' => 1,
            'key' => ['x' => 1],
            'name' => 'x_1',
            'ns' => 'foo.bar',
        ]);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(IndexInfo::class . ' is immutable');
        $info['v'] = 2;
    }

    public function testOffsetUnsetCannotBeCalled(): void
    {
        $info = new IndexInfo([
            'v' => 1,
            'key' => ['x' => 1],
            'name' => 'x_1',
            'ns' => 'foo.bar',
        ]);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(IndexInfo::class . ' is immutable');
        unset($info['v']);
    }

    public function testIs2dSphere(): void
    {
        $info = new IndexInfo([
            'v' => 2,
            'key' => ['pos' => '2dsphere'],
            'name' => 'pos_2dsphere',
            'ns' => 'foo.bar',
        ]);

        $this->assertSame(2, $info->getVersion());
        $this->assertSame(['pos' => '2dsphere'], $info->getKey());
        $this->assertSame('pos_2dsphere', $info->getName());
        $this->assertSame('foo.bar', $info->getNamespace());
        $this->assertTrue($info->is2dSphere());
        $this->assertFalse($info->isGeoHaystack());
        $this->assertFalse($info->isSparse());
        $this->assertFalse($info->isText());
        $this->assertFalse($info->isTtl());
        $this->assertFalse($info->isUnique());
    }

    public function testIsGeoHaystack(): void
    {
        $info = new IndexInfo([
            'v' => 2,
            'key' => ['pos2' => 'geoHaystack', 'x' => 1],
            'name' => 'pos2_geoHaystack_x_1',
            'ns' => 'foo.bar',
        ]);

        $this->assertSame(2, $info->getVersion());
        $this->assertSame(['pos2' => 'geoHaystack', 'x' => 1], $info->getKey());
        $this->assertSame('pos2_geoHaystack_x_1', $info->getName());
        $this->assertSame('foo.bar', $info->getNamespace());
        $this->assertFalse($info->is2dSphere());
        $this->assertTrue($info->isGeoHaystack());
        $this->assertFalse($info->isSparse());
        $this->assertFalse($info->isText());
        $this->assertFalse($info->isTtl());
        $this->assertFalse($info->isUnique());
    }

    public function testIsText(): void
    {
        $info = new IndexInfo([
            'v' => 2,
            'key' => ['_fts' => 'text', '_ftsx' => 1],
            'name' => 'title_text_description_text',
            'ns' => 'foo.bar',
        ]);

        $this->assertSame(2, $info->getVersion());
        $this->assertSame(['_fts' => 'text', '_ftsx' => 1], $info->getKey());
        $this->assertSame('title_text_description_text', $info->getName());
        $this->assertSame('foo.bar', $info->getNamespace());
        $this->assertFalse($info->is2dSphere());
        $this->assertFalse($info->isGeoHaystack());
        $this->assertFalse($info->isSparse());
        $this->assertTrue($info->isText());
        $this->assertFalse($info->isTtl());
        $this->assertFalse($info->isUnique());
    }
}
