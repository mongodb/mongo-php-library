<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder;

use BackedEnum;
use MongoDB\BSON\Document;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Pipeline;
use PHPUnit\Framework\TestCase;

abstract class PipelineTestCase extends TestCase
{
    final public static function assertSamePipeline(string|BackedEnum $expectedJson, Pipeline $pipeline): void
    {
        if ($expectedJson instanceof BackedEnum) {
            $expectedJson = $expectedJson->value;
        }

        // BSON Documents doesn't support top-level arrays.
        $expected = '{"pipeline":' . $expectedJson . '}';

        $codec = new BuilderEncoder();
        $actual = $codec->encode($pipeline);
        // Normalize with BSON round-trip
        $actual = Document::fromPHP(['pipeline' => $actual])->toCanonicalExtendedJSON();

        self::assertJsonStringEqualsJsonString($expected, $actual);
    }
}
