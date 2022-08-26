<?php

namespace MongoDB\Tests;

use InvalidArgumentException;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;
use stdClass;
use Traversable;

use function array_map;
use function array_merge;
use function array_values;
use function call_user_func;
use function getenv;
use function hash;
use function is_array;
use function is_object;
use function is_string;
use function iterator_to_array;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toJSON;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;

use const E_USER_DEPRECATED;

abstract class TestCase extends BaseTestCase
{
    /**
     * Return the connection URI.
     */
    public static function getUri(): string
    {
        return getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017';
    }

    /**
     * Asserts that a document has expected values for some fields.
     *
     * Only fields in the expected document will be checked. The actual document
     * may contain additional fields.
     *
     * @param array|object $expectedDocument
     * @param array|object $actualDocument
     */
    public function assertMatchesDocument($expectedDocument, $actualDocument): void
    {
        $normalizedExpectedDocument = $this->normalizeBSON($expectedDocument);
        $normalizedActualDocument = $this->normalizeBSON($actualDocument);

        $extraKeys = [];

        /* Avoid unsetting fields while we're iterating on the ArrayObject to
         * work around https://bugs.php.net/bug.php?id=70246 */
        foreach ($normalizedActualDocument as $key => $value) {
            if (! $normalizedExpectedDocument->offsetExists($key)) {
                $extraKeys[] = $key;
            }
        }

        foreach ($extraKeys as $key) {
            $normalizedActualDocument->offsetUnset($key);
        }

        $this->assertEquals(
            toJSON(fromPHP($normalizedExpectedDocument)),
            toJSON(fromPHP($normalizedActualDocument))
        );
    }

    /**
     * Asserts that a document has expected values for all fields.
     *
     * The actual document will be compared directly with the expected document
     * and may not contain extra fields.
     *
     * @param array|object $expectedDocument
     * @param array|object $actualDocument
     */
    public function assertSameDocument($expectedDocument, $actualDocument): void
    {
        $this->assertEquals(
            toJSON(fromPHP($this->normalizeBSON($expectedDocument))),
            toJSON(fromPHP($this->normalizeBSON($actualDocument)))
        );
    }

    public function assertSameDocuments(array $expectedDocuments, $actualDocuments): void
    {
        if ($actualDocuments instanceof Traversable) {
            $actualDocuments = iterator_to_array($actualDocuments);
        }

        if (! is_array($actualDocuments)) {
            throw new InvalidArgumentException('$actualDocuments is not an array or Traversable');
        }

        $normalizeRootDocuments = function ($document) {
            return toJSON(fromPHP($this->normalizeBSON($document)));
        };

        $this->assertEquals(
            array_map($normalizeRootDocuments, $expectedDocuments),
            array_map($normalizeRootDocuments, $actualDocuments)
        );
    }

    /**
     * Compatibility method as PHPUnit 9 no longer includes this method.
     */
    public function dataDescription(): string
    {
        $dataName = $this->dataName();

        return is_string($dataName) ? $dataName : '';
    }

