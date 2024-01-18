<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

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
                    init: <<<'JS'
                        function() {
                            return { count: 0, sum: 0 }
                        }
                        JS,
                    accumulate: <<<'JS'
                        function(state, numCopies) {
                            return { count: state.count + 1, sum: state.sum + numCopies }
                        }
                        JS,
                    accumulateArgs: [Expression::fieldPath('copies')],
                    merge: <<<'JS'
                        function(state1, state2) {
                            return {
                                count: state1.count + state2.count,
                                sum: state1.sum + state2.sum
                            }
                        }
                        JS,
                    finalize: <<<'JS'
                        function(state) {
                            return (state.sum / state.count)
                        }
                        JS,
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
                    init: <<<'JS'
                        function(city, userProfileCity) {
                            return { max: city === userProfileCity ? 3 : 1, restaurants: [] }
                        }
                        JS,
                    accumulate: <<<'JS'
                        function(state, restaurantName) {
                            if (state.restaurants.length < state.max) {
                                state.restaurants.push(restaurantName);
                            }
                            return state;
                        }
                        JS,
                    accumulateArgs: [Expression::fieldPath('name')],
                    merge: <<<'JS'
                        function(state1, state2) {
                            return {
                                max: state1.max,
                                restaurants: state1.restaurants.concat(state2.restaurants).slice(0, state1.max)
                            }
                        }
                        JS,
                    lang: 'js',
                    initArgs: [
                        Expression::fieldPath('city'),
                        'Bettles',
                    ],
                    finalize: <<<'JS'
                        function(state) {
                            return state.restaurants
                        }
                        JS,
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::AccumulatorUseInitArgsToVaryTheInitialStateByGroup, $pipeline);
    }
}
