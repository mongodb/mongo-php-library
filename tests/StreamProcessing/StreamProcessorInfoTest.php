<?php

namespace MongoDB\Tests\StreamProcessing;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\StreamProcessorInfo;
use MongoDB\Tests\TestCase;

class StreamProcessorInfoTest extends TestCase
{
    public function testGettersExposeKnownFields(): void
    {
        $info = new StreamProcessorInfo([
            'id' => 'proc-1',
            'name' => 'smokeTestProcessor',
            'state' => 'CREATED',
            'pipeline' => [['$source' => ['connectionName' => 'sample_stream_solar']]],
            'pipelineVersion' => 2,
            'tier' => 'SP2',
            'streamMetaFieldName' => '_stream_meta',
            'enableAutoScaling' => true,
            'failoverEnabled' => false,
            'activeRegion' => 'us-east-1',
            'workspaceDefaultRegion' => 'us-east-1',
            'lastStateChange' => new UTCDateTime(),
            'modifiedBy' => 'user-1',
            'hasStarted' => false,
            'errorMsg' => '',
            'errorRetryable' => false,
        ]);

        $this->assertSame('proc-1', $info->getId());
        $this->assertSame('smokeTestProcessor', $info->getName());
        $this->assertSame('CREATED', $info->getState());
        $this->assertSame(2, $info->getPipelineVersion());
        $this->assertSame('SP2', $info->getTier());
        $this->assertSame('_stream_meta', $info->getStreamMetaFieldName());
        $this->assertTrue($info->isAutoScalingEnabled());
        $this->assertFalse($info->isFailoverEnabled());
        $this->assertSame('us-east-1', $info->getActiveRegion());
        $this->assertSame('user-1', $info->getModifiedBy());
        $this->assertFalse($info->hasStarted());
        $this->assertSame('', $info->getErrorMsg());
        $this->assertFalse($info->isErrorRetryable());
        $this->assertNull($info->getErrorCode());
        $this->assertInstanceOf(UTCDateTime::class, $info->getLastStateChange());
    }

    public function testIdAndPipelineVersionAreOptional(): void
    {
        $info = new StreamProcessorInfo([
            'name' => 'p',
            'state' => 'CREATED',
            'pipeline' => [],
            'errorMsg' => '',
        ]);

        $this->assertNull($info->getId());
        $this->assertNull($info->getPipelineVersion());
        $this->assertSame('', $info->getErrorMsg());
    }

    public function testArrayAccessIsReadOnly(): void
    {
        $info = new StreamProcessorInfo(['name' => 'p', 'state' => 'CREATED']);
        $this->assertSame('p', $info['name']);
        $this->assertTrue(isset($info['state']));
        $this->assertFalse(isset($info['nope']));
    }
}
