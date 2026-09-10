<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the generated minItems/maxItems validation works correctly
 * for the $similarityCosine, $similarityDotProduct, and $similarityEuclidean operators.
 */
class SimilarityValidationTest extends TestCase
{
    public static function provideWrongItemCount(): iterable
    {
        yield 'empty array' => [[]];
        yield 'one item' => [['$a']];
        yield 'three items' => [['$a', '$b', '$c']];
        yield 'BSONArray with one item' => [new BSONArray(['$a'])];
        yield 'BSONArray with three items' => [new BSONArray(['$a', '$b', '$c'])];
    }

    #[DataProvider('provideWrongItemCount')]
    public function testSimilarityCosineRejectsWrongVectorCount(array|BSONArray $vectors): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected exactly 2 items for \$vectors/');

        Expression::similarityCosine($vectors);
    }

    #[DataProvider('provideWrongItemCount')]
    public function testSimilarityDotProductRejectsWrongVectorCount(array|BSONArray $vectors): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected exactly 2 items for \$vectors/');

        Expression::similarityDotProduct($vectors);
    }

    #[DataProvider('provideWrongItemCount')]
    public function testSimilarityEuclideanRejectsWrongVectorCount(array|BSONArray $vectors): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Expected exactly 2 items for \$vectors/');

        Expression::similarityEuclidean($vectors);
    }

    public function testSimilarityCosineAcceptsExactlyTwoItems(): void
    {
        $operator = Expression::similarityCosine(['$a', '$b']);
        $this->assertSame(['$a', '$b'], $operator->vectors);
    }

    public function testSimilarityCosineAcceptsBSONArrayWithTwoItems(): void
    {
        $vectors = new BSONArray(['$a', '$b']);
        $operator = Expression::similarityCosine($vectors);
        $this->assertSame($vectors, $operator->vectors);
    }
}
