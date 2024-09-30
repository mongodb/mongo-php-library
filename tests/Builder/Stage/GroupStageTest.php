<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use DateTimeImmutable;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Builder\Type\Sort;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $group stage
 */
class GroupStageTest extends PipelineTestCase
{
    public function testCalculateCountSumAndAverage(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                date: [
                    Query::gte(new UTCDateTime(new DateTimeImmutable('2014-01-01'))),
                    Query::lt(new UTCDateTime(new DateTimeImmutable('2015-01-01'))),
                ],
            ),
            Stage::group(
                _id: Expression::dateToString(Expression::dateFieldPath('date'), '%Y-%m-%d'),
                totalSaleAmount: Accumulator::sum(
                    Expression::multiply(
                        Expression::numberFieldPath('price'),
                        Expression::numberFieldPath('quantity'),
                    ),
                ),
                averageQuantity: Accumulator::avg(
                    Expression::numberFieldPath('quantity'),
                ),
                count: Accumulator::sum(1),
            ),
            Stage::sort(
                totalSaleAmount: Sort::Desc,
            ),
        );

        $this->assertSamePipeline(Pipelines::GroupCalculateCountSumAndAverage, $pipeline);
    }

    public function testCountTheNumberOfDocumentsInACollection(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: null,
                count: Accumulator::count(),
            ),
        );

        $this->assertSamePipeline(Pipelines::GroupCountTheNumberOfDocumentsInACollection, $pipeline);
    }

    public function testGroupByItemHaving(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('item'),
                totalSaleAmount: Accumulator::sum(
                    Expression::multiply(
                        Expression::numberFieldPath('price'),
                        Expression::numberFieldPath('quantity'),
                    ),
                ),
            ),
            Stage::match(
                totalSaleAmount: Query::gte(100),
            ),
        );

        $this->assertSamePipeline(Pipelines::GroupGroupByItemHaving, $pipeline);
    }

    public function testGroupByNull(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: null,
                totalSaleAmount: Accumulator::sum(
                    Expression::multiply(
                        Expression::numberFieldPath('price'),
                        Expression::numberFieldPath('quantity'),
                    ),
                ),
                averageQuantity: Accumulator::avg(
                    Expression::numberFieldPath('quantity'),
                ),
                count: Accumulator::sum(1),
            ),
        );

        $this->assertSamePipeline(Pipelines::GroupGroupByNull, $pipeline);
    }

    public function testGroupDocumentsByAuthor(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('author'),
                books: Accumulator::push(
                    Expression::variable('ROOT'),
                ),
            ),
            Stage::addFields(
                totalCopies: Expression::sum(
                    Expression::numberFieldPath('books.copies'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::GroupGroupDocumentsByAuthor, $pipeline);
    }

    public function testPivotData(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('author'),
                books: Accumulator::push(
                    Expression::stringFieldPath('title'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::GroupPivotData, $pipeline);
    }

    public function testRetrieveDistinctValues(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::stringFieldPath('item'),
            ),
        );

        $this->assertSamePipeline(Pipelines::GroupRetrieveDistinctValues, $pipeline);
    }
}
