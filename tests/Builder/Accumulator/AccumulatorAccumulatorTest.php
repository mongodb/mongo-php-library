<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

use MongoDB\BSON\Javascript;
use MongoDB\Builder\Accumulator;
use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

use function MongoDB\object;

/**
 * Test $accumulator accumulator
 */
class AccumulatorAccumulatorTest extends PipelineTestCase
{
    public function testUseAccumulatorToImplementTheAvgOperator(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: Expression::fieldPath('author'),
                avgCopies: Accumulator::accumulator(
                    init: new Javascript('function () { return { count: 0, sum: 0 } }'),
                    accumulate: new Javascript('function (state, numCopies) { return { count: state.count + 1, sum: state.sum + numCopies } }'),
                    accumulateArgs: [Expression::fieldPath('copies')],
                    merge: new Javascript('function (state1, state2) { return { count: state1.count + state2.count, sum: state1.sum + state2.sum } }'),
                    finalize: new Javascript('function (state) { return (state.sum / state.count) }'),
                    lang: 'js',
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AccumulatorUseAccumulatorToImplementTheAvgOperator, $pipeline);
    }

    public function testUseInitArgsToVaryTheInitialStateByGroup(): void
    {
        $pipeline = new Pipeline(
            Stage::group(
                _id: object(city: Expression::fieldPath('city')),
                restaurants: Accumulator::accumulator(
                    init: new Javascript('function (city, userProfileCity) { return { max: city === userProfileCity ? 3 : 1, restaurants: [] } }'),
                    accumulate: new Javascript('function (state, restaurantName) { if (state.restaurants.length < state.max) { state.restaurants.push(restaurantName); } return state; }'),
                    accumulateArgs: [Expression::fieldPath('name')],
                    merge: new Javascript('function (state1, state2) { return { max: state1.max, restaurants: state1.restaurants.concat(state2.restaurants).slice(0, state1.max) } }'),
                    lang: 'js',
                    initArgs: [
                        Expression::fieldPath('city'),
                        'Bettles',
                    ],
                    finalize: new Javascript('function (state) { return state.restaurants }'),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AccumulatorUseInitArgsToVaryTheInitialStateByGroup, $pipeline);
    }
}
