<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use stdClass;

use function array_diff_key;
use function array_fill_keys;
use function array_key_exists;
use function array_keys;
use function implode;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsBool;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\isType;
use function PHPUnit\Framework\logicalOr;

final class Util
{
    public static function assertHasOnlyKeys($arrayOrObject, array $keys): void
    {
        assertThat($arrayOrObject, logicalOr(isType('array'), isInstanceOf(stdClass::class)));
        $diff = array_diff_key((array) $arrayOrObject, array_fill_keys($keys, 1));
        assertEmpty($diff, 'Unsupported keys: ' . implode(',', array_keys($diff)));
    }

    public static function createReadConcern(stdClass $o): ReadConcern
    {
        self::assertHasOnlyKeys($o, ['level']);

        $level = $o->level ?? null;
        assertIsString($level);

        return new ReadConcern($level);
    }

    public static function createReadPreference(stdClass $o): ReadPreference
    {
        self::assertHasOnlyKeys($o, ['mode', 'tagSets', 'maxStalenessSeconds', 'hedge']);

        $mode = $o->mode ?? null;
        $tagSets = $o->tagSets ?? null;
        $maxStalenessSeconds = $o->maxStalenessSeconds ?? null;
        $hedge = $o->hedge ?? null;

        assertIsString($mode);

        if (isset($tagSets)) {
            assertIsArray($tagSets);
            assertContains('object', $tagSets);
        }

        $options = [];

        if (isset($maxStalenessSeconds)) {
            assertIsInt($maxStalenessSeconds);
            $options['maxStalenessSeconds'] = $maxStalenessSeconds;
        }

        if (isset($hedge)) {
            assertIsObject($hedge);
            $options['hedge'] = $hedge;
        }

        return new ReadPreference($mode, $tagSets, $options);
    }

    public static function createWriteConcern(stdClass $o): WriteConcern
    {
        self::assertHasOnlyKeys($o, ['w', 'wtimeoutMS', 'journal']);

        $w = $o->w ?? -2; /* MONGOC_WRITE_CONCERN_W_DEFAULT */
        $wtimeoutMS = $o->wtimeoutMS ?? 0;
        $journal = $o->journal ?? null;

        assertThat($w, logicalOr(isType('int'), isType('string')));
        assertIsInt($wtimeoutMS);

        $args = [$w, $wtimeoutMS];

        if (isset($journal)) {
            assertIsBool($journal);
            $args[] = $journal;
        }

        return new WriteConcern(...$args);
    }

    public static function prepareCommonOptions(array $options): array
    {
        if (array_key_exists('readConcern', $options)) {
            assertIsObject($options['readConcern']);
            $options['readConcern'] = self::createReadConcern($options['readConcern']);
        }

        if (array_key_exists('readPreference', $options)) {
            assertIsObject($options['readPreference']);
            $options['readPreference'] = self::createReadPreference($options['readPreference']);
        }

        if (array_key_exists('writeConcern', $options)) {
            assertIsObject($options['writeConcern']);
            $options['writeConcern'] = self::createWriteConcern($options['writeConcern']);
        }

        return $options;
    }
}
