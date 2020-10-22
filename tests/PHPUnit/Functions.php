<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use ArrayAccess;
use Countable;
use DOMDocument;
use DOMElement;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\ClassHasAttribute;
use PHPUnit\Framework\Constraint\ClassHasStaticAttribute;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\Count;
use PHPUnit\Framework\Constraint\DirectoryExists;
use PHPUnit\Framework\Constraint\FileExists;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsAnything;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsEqualCanonicalizing;
use PHPUnit\Framework\Constraint\IsEqualIgnoringCase;
use PHPUnit\Framework\Constraint\IsEqualWithDelta;
use PHPUnit\Framework\Constraint\IsFalse;
use PHPUnit\Framework\Constraint\IsFinite;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsInfinite;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsJson;
use PHPUnit\Framework\Constraint\IsNan;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsReadable;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\IsWritable;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\LogicalXor;
use PHPUnit\Framework\Constraint\ObjectHasAttribute;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Constraint\TraversableContainsEqual;
use PHPUnit\Framework\Constraint\TraversableContainsIdentical;
use PHPUnit\Framework\Constraint\TraversableContainsOnly;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount as AnyInvokedCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtIndex as InvokedAtIndexMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastCount as InvokedAtLeastCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce as InvokedAtLeastOnceMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtMostCount as InvokedAtMostCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls as ConsecutiveCallsStub;
use PHPUnit\Framework\MockObject\Stub\Exception as ExceptionStub;
use PHPUnit\Framework\MockObject\Stub\ReturnArgument as ReturnArgumentStub;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback as ReturnCallbackStub;
use PHPUnit\Framework\MockObject\Stub\ReturnSelf as ReturnSelfStub;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use PHPUnit\Framework\MockObject\Stub\ReturnValueMap as ReturnValueMapStub;
use PHPUnit\Util\Exception;
use PHPUnit\Util\Xml\Exception as XmlException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Throwable;
use function func_get_args;
use function function_exists;

