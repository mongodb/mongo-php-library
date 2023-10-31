<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder;

use BackedEnum;
use MongoDB\Builder\BuilderEncoder;
use MongoDB\Builder\Pipeline;
use PHPUnit\Framework\TestCase;

use function MongoDB\BSON\fromJSON;
use function MongoDB\BSON\toPHP;
use function var_export;

class PipelineTestCase extends TestCase
{
    final public static function assertSamePipeline(string|BackedEnum $expectedJson, Pipeline $pipeline): void
    {
        if ($expectedJson instanceof BackedEnum) {
            $expectedJson = $expectedJson->value;
        }

        // BSON Documents doesn't support top-level arrays.
        $expected = toPHP(fromJSON('{"root":' . $expectedJson . '}'))->root;

        $codec = new BuilderEncoder();
        $actual = $codec->encode($pipeline);

        self::assertEquals($expected, $actual, var_export($actual, true));
    }
}
