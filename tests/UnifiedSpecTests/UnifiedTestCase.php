<?php

namespace MongoDB\Tests\UnifiedSpecTests;

use Generator;
use IteratorAggregate;
use stdClass;
use Traversable;

use function file_get_contents;
use function MongoDB\BSON\fromJSON;
use function MongoDB\BSON\toPHP;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsObject;
use function PHPUnit\Framework\assertIsString;

/**
 * Unified test case model class.
 *
 * This model corresponds to a single test case (i.e. element in "tests" array)
 * within a JSON object conforming to the unified test format's JSON schema.
 * This test case may be executed by UnifiedTestRunner::run().
 *
 * @see https://github.com/mongodb/specifications/blob/master/source/unified-test-format/unified-test-format.rst
 */
final class UnifiedTestCase implements IteratorAggregate
{
    /** @var stdClass */
    private $test;

    /** @var string */
    private $schemaVersion;

    /** @var array|null */
    private $runOnRequirements;

    /** @var array|null */
    private $createEntities;

    /** @var array|null */
    private $initialData;

    private function __construct(stdClass $test, string $schemaVersion, ?array $runOnRequirements = null, ?array $createEntities = null, ?array $initialData = null)
    {
        $this->test = $test;
        $this->schemaVersion = $schemaVersion;
        $this->runOnRequirements = $runOnRequirements;
        $this->createEntities = $createEntities;
        $this->initialData = $initialData;
    }

    /**
     * Return this object as arguments for UnifiedTestRunner::doTestCase().
     *
     * This allows the UnifiedTest object to be used directly with the argument
     * unpacking operator (i.e. "...").
     *
     * @see https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @see https://php.net/manual/en/functions.arguments.php#functions.variable-arg-list
     */
    public function getIterator(): Traversable
    {
        yield $this->test;
        yield $this->schemaVersion;
        yield $this->runOnRequirements;
        yield $this->createEntities;
        yield $this->initialData;
    }

    /**
     * Yields UnifiedTestCase objects for a JSON file.
     */
    public static function fromFile(string $filename): Generator
    {
        /* Decode the file through the driver's extended JSON parser to ensure
         * proper handling of special types. */
        $json = toPHP(fromJSON(file_get_contents($filename)));

        yield from static::fromJSON($json);
    }

    /**
     * Yields UnifiedTestCase objects for parsed JSON.
     *
     * The top-level and test-level "description" fields will be concatenated
     * and used as the key for each yielded value.
     */
    public static function fromJSON(stdClass $json): Generator
    {
        $description = $json->description;
        $schemaVersion = $json->schemaVersion;
        $runOnRequirements = $json->runOnRequirements ?? null;
        $createEntities = $json->createEntities ?? null;
        $initialData = $json->initialData ?? null;
        $tests = $json->tests;

        /* Assertions in data providers do not count towards test assertions
         * but failures will interrupt the test suite with a warning. */
        assertIsString($description);
        assertIsString($schemaVersion);
        assertIsArray($tests);

        foreach ($tests as $test) {
            assertIsObject($test);
            assertIsString($test->description);

            $name = $description . ': ' . $test->description;

            yield $name => new self($test, $schemaVersion, $runOnRequirements, $createEntities, $initialData);
        }
    }
}