if (! function_exists('PHPUnit\Framework\assertArrayHasKey')) {
    /**
     * Asserts that an array has a specified key.
     *
     * @see Assert::assertArrayHasKey
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertArrayHasKey($key, $array, string $message = '')
    {
        Assert::assertArrayHasKey(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertArrayNotHasKey')) {
    /**
     * Asserts that an array does not have a specified key.
     *
     * @see Assert::assertArrayNotHasKey
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertArrayNotHasKey($key, $array, string $message = '')
    {
        Assert::assertArrayNotHasKey(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertContains')) {
    /**
     * Asserts that a haystack contains a needle.
     *
     * @see Assert::assertContains
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertContains($needle, $haystack, string $message = '')
    {
        Assert::assertContains(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertContainsEquals')) {
    function assertContainsEquals($needle, $haystack, string $message = '')
    {
        Assert::assertContainsEquals(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotContains')) {
    /**
     * Asserts that a haystack does not contain a needle.
     *
     * @see Assert::assertNotContains
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertNotContains($needle, $haystack, string $message = '')
    {
        Assert::assertNotContains(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotContainsEquals')) {
    function assertNotContainsEquals($needle, $haystack, string $message = '')
    {
        Assert::assertNotContainsEquals(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertContainsOnly')) {
    /**
     * Asserts that a haystack contains only values of a given type.
     *
     * @see Assert::assertContainsOnly
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertContainsOnly(string $type, $haystack, bool $isNativeType = null, string $message = '')
    {
        Assert::assertContainsOnly(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertContainsOnlyInstancesOf')) {
    /**
     * Asserts that a haystack contains only instances of a given class name.
     *
     * @see Assert::assertContainsOnlyInstancesOf
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertContainsOnlyInstancesOf(string $className, $haystack, string $message = '')
    {
        Assert::assertContainsOnlyInstancesOf(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotContainsOnly')) {
    /**
     * Asserts that a haystack does not contain only values of a given type.
     *
     * @see Assert::assertNotContainsOnly
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertNotContainsOnly(string $type, $haystack, bool $isNativeType = null, string $message = '')
    {
        Assert::assertNotContainsOnly(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertCount')) {
    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @see Assert::assertCount
     *
     * @param Countable|iterable $haystack
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertCount(int $expectedCount, $haystack, string $message = '')
    {
        Assert::assertCount(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotCount')) {
    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @see Assert::assertNotCount
     *
     * @param Countable|iterable $haystack
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertNotCount(int $expectedCount, $haystack, string $message = '')
    {
        Assert::assertNotCount(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertEquals')) {
    /**
     * Asserts that two variables are equal.
     *
     * @see Assert::assertEquals
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertEquals($expected, $actual, string $message = '')
    {
        Assert::assertEquals(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertEqualsCanonicalizing')) {
    /**
     * Asserts that two variables are equal (canonicalizing).
     *
     * @see Assert::assertEqualsCanonicalizing
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertEqualsCanonicalizing($expected, $actual, string $message = '')
    {
        Assert::assertEqualsCanonicalizing(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertEqualsIgnoringCase')) {
    /**
     * Asserts that two variables are equal (ignoring case).
     *
     * @see Assert::assertEqualsIgnoringCase
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertEqualsIgnoringCase($expected, $actual, string $message = '')
    {
        Assert::assertEqualsIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertEqualsWithDelta')) {
    /**
     * Asserts that two variables are equal (with delta).
     *
     * @see Assert::assertEqualsWithDelta
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertEqualsWithDelta($expected, $actual, float $delta, string $message = '')
    {
        Assert::assertEqualsWithDelta(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotEquals')) {
    /**
     * Asserts that two variables are not equal.
     *
     * @see Assert::assertNotEquals
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertNotEquals($expected, $actual, string $message = '')
    {
        Assert::assertNotEquals(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotEqualsCanonicalizing')) {
    /**
     * Asserts that two variables are not equal (canonicalizing).
     *
     * @see Assert::assertNotEqualsCanonicalizing
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertNotEqualsCanonicalizing($expected, $actual, string $message = '')
    {
        Assert::assertNotEqualsCanonicalizing(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotEqualsIgnoringCase')) {
    /**
     * Asserts that two variables are not equal (ignoring case).
     *
     * @see Assert::assertNotEqualsIgnoringCase
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertNotEqualsIgnoringCase($expected, $actual, string $message = '')
    {
        Assert::assertNotEqualsIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotEqualsWithDelta')) {
    /**
     * Asserts that two variables are not equal (with delta).
     *
     * @see Assert::assertNotEqualsWithDelta
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertNotEqualsWithDelta($expected, $actual, float $delta, string $message = '')
    {
        Assert::assertNotEqualsWithDelta(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertEmpty')) {
    /**
     * Asserts that a variable is empty.
     *
     * @see Assert::assertEmpty
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert empty $actual
     */
    function assertEmpty($actual, string $message = '')
    {
        Assert::assertEmpty(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotEmpty')) {
    /**
     * Asserts that a variable is not empty.
     *
     * @see Assert::assertNotEmpty
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !empty $actual
     */
    function assertNotEmpty($actual, string $message = '')
    {
        Assert::assertNotEmpty(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertGreaterThan')) {
    /**
     * Asserts that a value is greater than another value.
     *
     * @see Assert::assertGreaterThan
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertGreaterThan($expected, $actual, string $message = '')
    {
        Assert::assertGreaterThan(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertGreaterThanOrEqual')) {
    /**
     * Asserts that a value is greater than or equal to another value.
     *
     * @see Assert::assertGreaterThanOrEqual
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertGreaterThanOrEqual($expected, $actual, string $message = '')
    {
        Assert::assertGreaterThanOrEqual(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertLessThan')) {
    /**
     * Asserts that a value is smaller than another value.
     *
     * @see Assert::assertLessThan
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertLessThan($expected, $actual, string $message = '')
    {
        Assert::assertLessThan(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertLessThanOrEqual')) {
    /**
     * Asserts that a value is smaller than or equal to another value.
     *
     * @see Assert::assertLessThanOrEqual
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertLessThanOrEqual($expected, $actual, string $message = '')
    {
        Assert::assertLessThanOrEqual(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileEquals')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @see Assert::assertFileEquals
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileEquals(string $expected, string $actual, string $message = '')
    {
        Assert::assertFileEquals(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileEqualsCanonicalizing')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file (canonicalizing).
     *
     * @see Assert::assertFileEqualsCanonicalizing
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileEqualsCanonicalizing(string $expected, string $actual, string $message = '')
    {
        Assert::assertFileEqualsCanonicalizing(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileEqualsIgnoringCase')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file (ignoring case).
     *
     * @see Assert::assertFileEqualsIgnoringCase
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileEqualsIgnoringCase(string $expected, string $actual, string $message = '')
    {
        Assert::assertFileEqualsIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileNotEquals')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of
     * another file.
     *
     * @see Assert::assertFileNotEquals
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileNotEquals(string $expected, string $actual, string $message = '')
    {
        Assert::assertFileNotEquals(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileNotEqualsCanonicalizing')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of another
     * file (canonicalizing).
     *
     * @see Assert::assertFileNotEqualsCanonicalizing
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileNotEqualsCanonicalizing(string $expected, string $actual, string $message = '')
    {
        Assert::assertFileNotEqualsCanonicalizing(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileNotEqualsIgnoringCase')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of another
     * file (ignoring case).
     *
     * @see Assert::assertFileNotEqualsIgnoringCase
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileNotEqualsIgnoringCase(string $expected, string $actual, string $message = '')
    {
        Assert::assertFileNotEqualsIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringEqualsFile')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file.
     *
     * @see Assert::assertStringEqualsFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringEqualsFile(string $expectedFile, string $actualString, string $message = '')
    {
        Assert::assertStringEqualsFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringEqualsFileCanonicalizing')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file (canonicalizing).
     *
     * @see Assert::assertStringEqualsFileCanonicalizing
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringEqualsFileCanonicalizing(string $expectedFile, string $actualString, string $message = '')
    {
        Assert::assertStringEqualsFileCanonicalizing(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringEqualsFileIgnoringCase')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file (ignoring case).
     *
     * @see Assert::assertStringEqualsFileIgnoringCase
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringEqualsFileIgnoringCase(string $expectedFile, string $actualString, string $message = '')
    {
        Assert::assertStringEqualsFileIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringNotEqualsFile')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file.
     *
     * @see Assert::assertStringNotEqualsFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringNotEqualsFile(string $expectedFile, string $actualString, string $message = '')
    {
        Assert::assertStringNotEqualsFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringNotEqualsFileCanonicalizing')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file (canonicalizing).
     *
     * @see Assert::assertStringNotEqualsFileCanonicalizing
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringNotEqualsFileCanonicalizing(string $expectedFile, string $actualString, string $message = '')
    {
        Assert::assertStringNotEqualsFileCanonicalizing(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringNotEqualsFileIgnoringCase')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file (ignoring case).
     *
     * @see Assert::assertStringNotEqualsFileIgnoringCase
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringNotEqualsFileIgnoringCase(string $expectedFile, string $actualString, string $message = '')
    {
        Assert::assertStringNotEqualsFileIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsReadable')) {
    /**
     * Asserts that a file/dir is readable.
     *
     * @see Assert::assertIsReadable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertIsReadable(string $filename, string $message = '')
    {
        Assert::assertIsReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotReadable')) {
    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @see Assert::assertIsNotReadable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertIsNotReadable(string $filename, string $message = '')
    {
        Assert::assertIsNotReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotIsReadable')) {
    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4062
     * @see Assert::assertNotIsReadable
     */
    function assertNotIsReadable(string $filename, string $message = '')
    {
        Assert::assertNotIsReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsWritable')) {
    /**
     * Asserts that a file/dir exists and is writable.
     *
     * @see Assert::assertIsWritable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertIsWritable(string $filename, string $message = '')
    {
        Assert::assertIsWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotWritable')) {
    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @see Assert::assertIsNotWritable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertIsNotWritable(string $filename, string $message = '')
    {
        Assert::assertIsNotWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotIsWritable')) {
    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4065
     * @see Assert::assertNotIsWritable
     */
    function assertNotIsWritable(string $filename, string $message = '')
    {
        Assert::assertNotIsWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryExists')) {
    /**
     * Asserts that a directory exists.
     *
     * @see Assert::assertDirectoryExists
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertDirectoryExists(string $directory, string $message = '')
    {
        Assert::assertDirectoryExists(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryDoesNotExist')) {
    /**
     * Asserts that a directory does not exist.
     *
     * @see Assert::assertDirectoryDoesNotExist
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertDirectoryDoesNotExist(string $directory, string $message = '')
    {
        Assert::assertDirectoryDoesNotExist(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryNotExists')) {
    /**
     * Asserts that a directory does not exist.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4068
     * @see Assert::assertDirectoryNotExists
     */
    function assertDirectoryNotExists(string $directory, string $message = '')
    {
        Assert::assertDirectoryNotExists(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryIsReadable')) {
    /**
     * Asserts that a directory exists and is readable.
     *
     * @see Assert::assertDirectoryIsReadable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertDirectoryIsReadable(string $directory, string $message = '')
    {
        Assert::assertDirectoryIsReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryIsNotReadable')) {
    /**
     * Asserts that a directory exists and is not readable.
     *
     * @see Assert::assertDirectoryIsNotReadable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertDirectoryIsNotReadable(string $directory, string $message = '')
    {
        Assert::assertDirectoryIsNotReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryNotIsReadable')) {
    /**
     * Asserts that a directory exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4071
     * @see Assert::assertDirectoryNotIsReadable
     */
    function assertDirectoryNotIsReadable(string $directory, string $message = '')
    {
        Assert::assertDirectoryNotIsReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryIsWritable')) {
    /**
     * Asserts that a directory exists and is writable.
     *
     * @see Assert::assertDirectoryIsWritable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertDirectoryIsWritable(string $directory, string $message = '')
    {
        Assert::assertDirectoryIsWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryIsNotWritable')) {
    /**
     * Asserts that a directory exists and is not writable.
     *
     * @see Assert::assertDirectoryIsNotWritable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertDirectoryIsNotWritable(string $directory, string $message = '')
    {
        Assert::assertDirectoryIsNotWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDirectoryNotIsWritable')) {
    /**
     * Asserts that a directory exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4074
     * @see Assert::assertDirectoryNotIsWritable
     */
    function assertDirectoryNotIsWritable(string $directory, string $message = '')
    {
        Assert::assertDirectoryNotIsWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileExists')) {
    /**
     * Asserts that a file exists.
     *
     * @see Assert::assertFileExists
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileExists(string $filename, string $message = '')
    {
        Assert::assertFileExists(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileDoesNotExist')) {
    /**
     * Asserts that a file does not exist.
     *
     * @see Assert::assertFileDoesNotExist
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileDoesNotExist(string $filename, string $message = '')
    {
        Assert::assertFileDoesNotExist(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileNotExists')) {
    /**
     * Asserts that a file does not exist.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4077
     * @see Assert::assertFileNotExists
     */
    function assertFileNotExists(string $filename, string $message = '')
    {
        Assert::assertFileNotExists(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileIsReadable')) {
    /**
     * Asserts that a file exists and is readable.
     *
     * @see Assert::assertFileIsReadable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileIsReadable(string $file, string $message = '')
    {
        Assert::assertFileIsReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileIsNotReadable')) {
    /**
     * Asserts that a file exists and is not readable.
     *
     * @see Assert::assertFileIsNotReadable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileIsNotReadable(string $file, string $message = '')
    {
        Assert::assertFileIsNotReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileNotIsReadable')) {
    /**
     * Asserts that a file exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4080
     * @see Assert::assertFileNotIsReadable
     */
    function assertFileNotIsReadable(string $file, string $message = '')
    {
        Assert::assertFileNotIsReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileIsWritable')) {
    /**
     * Asserts that a file exists and is writable.
     *
     * @see Assert::assertFileIsWritable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileIsWritable(string $file, string $message = '')
    {
        Assert::assertFileIsWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileIsNotWritable')) {
    /**
     * Asserts that a file exists and is not writable.
     *
     * @see Assert::assertFileIsNotWritable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFileIsNotWritable(string $file, string $message = '')
    {
        Assert::assertFileIsNotWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFileNotIsWritable')) {
    /**
     * Asserts that a file exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4083
     * @see Assert::assertFileNotIsWritable
     */
    function assertFileNotIsWritable(string $file, string $message = '')
    {
        Assert::assertFileNotIsWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertTrue')) {
    /**
     * Asserts that a condition is true.
     *
     * @see Assert::assertTrue
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert true $condition
     */
    function assertTrue($condition, string $message = '')
    {
        Assert::assertTrue(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotTrue')) {
    /**
     * Asserts that a condition is not true.
     *
     * @see Assert::assertNotTrue
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !true $condition
     */
    function assertNotTrue($condition, string $message = '')
    {
        Assert::assertNotTrue(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFalse')) {
    /**
     * Asserts that a condition is false.
     *
     * @see Assert::assertFalse
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert false $condition
     */
    function assertFalse($condition, string $message = '')
    {
        Assert::assertFalse(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotFalse')) {
    /**
     * Asserts that a condition is not false.
     *
     * @see Assert::assertNotFalse
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !false $condition
     */
    function assertNotFalse($condition, string $message = '')
    {
        Assert::assertNotFalse(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNull')) {
    /**
     * Asserts that a variable is null.
     *
     * @see Assert::assertNull
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert null $actual
     */
    function assertNull($actual, string $message = '')
    {
        Assert::assertNull(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotNull')) {
    /**
     * Asserts that a variable is not null.
     *
     * @see Assert::assertNotNull
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !null $actual
     */
    function assertNotNull($actual, string $message = '')
    {
        Assert::assertNotNull(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertFinite')) {
    /**
     * Asserts that a variable is finite.
     *
     * @see Assert::assertFinite
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertFinite($actual, string $message = '')
    {
        Assert::assertFinite(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertInfinite')) {
    /**
     * Asserts that a variable is infinite.
     *
     * @see Assert::assertInfinite
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertInfinite($actual, string $message = '')
    {
        Assert::assertInfinite(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNan')) {
    /**
     * Asserts that a variable is nan.
     *
     * @see Assert::assertNan
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertNan($actual, string $message = '')
    {
        Assert::assertNan(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertClassHasAttribute')) {
    /**
     * Asserts that a class has a specified attribute.
     *
     * @see Assert::assertClassHasAttribute
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertClassHasAttribute(string $attributeName, string $className, string $message = '')
    {
        Assert::assertClassHasAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertClassNotHasAttribute')) {
    /**
     * Asserts that a class does not have a specified attribute.
     *
     * @see Assert::assertClassNotHasAttribute
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertClassNotHasAttribute(string $attributeName, string $className, string $message = '')
    {
        Assert::assertClassNotHasAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertClassHasStaticAttribute')) {
    /**
     * Asserts that a class has a specified static attribute.
     *
     * @see Assert::assertClassHasStaticAttribute
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertClassHasStaticAttribute(string $attributeName, string $className, string $message = '')
    {
        Assert::assertClassHasStaticAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertClassNotHasStaticAttribute')) {
    /**
     * Asserts that a class does not have a specified static attribute.
     *
     * @see Assert::assertClassNotHasStaticAttribute
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertClassNotHasStaticAttribute(string $attributeName, string $className, string $message = '')
    {
        Assert::assertClassNotHasStaticAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertObjectHasAttribute')) {
    /**
     * Asserts that an object has a specified attribute.
     *
     * @see Assert::assertObjectHasAttribute
     *
     * @param object $object
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertObjectHasAttribute(string $attributeName, $object, string $message = '')
    {
        Assert::assertObjectHasAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertObjectNotHasAttribute')) {
    /**
     * Asserts that an object does not have a specified attribute.
     *
     * @see Assert::assertObjectNotHasAttribute
     *
     * @param object $object
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertObjectNotHasAttribute(string $attributeName, $object, string $message = '')
    {
        Assert::assertObjectNotHasAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertSame')) {
    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @see Assert::assertSame
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-template ExpectedType
     * @psalm-param ExpectedType $expected
     * @psalm-assert =ExpectedType $actual
     */
    function assertSame($expected, $actual, string $message = '')
    {
        Assert::assertSame(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotSame')) {
    /**
     * Asserts that two variables do not have the same type and value.
     * Used on objects, it asserts that two variables do not reference
     * the same object.
     *
     * @see Assert::assertNotSame
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertNotSame($expected, $actual, string $message = '')
    {
        Assert::assertNotSame(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertInstanceOf')) {
    /**
     * Asserts that a variable is of a given type.
     *
     * @see Assert::assertInstanceOf
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     *
     * @psalm-template ExpectedType of object
     * @psalm-param class-string<ExpectedType> $expected
     * @psalm-assert ExpectedType $actual
     */
    function assertInstanceOf(string $expected, $actual, string $message = '')
    {
        Assert::assertInstanceOf(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotInstanceOf')) {
    /**
     * Asserts that a variable is not of a given type.
     *
     * @see Assert::assertNotInstanceOf
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     *
     * @psalm-template ExpectedType of object
     * @psalm-param class-string<ExpectedType> $expected
     * @psalm-assert !ExpectedType $actual
     */
    function assertNotInstanceOf(string $expected, $actual, string $message = '')
    {
        Assert::assertNotInstanceOf(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsArray')) {
    /**
     * Asserts that a variable is of type array.
     *
     * @see Assert::assertIsArray
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert array $actual
     */
    function assertIsArray($actual, string $message = '')
    {
        Assert::assertIsArray(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsBool')) {
    /**
     * Asserts that a variable is of type bool.
     *
     * @see Assert::assertIsBool
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert bool $actual
     */
    function assertIsBool($actual, string $message = '')
    {
        Assert::assertIsBool(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsFloat')) {
    /**
     * Asserts that a variable is of type float.
     *
     * @see Assert::assertIsFloat
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert float $actual
     */
    function assertIsFloat($actual, string $message = '')
    {
        Assert::assertIsFloat(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsInt')) {
    /**
     * Asserts that a variable is of type int.
     *
     * @see Assert::assertIsInt
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert int $actual
     */
    function assertIsInt($actual, string $message = '')
    {
        Assert::assertIsInt(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNumeric')) {
    /**
     * Asserts that a variable is of type numeric.
     *
     * @see Assert::assertIsNumeric
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert numeric $actual
     */
    function assertIsNumeric($actual, string $message = '')
    {
        Assert::assertIsNumeric(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsObject')) {
    /**
     * Asserts that a variable is of type object.
     *
     * @see Assert::assertIsObject
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert object $actual
     */
    function assertIsObject($actual, string $message = '')
    {
        Assert::assertIsObject(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsResource')) {
    /**
     * Asserts that a variable is of type resource.
     *
     * @see Assert::assertIsResource
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert resource $actual
     */
    function assertIsResource($actual, string $message = '')
    {
        Assert::assertIsResource(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsClosedResource')) {
    /**
     * Asserts that a variable is of type resource and is closed.
     *
     * @see Assert::assertIsClosedResource
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert resource $actual
     */
    function assertIsClosedResource($actual, string $message = '')
    {
        Assert::assertIsClosedResource(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsString')) {
    /**
     * Asserts that a variable is of type string.
     *
     * @see Assert::assertIsString
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert string $actual
     */
    function assertIsString($actual, string $message = '')
    {
        Assert::assertIsString(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsScalar')) {
    /**
     * Asserts that a variable is of type scalar.
     *
     * @see Assert::assertIsScalar
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert scalar $actual
     */
    function assertIsScalar($actual, string $message = '')
    {
        Assert::assertIsScalar(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsCallable')) {
    /**
     * Asserts that a variable is of type callable.
     *
     * @see Assert::assertIsCallable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert callable $actual
     */
    function assertIsCallable($actual, string $message = '')
    {
        Assert::assertIsCallable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsIterable')) {
    /**
     * Asserts that a variable is of type iterable.
     *
     * @see Assert::assertIsIterable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert iterable $actual
     */
    function assertIsIterable($actual, string $message = '')
    {
        Assert::assertIsIterable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotArray')) {
    /**
     * Asserts that a variable is not of type array.
     *
     * @see Assert::assertIsNotArray
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !array $actual
     */
    function assertIsNotArray($actual, string $message = '')
    {
        Assert::assertIsNotArray(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotBool')) {
    /**
     * Asserts that a variable is not of type bool.
     *
     * @see Assert::assertIsNotBool
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !bool $actual
     */
    function assertIsNotBool($actual, string $message = '')
    {
        Assert::assertIsNotBool(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotFloat')) {
    /**
     * Asserts that a variable is not of type float.
     *
     * @see Assert::assertIsNotFloat
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !float $actual
     */
    function assertIsNotFloat($actual, string $message = '')
    {
        Assert::assertIsNotFloat(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotInt')) {
    /**
     * Asserts that a variable is not of type int.
     *
     * @see Assert::assertIsNotInt
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !int $actual
     */
    function assertIsNotInt($actual, string $message = '')
    {
        Assert::assertIsNotInt(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotNumeric')) {
    /**
     * Asserts that a variable is not of type numeric.
     *
     * @see Assert::assertIsNotNumeric
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !numeric $actual
     */
    function assertIsNotNumeric($actual, string $message = '')
    {
        Assert::assertIsNotNumeric(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotObject')) {
    /**
     * Asserts that a variable is not of type object.
     *
     * @see Assert::assertIsNotObject
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !object $actual
     */
    function assertIsNotObject($actual, string $message = '')
    {
        Assert::assertIsNotObject(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotResource')) {
    /**
     * Asserts that a variable is not of type resource.
     *
     * @see Assert::assertIsNotResource
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !resource $actual
     */
    function assertIsNotResource($actual, string $message = '')
    {
        Assert::assertIsNotResource(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotClosedResource')) {
    /**
     * Asserts that a variable is not of type resource.
     *
     * @see Assert::assertIsNotClosedResource
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !resource $actual
     */
    function assertIsNotClosedResource($actual, string $message = '')
    {
        Assert::assertIsNotClosedResource(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotString')) {
    /**
     * Asserts that a variable is not of type string.
     *
     * @see Assert::assertIsNotString
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !string $actual
     */
    function assertIsNotString($actual, string $message = '')
    {
        Assert::assertIsNotString(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotScalar')) {
    /**
     * Asserts that a variable is not of type scalar.
     *
     * @see Assert::assertIsNotScalar
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !scalar $actual
     */
    function assertIsNotScalar($actual, string $message = '')
    {
        Assert::assertIsNotScalar(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotCallable')) {
    /**
     * Asserts that a variable is not of type callable.
     *
     * @see Assert::assertIsNotCallable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !callable $actual
     */
    function assertIsNotCallable($actual, string $message = '')
    {
        Assert::assertIsNotCallable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertIsNotIterable')) {
    /**
     * Asserts that a variable is not of type iterable.
     *
     * @see Assert::assertIsNotIterable
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @psalm-assert !iterable $actual
     */
    function assertIsNotIterable($actual, string $message = '')
    {
        Assert::assertIsNotIterable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertMatchesRegularExpression')) {
    /**
     * Asserts that a string matches a given regular expression.
     *
     * @see Assert::assertMatchesRegularExpression
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertMatchesRegularExpression(string $pattern, string $string, string $message = '')
    {
        Assert::assertMatchesRegularExpression(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertRegExp')) {
    /**
     * Asserts that a string matches a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4086
     * @see Assert::assertRegExp
     */
    function assertRegExp(string $pattern, string $string, string $message = '')
    {
        Assert::assertRegExp(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertDoesNotMatchRegularExpression')) {
    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @see Assert::assertDoesNotMatchRegularExpression
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertDoesNotMatchRegularExpression(string $pattern, string $string, string $message = '')
    {
        Assert::assertDoesNotMatchRegularExpression(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotRegExp')) {
    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4089
     * @see Assert::assertNotRegExp
     */
    function assertNotRegExp(string $pattern, string $string, string $message = '')
    {
        Assert::assertNotRegExp(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertSameSize')) {
    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is the same.
     *
     * @see Assert::assertSameSize
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertSameSize($expected, $actual, string $message = '')
    {
        Assert::assertSameSize(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertNotSameSize')) {
    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is not the same.
     *
     * @see Assert::assertNotSameSize
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertNotSameSize($expected, $actual, string $message = '')
    {
        Assert::assertNotSameSize(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringMatchesFormat')) {
    /**
     * Asserts that a string matches a given format string.
     *
     * @see Assert::assertStringMatchesFormat
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringMatchesFormat(string $format, string $string, string $message = '')
    {
        Assert::assertStringMatchesFormat(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringNotMatchesFormat')) {
    /**
     * Asserts that a string does not match a given format string.
     *
     * @see Assert::assertStringNotMatchesFormat
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringNotMatchesFormat(string $format, string $string, string $message = '')
    {
        Assert::assertStringNotMatchesFormat(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringMatchesFormatFile')) {
    /**
     * Asserts that a string matches a given format file.
     *
     * @see Assert::assertStringMatchesFormatFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringMatchesFormatFile(string $formatFile, string $string, string $message = '')
    {
        Assert::assertStringMatchesFormatFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringNotMatchesFormatFile')) {
    /**
     * Asserts that a string does not match a given format string.
     *
     * @see Assert::assertStringNotMatchesFormatFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringNotMatchesFormatFile(string $formatFile, string $string, string $message = '')
    {
        Assert::assertStringNotMatchesFormatFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringStartsWith')) {
    /**
     * Asserts that a string starts with a given prefix.
     *
     * @see Assert::assertStringStartsWith
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringStartsWith(string $prefix, string $string, string $message = '')
    {
        Assert::assertStringStartsWith(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringStartsNotWith')) {
    /**
     * Asserts that a string starts not with a given prefix.
     *
     * @see Assert::assertStringStartsNotWith
     *
     * @param string $prefix
     * @param string $string
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringStartsNotWith($prefix, $string, string $message = '')
    {
        Assert::assertStringStartsNotWith(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringContainsString')) {
    /**
     * @see Assert::assertStringContainsString
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringContainsString(string $needle, string $haystack, string $message = '')
    {
        Assert::assertStringContainsString(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringContainsStringIgnoringCase')) {
    /**
     * @see Assert::assertStringContainsStringIgnoringCase
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringContainsStringIgnoringCase(string $needle, string $haystack, string $message = '')
    {
        Assert::assertStringContainsStringIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringNotContainsString')) {
    /**
     * @see Assert::assertStringNotContainsString
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringNotContainsString(string $needle, string $haystack, string $message = '')
    {
        Assert::assertStringNotContainsString(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringNotContainsStringIgnoringCase')) {
    /**
     * @see Assert::assertStringNotContainsStringIgnoringCase
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringNotContainsStringIgnoringCase(string $needle, string $haystack, string $message = '')
    {
        Assert::assertStringNotContainsStringIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringEndsWith')) {
    /**
     * Asserts that a string ends with a given suffix.
     *
     * @see Assert::assertStringEndsWith
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringEndsWith(string $suffix, string $string, string $message = '')
    {
        Assert::assertStringEndsWith(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertStringEndsNotWith')) {
    /**
     * Asserts that a string ends not with a given suffix.
     *
     * @see Assert::assertStringEndsNotWith
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertStringEndsNotWith(string $suffix, string $string, string $message = '')
    {
        Assert::assertStringEndsNotWith(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertXmlFileEqualsXmlFile')) {
    /**
     * Asserts that two XML files are equal.
     *
     * @see Assert::assertXmlFileEqualsXmlFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertXmlFileEqualsXmlFile(string $expectedFile, string $actualFile, string $message = '')
    {
        Assert::assertXmlFileEqualsXmlFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertXmlFileNotEqualsXmlFile')) {
    /**
     * Asserts that two XML files are not equal.
     *
     * @see Assert::assertXmlFileNotEqualsXmlFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    function assertXmlFileNotEqualsXmlFile(string $expectedFile, string $actualFile, string $message = '')
    {
        Assert::assertXmlFileNotEqualsXmlFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertXmlStringEqualsXmlFile')) {
    /**
     * Asserts that two XML documents are equal.
     *
     * @see Assert::assertXmlStringEqualsXmlFile
     *
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws XmlException
     */
    function assertXmlStringEqualsXmlFile(string $expectedFile, $actualXml, string $message = '')
    {
        Assert::assertXmlStringEqualsXmlFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertXmlStringNotEqualsXmlFile')) {
    /**
     * Asserts that two XML documents are not equal.
     *
     * @see Assert::assertXmlStringNotEqualsXmlFile
     *
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws XmlException
     */
    function assertXmlStringNotEqualsXmlFile(string $expectedFile, $actualXml, string $message = '')
    {
        Assert::assertXmlStringNotEqualsXmlFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertXmlStringEqualsXmlString')) {
    /**
     * Asserts that two XML documents are equal.
     *
     * @see Assert::assertXmlStringEqualsXmlString
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws XmlException
     */
    function assertXmlStringEqualsXmlString($expectedXml, $actualXml, string $message = '')
    {
        Assert::assertXmlStringEqualsXmlString(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertXmlStringNotEqualsXmlString')) {
    /**
     * Asserts that two XML documents are not equal.
     *
     * @see Assert::assertXmlStringNotEqualsXmlString
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws XmlException
     */
    function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, string $message = '')
    {
        Assert::assertXmlStringNotEqualsXmlString(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertEqualXMLStructure')) {
    /**
     * Asserts that a hierarchy of DOMElements matches.
     *
     * @throws AssertionFailedError
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4091
     * @see Assert::assertEqualXMLStructure
     */
    function assertEqualXMLStructure(DOMElement $expectedElement, DOMElement $actualElement, bool $checkAttributes = false, string $message = '')
    {
        Assert::assertEqualXMLStructure(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertThat')) {
    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @see Assert::assertThat
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertThat($value, Constraint $constraint, string $message = '')
    {
        Assert::assertThat(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertJson')) {
    /**
     * Asserts that a string is a valid JSON string.
     *
     * @see Assert::assertJson
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertJson(string $actualJson, string $message = '')
    {
        Assert::assertJson(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertJsonStringEqualsJsonString')) {
    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @see Assert::assertJsonStringEqualsJsonString
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertJsonStringEqualsJsonString(string $expectedJson, string $actualJson, string $message = '')
    {
        Assert::assertJsonStringEqualsJsonString(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertJsonStringNotEqualsJsonString')) {
    /**
     * Asserts that two given JSON encoded objects or arrays are not equal.
     *
     * @see Assert::assertJsonStringNotEqualsJsonString
     *
     * @param string $expectedJson
     * @param string $actualJson
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, string $message = '')
    {
        Assert::assertJsonStringNotEqualsJsonString(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertJsonStringEqualsJsonFile')) {
    /**
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     *
     * @see Assert::assertJsonStringEqualsJsonFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertJsonStringEqualsJsonFile(string $expectedFile, string $actualJson, string $message = '')
    {
        Assert::assertJsonStringEqualsJsonFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertJsonStringNotEqualsJsonFile')) {
    /**
     * Asserts that the generated JSON encoded object and the content of the given file are not equal.
     *
     * @see Assert::assertJsonStringNotEqualsJsonFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertJsonStringNotEqualsJsonFile(string $expectedFile, string $actualJson, string $message = '')
    {
        Assert::assertJsonStringNotEqualsJsonFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertJsonFileEqualsJsonFile')) {
    /**
     * Asserts that two JSON files are equal.
     *
     * @see Assert::assertJsonFileEqualsJsonFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertJsonFileEqualsJsonFile(string $expectedFile, string $actualFile, string $message = '')
    {
        Assert::assertJsonFileEqualsJsonFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\assertJsonFileNotEqualsJsonFile')) {
    /**
     * Asserts that two JSON files are not equal.
     *
     * @see Assert::assertJsonFileNotEqualsJsonFile
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    function assertJsonFileNotEqualsJsonFile(string $expectedFile, string $actualFile, string $message = '')
    {
        Assert::assertJsonFileNotEqualsJsonFile(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\logicalAnd')) {
    function logicalAnd() : LogicalAnd
    {
        return Assert::logicalAnd(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\logicalOr')) {
    function logicalOr() : LogicalOr
    {
        return Assert::logicalOr(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\logicalNot')) {
    function logicalNot(Constraint $constraint) : LogicalNot
    {
        return Assert::logicalNot(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\logicalXor')) {
    function logicalXor() : LogicalXor
    {
        return Assert::logicalXor(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\anything')) {
    function anything() : IsAnything
    {
        return Assert::anything(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isTrue')) {
    function isTrue() : IsTrue
    {
        return Assert::isTrue(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\callback')) {
    function callback(callable $callback) : Callback
    {
        return Assert::callback(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isFalse')) {
    function isFalse() : IsFalse
    {
        return Assert::isFalse(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isJson')) {
    function isJson() : IsJson
    {
        return Assert::isJson(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isNull')) {
    function isNull() : IsNull
    {
        return Assert::isNull(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isFinite')) {
    function isFinite() : IsFinite
    {
        return Assert::isFinite(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isInfinite')) {
    function isInfinite() : IsInfinite
    {
        return Assert::isInfinite(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isNan')) {
    function isNan() : IsNan
    {
        return Assert::isNan(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\containsEqual')) {
    function containsEqual($value) : TraversableContainsEqual
    {
        return Assert::containsEqual(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\containsIdentical')) {
    function containsIdentical($value) : TraversableContainsIdentical
    {
        return Assert::containsIdentical(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\containsOnly')) {
    function containsOnly(string $type) : TraversableContainsOnly
    {
        return Assert::containsOnly(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\containsOnlyInstancesOf')) {
    function containsOnlyInstancesOf(string $className) : TraversableContainsOnly
    {
        return Assert::containsOnlyInstancesOf(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\arrayHasKey')) {
    function arrayHasKey($key) : ArrayHasKey
    {
        return Assert::arrayHasKey(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\equalTo')) {
    function equalTo($value) : IsEqual
    {
        return Assert::equalTo(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\equalToCanonicalizing')) {
    function equalToCanonicalizing($value) : IsEqualCanonicalizing
    {
        return Assert::equalToCanonicalizing(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\equalToIgnoringCase')) {
    function equalToIgnoringCase($value) : IsEqualIgnoringCase
    {
        return Assert::equalToIgnoringCase(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\equalToWithDelta')) {
    function equalToWithDelta($value, float $delta) : IsEqualWithDelta
    {
        return Assert::equalToWithDelta(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isEmpty')) {
    function isEmpty() : IsEmpty
    {
        return Assert::isEmpty(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isWritable')) {
    function isWritable() : IsWritable
    {
        return Assert::isWritable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isReadable')) {
    function isReadable() : IsReadable
    {
        return Assert::isReadable(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\directoryExists')) {
    function directoryExists() : DirectoryExists
    {
        return Assert::directoryExists(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\fileExists')) {
    function fileExists() : FileExists
    {
        return Assert::fileExists(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\greaterThan')) {
    function greaterThan($value) : GreaterThan
    {
        return Assert::greaterThan(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\greaterThanOrEqual')) {
    function greaterThanOrEqual($value) : LogicalOr
    {
        return Assert::greaterThanOrEqual(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\classHasAttribute')) {
    function classHasAttribute(string $attributeName) : ClassHasAttribute
    {
        return Assert::classHasAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\classHasStaticAttribute')) {
    function classHasStaticAttribute(string $attributeName) : ClassHasStaticAttribute
    {
        return Assert::classHasStaticAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\objectHasAttribute')) {
    function objectHasAttribute($attributeName) : ObjectHasAttribute
    {
        return Assert::objectHasAttribute(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\identicalTo')) {
    function identicalTo($value) : IsIdentical
    {
        return Assert::identicalTo(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isInstanceOf')) {
    function isInstanceOf(string $className) : IsInstanceOf
    {
        return Assert::isInstanceOf(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\isType')) {
    function isType(string $type) : IsType
    {
        return Assert::isType(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\lessThan')) {
    function lessThan($value) : LessThan
    {
        return Assert::lessThan(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\lessThanOrEqual')) {
    function lessThanOrEqual($value) : LogicalOr
    {
        return Assert::lessThanOrEqual(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\matchesRegularExpression')) {
    function matchesRegularExpression(string $pattern) : RegularExpression
    {
        return Assert::matchesRegularExpression(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\matches')) {
    function matches(string $string) : StringMatchesFormatDescription
    {
        return Assert::matches(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\stringStartsWith')) {
    function stringStartsWith($prefix) : StringStartsWith
    {
        return Assert::stringStartsWith(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\stringContains')) {
    function stringContains(string $string, bool $case = true) : StringContains
    {
        return Assert::stringContains(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\stringEndsWith')) {
    function stringEndsWith(string $suffix) : StringEndsWith
    {
        return Assert::stringEndsWith(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\countOf')) {
    function countOf(int $count) : Count
    {
        return Assert::countOf(...func_get_args());
    }
}

if (! function_exists('PHPUnit\Framework\any')) {
    /**
     * Returns a matcher that matches when the method is executed
     * zero or more times.
     */
    function any() : AnyInvokedCountMatcher
    {
        return new AnyInvokedCountMatcher();
    }
}

if (! function_exists('PHPUnit\Framework\never')) {
    /**
     * Returns a matcher that matches when the method is never executed.
     */
    function never() : InvokedCountMatcher
    {
        return new InvokedCountMatcher(0);
    }
}

if (! function_exists('PHPUnit\Framework\atLeast')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at least N times.
     */
    function atLeast(int $requiredInvocations) : InvokedAtLeastCountMatcher
    {
        return new InvokedAtLeastCountMatcher(
            $requiredInvocations
        );
    }
}

if (! function_exists('PHPUnit\Framework\atLeastOnce')) {
    /**
     * Returns a matcher that matches when the method is executed at least once.
     */
    function atLeastOnce() : InvokedAtLeastOnceMatcher
    {
        return new InvokedAtLeastOnceMatcher();
    }
}

if (! function_exists('PHPUnit\Framework\once')) {
    /**
     * Returns a matcher that matches when the method is executed exactly once.
     */
    function once() : InvokedCountMatcher
    {
        return new InvokedCountMatcher(1);
    }
}

if (! function_exists('PHPUnit\Framework\exactly')) {
    /**
     * Returns a matcher that matches when the method is executed
     * exactly $count times.
     */
    function exactly(int $count) : InvokedCountMatcher
    {
        return new InvokedCountMatcher($count);
    }
}

if (! function_exists('PHPUnit\Framework\atMost')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at most N times.
     */
    function atMost(int $allowedInvocations) : InvokedAtMostCountMatcher
    {
        return new InvokedAtMostCountMatcher($allowedInvocations);
    }
}

if (! function_exists('PHPUnit\Framework\at')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at the given index.
     */
    function at(int $index) : InvokedAtIndexMatcher
    {
        return new InvokedAtIndexMatcher($index);
    }
}

if (! function_exists('PHPUnit\Framework\returnValue')) {
    function returnValue($value) : ReturnStub
    {
        return new ReturnStub($value);
    }
}

if (! function_exists('PHPUnit\Framework\returnValueMap')) {
    function returnValueMap(array $valueMap) : ReturnValueMapStub
    {
        return new ReturnValueMapStub($valueMap);
    }
}

if (! function_exists('PHPUnit\Framework\returnArgument')) {
    function returnArgument(int $argumentIndex) : ReturnArgumentStub
    {
        return new ReturnArgumentStub($argumentIndex);
    }
}

if (! function_exists('PHPUnit\Framework\returnCallback')) {
    function returnCallback($callback) : ReturnCallbackStub
    {
        return new ReturnCallbackStub($callback);
    }
}

if (! function_exists('PHPUnit\Framework\returnSelf')) {
    /**
     * Returns the current object.
     *
     * This method is useful when mocking a fluent interface.
     */
    function returnSelf() : ReturnSelfStub
    {
        return new ReturnSelfStub();
    }
}

if (! function_exists('PHPUnit\Framework\throwException')) {
    function throwException(Throwable $exception) : ExceptionStub
    {
        return new ExceptionStub($exception);
    }
}

if (! function_exists('PHPUnit\Framework\onConsecutiveCalls')) {
    function onConsecutiveCalls() : ConsecutiveCallsStub
    {
        $args = func_get_args();

        return new ConsecutiveCallsStub($args);
    }
}
