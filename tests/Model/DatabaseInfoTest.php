<?php

namespace MongoDB\Tests;

use MongoDB\Model\DatabaseInfo;
use MongoDB\Tests\TestCase;

class DatabaseInfoTest extends TestCase
{
    public function testGetName()
    {
        $info = new DatabaseInfo(array('name' => 'foo'));
        $this->assertSame('foo', $info->getName());
    }

    public function testGetSizeOnDisk()
    {
        $info = new DatabaseInfo(array('sizeOnDisk' => '1048576'));
        $this->assertSame(1048576, $info->getSizeOnDisk());
    }

    public function testIsEmpty()
    {
        $info = new DatabaseInfo(array('empty' => false));
        $this->assertFalse($info->isEmpty());

        $info = new DatabaseInfo(array('empty' => true));
        $this->assertTrue($info->isEmpty());
    }
}
