<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder;

use BackedEnum;
use MongoDB\BSON\Document;
use MongoDB\Builder\BuilderEncoder;
use PHPUnit\Framework\TestCase;

use function json_decode;
use function json_encode;

abstract class UpdateTestCase extends TestCase
{
    /**
     * Assert that the update matches the expected JSON representation.
     *
     * The expected JSON contains both "filter" and "update" fields, but only the "update" field is tested.
     *
     * @param string|BackedEnum $expectedJson Expected JSON with "filter" and "update" keys
     * @param object|array      $update       Update document
     */
    final public static function assertSameUpdate(string|BackedEnum $expectedJson, object|array $update): void
    {
        if ($expectedJson instanceof BackedEnum) {
            $expectedJson = $expectedJson->value;
        }

        // Extract only the "update" field from the expected JSON
        $expectedData = json_decode($expectedJson, true);
        $expected = json_encode(['update' => $expectedData['update']]);

        $codec = new BuilderEncoder();
        $actual = $codec->encode($update);

        $actual = Document::fromPHP(['update' => $actual])->toCanonicalExtendedJSON();

        self::assertJsonStringEqualsJsonString($expected, $actual);
    }
}