    public function provideInvalidArrayValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidArrayValues());
    }

    public function provideInvalidDocumentValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidDocumentValues());
    }

    public function provideInvalidIntegerValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidIntegerValues());
    }

    protected function assertDeprecated(callable $execution): void
    {
        $errors = [];

        set_error_handler(function ($errno, $errstr) use (&$errors): void {
            $errors[] = $errstr;
        }, E_USER_DEPRECATED);

        try {
            call_user_func($execution);
        } finally {
            restore_error_handler();
        }

        $this->assertCount(1, $errors);
    }

    /**
     * Return the test collection name.
     */
    protected function getCollectionName(): string
    {
        $class = new ReflectionClass($this);

        return sprintf('%s.%s', $class->getShortName(), hash('crc32b', $this->getName()));
    }

    /**
     * Return the test database name.
     */
    protected function getDatabaseName(): string
    {
        return getenv('MONGODB_DATABASE') ?: 'phplib_test';
    }

    /**
     * Return a list of invalid array values.
     */
    protected function getInvalidArrayValues(bool $includeNull = false): array
    {
        return array_merge([123, 3.14, 'foo', true, new stdClass()], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid boolean values.
     */
    protected function getInvalidBooleanValues(bool $includeNull = false): array
    {
        return array_merge([123, 3.14, 'foo', [], new stdClass()], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid document values.
     */
    protected function getInvalidDocumentValues(bool $includeNull = false): array
    {
        return array_merge([123, 3.14, 'foo', true], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid integer values.
     */
    protected function getInvalidIntegerValues(bool $includeNull = false): array
    {
        return array_merge([3.14, 'foo', true, [], new stdClass()], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid ReadPreference values.
     */
    protected function getInvalidReadConcernValues(bool $includeNull = false): array
    {
        return array_merge([123, 3.14, 'foo', true, [], new stdClass(), new ReadPreference(ReadPreference::RP_PRIMARY), new WriteConcern(1)], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid ReadPreference values.
     */
    protected function getInvalidReadPreferenceValues(bool $includeNull = false): array
    {
        return array_merge([123, 3.14, 'foo', true, [], new stdClass(), new ReadConcern(), new WriteConcern(1)], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid Session values.
     */
    protected function getInvalidSessionValues(bool $includeNull = false): array
    {
        return array_merge([123, 3.14, 'foo', true, [], new stdClass(), new ReadConcern(), new ReadPreference(ReadPreference::RP_PRIMARY), new WriteConcern(1)], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid string values.
     */
    protected function getInvalidStringValues(bool $includeNull = false): array
    {
        return array_merge([123, 3.14, true, [], new stdClass()], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid WriteConcern values.
     */
    protected function getInvalidWriteConcernValues(bool $includeNull = false): array
    {
        return array_merge([123, 3.14, 'foo', true, [], new stdClass(), new ReadConcern(), new ReadPreference(ReadPreference::RP_PRIMARY)], $includeNull ? [null] : []);
    }

    /**
     * Return the test namespace.
     */
    protected function getNamespace(): string
    {
         return sprintf('%s.%s', $this->getDatabaseName(), $this->getCollectionName());
    }

    /**
     * Wrap a list of values for use as a single-argument data provider.
     *
     * @param array $values List of values
     */
    protected function wrapValuesForDataProvider(array $values): array
    {
        return array_map(function ($value) {
            return [$value];
        }, $values);
    }

    /**
     * Normalizes a BSON document or array for use with assertEquals().
     *
     * The argument will be converted to a BSONArray or BSONDocument based on
     * its type and keys. Document fields will be sorted alphabetically. Each
     * value within the array or document will then be normalized recursively.
     *
     * @param array|object $bson
     * @return BSONDocument|BSONArray
     * @throws InvalidArgumentException if $bson is not an array or object
     */
    private function normalizeBSON($bson)
    {
        if (! is_array($bson) && ! is_object($bson)) {
            throw new InvalidArgumentException('$bson is not an array or object');
        }

        if ($bson instanceof BSONArray || (is_array($bson) && $bson === array_values($bson))) {
            if (! $bson instanceof BSONArray) {
                $bson = new BSONArray($bson);
            }
        } else {
            if (! $bson instanceof BSONDocument) {
                $bson = new BSONDocument((array) $bson);
            }

            $bson->ksort();
        }

        foreach ($bson as $key => $value) {
            if ($value instanceof BSONArray || (is_array($value) && $value === array_values($value))) {
                $bson[$key] = $this->normalizeBSON($value);
                continue;
            }

            if ($value instanceof stdClass || $value instanceof BSONDocument || is_array($value)) {
                $bson[$key] = $this->normalizeBSON($value);
                continue;
            }
        }

        return $bson;
    }
}
