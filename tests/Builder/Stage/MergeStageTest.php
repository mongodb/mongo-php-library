<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

use MongoDB\BSON\UTCDateTime;
use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Query;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $merge stage
 */
class MergeStageTest extends PipelineTestCase
{
    public function testMergeResultsFromMultipleCollections(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::stringFieldPath('quarter'),
                purchased: Accumulator::sum(
                    Expression::intFieldPath('qty'),
                ),
            ),
            Stage::merge(
                into: 'quarterlyreport',
                on: '_id',
                whenMatched: 'merge',
                whenNotMatched: 'insert',
            ),
        );

        $this->assertSamePipeline(Pipelines::MergeMergeResultsFromMultipleCollections, $pipeline);
    }

    public function testOnDemandMaterializedViewInitialCreation(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(
                    fiscal_year: Expression::stringFieldPath('fiscal_year'),
                    dept: Expression::stringFieldPath('dept'),
                ),
                salaries: Accumulator::sum(
                    Expression::numberFieldPath('salary'),
                ),
            ),
            Stage::merge(
                into: object(
                    db: 'reporting',
                    coll: 'budgets',
                ),
                on: '_id',
                whenMatched: 'replace',
                whenNotMatched: 'insert',
            ),
        );

        $this->assertSamePipeline(Pipelines::MergeOnDemandMaterializedViewInitialCreation, $pipeline);
    }

    public function testOnDemandMaterializedViewUpdateReplaceData(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                fiscal_year: Query::gte(2019),
            ),
            Stage::group(
                _id: object(
                    fiscal_year: Expression::stringFieldPath('fiscal_year'),
                    dept: Expression::stringFieldPath('dept'),
                ),
                salaries: Accumulator::sum(
                    Expression::numberFieldPath('salary'),
                ),
            ),
            Stage::merge(
                into: object(
                    db: 'reporting',
                    coll: 'budgets',
                ),
                on: '_id',
                whenMatched: 'replace',
                whenNotMatched: 'insert',
            ),
        );

        $this->assertSamePipeline(Pipelines::MergeOnDemandMaterializedViewUpdateReplaceData, $pipeline);
    }

    public function testOnlyInsertNewData(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                fiscal_year: 2019,
            ),
            Stage::group(
                _id: object(
                    fiscal_year: Expression::stringFieldPath('fiscal_year'),
                    dept: Expression::stringFieldPath('dept'),
                ),
                employees: Accumulator::push(
                    Expression::numberFieldPath('employee'),
                ),
            ),
            Stage::project(
                _id: 0,
                dept: Expression::fieldPath('_id.dept'),
                fiscal_year: Expression::fieldPath('_id.fiscal_year'),
                employees: 1,
            ),
            Stage::merge(
                into: object(
                    db: 'reporting',
                    coll: 'orgArchive',
                ),
                on: ['dept', 'fiscal_year'],
                whenMatched: 'fail',
            ),
        );

        $this->assertSamePipeline(Pipelines::MergeOnlyInsertNewData, $pipeline);
    }

    public function testUseThePipelineToCustomizeTheMerge(): void
    {
        $pipeline = new Pipeline(
            Stage::match(
                date: [
                    Query::gte(new UTCDateTime(1557187200000)),
                    Query::lt(new UTCDateTime(1557273600000)),
                ],
            ),
            Stage::project(
                _id: Expression::dateToString(
                    format: '%Y-%m',
                    date: Expression::dateFieldPath('date'),
                ),
                thumbsup: 1,
                thumbsdown: 1,
            ),
            Stage::merge(
                into: 'monthlytotals',
                on: '_id',
                whenMatched: new Pipeline(
                    Stage::addFields(
                        thumbsup: Expression::add(
                            Expression::numberFieldPath('thumbsup'),
                            Expression::variable('new.thumbsup'),
                        ),
                        thumbsdown: Expression::add(
                            Expression::numberFieldPath('thumbsdown'),
                            Expression::variable('new.thumbsdown'),
                        ),
                    ),
                ),
                whenNotMatched: 'insert',
            ),
        );

        $this->assertSamePipeline(Pipelines::MergeUseThePipelineToCustomizeTheMerge, $pipeline);
    }

    public function testUseVariablesToCustomizeTheMerge(): void
    {
        $pipeline = new Pipeline(
            Stage::merge(
                into: 'cakeSales',
                let: object(
                    year: '2020',
                ),
                whenMatched: new Pipeline(
                    Stage::addFields(
                        salesYear: Expression::variable('year'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MergeUseVariablesToCustomizeTheMerge, $pipeline);
    }
}
