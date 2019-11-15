<?php

namespace MongoDB\Tests;

use InvalidArgumentException;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Tests\Compat\PolyfillAssertTrait;
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
use function iterator_to_array;
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toJSON;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use const E_USER_DEPRECATED;

abstract class TestCase extends BaseTestCase
{
    use PolyfillAssertTrait;

    /**
     * Return the connection URI.
     *
     * @return string
     */
    public static function getUri()
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
    public function assertMatchesDocument($expectedDocument, $actualDocument)
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
    public function assertSameDocument($expectedDocument, $actualDocument)
    {
        $this->assertEquals(
            toJSON(fromPHP($this->normalizeBSON($expectedDocument))),
            toJSON(fromPHP($this->normalizeBSON($actualDocument)))
        );
    }

    public function assertSameDocuments(array $expectedDocuments, $actualDocuments)
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

    public function provideInvalidArrayValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidArrayValues());
    }

    public function provideInvalidDocumentValues()
    {
        return $this->wrapValuesForDataProvider($this->getInvalidDocumentValues());
    }

    protected function assertDeprecated(callable $execution)
    {
        $errors = [];

        set_error_handler(function ($errno, $errstr) use (&$errors) {
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
     *
     * @return string
     */
    protected function getCollectionName()
    {
        $class = new ReflectionClass($this);

        return sprintf('%s.%s', $class->getShortName(), hash('crc32b', $this->getName()));
    }

    /**
     * Return the test database name.
     *
     * @return string
     */
    protected function getDatabaseName()
    {
        return getenv('MONGODB_DATABASE') ?: 'phplib_test';
    }

    /**
     * Return a list of invalid array values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidArrayValues($includeNull = false)
    {
        return array_merge([123, 3.14, 'foo', true, new stdClass()], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid boolean values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidBooleanValues($includeNull = false)
    {
        return array_merge([123, 3.14, 'foo', [], new stdClass()], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid document values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidDocumentValues($includeNull = false)
    {
        return array_merge([123, 3.14, 'foo', true], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid integer values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidIntegerValues($includeNull = false)
    {
        return array_merge([3.14, 'foo', true, [], new stdClass()], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid ReadPreference values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidReadConcernValues($includeNull = false)
    {
        return array_merge([123, 3.14, 'foo', true, [], new stdClass(), new ReadPreference(ReadPreference::RP_PRIMARY), new WriteConcern(1)], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid ReadPreference values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidReadPreferenceValues($includeNull = false)
    {
        return array_merge([123, 3.14, 'foo', true, [], new stdClass(), new ReadConcern(), new WriteConcern(1)], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid Session values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidSessionValues($includeNull = false)
    {
        return array_merge([123, 3.14, 'foo', true, [], new stdClass(), new ReadConcern(), new ReadPreference(ReadPreference::RP_PRIMARY), new WriteConcern(1)], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid string values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidStringValues($includeNull = false)
    {
        return array_merge([123, 3.14, true, [], new stdClass()], $includeNull ? [null] : []);
    }

    /**
     * Return a list of invalid WriteConcern values.
     *
     * @param boolean $includeNull
     *
     * @return array
     */
    protected function getInvalidWriteConcernValues($includeNull = false)
    {
        return array_merge([123, 3.14, 'foo', true, [], new stdClass(), new ReadConcern(), new ReadPreference(ReadPreference::RP_PRIMARY)], $includeNull ? [null] : []);
    }

    /**
     * Return the test namespace.
     *
     * @return string
     */
    protected function getNamespace()
    {
         return sprintf('%s.%s', $this->getDatabaseName(), $this->getCollectionName());
    }

    /**
     * Wrap a list of values for use as a single-argument data provider.
     *
     * @param array $values List of values
     * @return array
     */
    protected function wrapValuesForDataProvider(array $values)
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
