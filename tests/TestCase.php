<?php

namespace MongoDB\Tests;

use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase as BaseTestCase;
use InvalidArgumentException;
use ReflectionClass;
use stdClass;
use Traversable;

abstract class TestCase extends BaseTestCase
{
    public function expectException($exception)
    {
        if (method_exists(BaseTestCase::class, 'expectException')) {
            parent::expectException($exception);
            return;
        }
        parent::setExpectedException($exception);
    }

   public function expectExceptionMessage($exceptionMessage)
    {
        if (method_exists(BaseTestCase::class, 'expectExceptionMessage')) {
            parent::expectExceptionMessage($exceptionMessage);
            return;
        }
        parent::setExpectedException($this->getExpectedException(), $exceptionMessage);
    }

    public function expectExceptionMessageRegExp($exceptionMessageRegExp)
    {
        if (method_exists(BaseTestCase::class, 'expectExceptionMessageRegExp')) {
            parent::expectExceptionMessageRegExp($exceptionMessageRegExp);
            return;
        }
        parent::setExpectedExceptionRegExp($this->getExpectedException(), $exceptionMessageRegExp);
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

        set_error_handler(function($errno, $errstr) use (&$errors) {
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
     * Asserts that a document has expected values for some fields.
     *
     * Only fields in the expected document will be checked. The actual document
     * may contain additional fields.
     *
     * @param array|object $expectedDocument
     * @param array|object $actualDocument
     */
    protected function assertMatchesDocument($expectedDocument, $actualDocument)
    {
        $normalizedExpectedDocument = $this->normalizeBSON($expectedDocument);
        $normalizedActualDocument = $this->normalizeBSON($actualDocument);

        $extraKeys = [];

        /* Avoid unsetting fields while we're iterating on the ArrayObject to
         * work around https://bugs.php.net/bug.php?id=70246 */
        foreach ($normalizedActualDocument as $key => $value) {
            if ( ! $normalizedExpectedDocument->offsetExists($key)) {
                $extraKeys[] = $key;
            }
        }

        foreach ($extraKeys as $key) {
            $normalizedActualDocument->offsetUnset($key);
        }

        $this->assertEquals(
            \MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($normalizedExpectedDocument)),
            \MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($normalizedActualDocument))
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
    protected function assertSameDocument($expectedDocument, $actualDocument)
    {
        $this->assertEquals(
            \MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($this->normalizeBSON($expectedDocument))),
            \MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($this->normalizeBSON($actualDocument)))
        );
    }

    protected function assertSameDocuments(array $expectedDocuments, $actualDocuments)
    {
        if ($actualDocuments instanceof Traversable) {
            $actualDocuments = iterator_to_array($actualDocuments);
        }

        if ( ! is_array($actualDocuments)) {
            throw new InvalidArgumentException('$actualDocuments is not an array or Traversable');
        }

        $normalizeRootDocuments = function($document) {
            return \MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($this->normalizeBSON($document)));
        };

        $this->assertEquals(
            array_map($normalizeRootDocuments, $expectedDocuments),
            array_map($normalizeRootDocuments, $actualDocuments)
        );
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
     * @return array
     */
    protected function getInvalidArrayValues()
    {
        return [123, 3.14, 'foo', true, new stdClass];
    }

    /**
     * Return a list of invalid boolean values.
     *
     * @return array
     */
    protected function getInvalidBooleanValues()
    {
        return [123, 3.14, 'foo', [], new stdClass];
    }

    /**
     * Return a list of invalid document values.
     *
     * @return array
     */
    protected function getInvalidDocumentValues()
    {
        return [123, 3.14, 'foo', true];
    }

    /**
     * Return a list of invalid integer values.
     *
     * @return array
     */
    protected function getInvalidIntegerValues()
    {
        return [3.14, 'foo', true, [], new stdClass];
    }

    /**
     * Return a list of invalid ReadPreference values.
     *
     * @return array
     */
    protected function getInvalidReadConcernValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass, new ReadPreference(ReadPreference::RP_PRIMARY), new WriteConcern(1)];
    }

    /**
     * Return a list of invalid ReadPreference values.
     *
     * @return array
     */
    protected function getInvalidReadPreferenceValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass, new ReadConcern, new WriteConcern(1)];
    }

    /**
     * Return a list of invalid Session values.
     *
     * @return array
     */
    protected function getInvalidSessionValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass, new ReadConcern, new ReadPreference(ReadPreference::RP_PRIMARY), new WriteConcern(1)];
    }

    /**
     * Return a list of invalid string values.
     *
     * @return array
     */
    protected function getInvalidStringValues()
    {
        return [123, 3.14, true, [], new stdClass];
    }

    /**
     * Return a list of invalid WriteConcern values.
     *
     * @return array
     */
    protected function getInvalidWriteConcernValues()
    {
        return [123, 3.14, 'foo', true, [], new stdClass, new ReadConcern, new ReadPreference(ReadPreference::RP_PRIMARY)];
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
     * Return the connection URI.
     *
     * @return string
     */
    protected function getUri()
    {
        return getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017';
    }

    /**
     * Wrap a list of values for use as a single-argument data provider.
     *
     * @param array $values List of values
     * @return array
     */
    protected function wrapValuesForDataProvider(array $values)
    {
        return array_map(function($value) { return [$value]; }, $values);
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
        if ( ! is_array($bson) && ! is_object($bson)) {
            throw new InvalidArgumentException('$bson is not an array or object');
        }

        if ($bson instanceof BSONArray || (is_array($bson) && $bson === array_values($bson))) {
            if ( ! $bson instanceof BSONArray) {
                $bson = new BSONArray($bson);
            }
        } else {
            if ( ! $bson instanceof BSONDocument) {
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
