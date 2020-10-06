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
use function assertContains;
use function assertEmpty;
use function assertInternalType;
use function assertThat;
use function implode;
use function isType;
use function logicalOr;

final class Util
{
    public static function assertHasOnlyKeys($arrayOrObject, array $keys)
    {
        // TODO: replace isType('object') with instanceOf(stdClass::class)
        assertThat($arrayOrObject, logicalOr(isType('array'), isType('object')));
        $diff = array_diff_key((array) $arrayOrObject, array_fill_keys($keys, 1));
        assertEmpty($diff, 'Unsupported keys: ' . implode(',', array_keys($diff)));
    }

    public static function createReadConcern(stdClass $o) : ReadConcern
    {
        self::assertHasOnlyKeys($o, ['level']);

        $level = $o->level ?? null;
        assertInternalType('string', $level);

        return new ReadConcern($level);
    }

    public static function createReadPreference(stdClass $o) : ReadPreference
    {
        self::assertHasOnlyKeys($o, ['mode', 'tagSets', 'maxStalenessSeconds', 'hedge']);

        $mode = $o->mode ?? null;
        $tagSets = $o->tagSets ?? null;
        $maxStalenessSeconds = $o->maxStalenessSeconds ?? null;
        $hedge = $o->hedge ?? null;

        assertInternalType('string', $mode);

        if (isset($tagSets)) {
            assertInternalType('array', $tagSets);
            assertContains('object', $tagSets);
        }

        $options = [];

        if (isset($maxStalenessSeconds)) {
            assertInternalType('int', $maxStalenessSeconds);
            $options['maxStalenessSeconds'] = $maxStalenessSeconds;
        }

        if (isset($hedge)) {
            assertInternalType('object', $hedge);
            $options['hedge'] = $hedge;
        }

        return new ReadPreference($mode, $tagSets, $options);
    }

    public static function createWriteConcern(stdClass $o) : WriteConcern
    {
        self::assertHasOnlyKeys($o, ['w', 'wtimeoutMS', 'journal']);

        $w = $o->w ?? -2; /* MONGOC_WRITE_CONCERN_W_DEFAULT */
        $wtimeoutMS = $o->wtimeoutMS ?? 0;
        $journal = $o->journal ?? null;

        assertThat($w, logicalOr(isType('int'), isType('string')));
        assertInternalType('int', $wtimeoutMS);

        $args = [$w, $wtimeoutMS];

        if (isset($journal)) {
            assertInternalType('bool', $journal);
            $args[] = $journal;
        }

        return new WriteConcern(...$args);
    }

    public static function prepareCommonOptions(array $options) : array
    {
        if (array_key_exists('readConcern', $options)) {
            assertInternalType('object', $options['readConcern']);
            $options['readConcern'] = self::createReadConcern($options['readConcern']);
        }

        if (array_key_exists('readPreference', $options)) {
            assertInternalType('object', $options['readPreference']);
            $options['readPreference'] = self::createReadPreference($options['readPreference']);
        }

        if (array_key_exists('writeConcern', $options)) {
            assertInternalType('object', $options['writeConcern']);
            $options['writeConcern'] = self::createWriteConcern($options['writeConcern']);
        }

        return $options;
    }
}
