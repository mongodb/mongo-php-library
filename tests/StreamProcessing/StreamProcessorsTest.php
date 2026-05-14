<?php

namespace MongoDB\Tests\StreamProcessing;

use MongoDB\Driver\Manager;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\StreamProcessor;
use MongoDB\StreamProcessors;
use MongoDB\Tests\TestCase;

class StreamProcessorsTest extends TestCase
{
    private function makeManager(): Manager
    {
        return new Manager('mongodb://localhost:27017/');
    }

    public function testGetReturnsStreamProcessorHandle(): void
    {
        $sp = new StreamProcessors($this->makeManager());
        $handle = $sp->get('proc');
        $this->assertInstanceOf(StreamProcessor::class, $handle);
        $this->assertSame('proc', $handle->getName());
    }

    public function testCreateRejectsEmptyName(): void
    {
        $sp = new StreamProcessors($this->makeManager());
        $this->expectException(InvalidArgumentException::class);
        $sp->create('', []);
    }

    public function testGetInfoRejectsEmptyName(): void
    {
        $sp = new StreamProcessors($this->makeManager());
        $this->expectException(InvalidArgumentException::class);
        $sp->getInfo('');
    }

    public function testStreamProcessorConstructorRejectsEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StreamProcessor($this->makeManager(), '');
    }
}
