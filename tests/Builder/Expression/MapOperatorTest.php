<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

use MongoDB\Builder\Expression;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;
use MongoDB\Tests\Builder\PipelineTestCase;

/**
 * Test $map expression
 */
class MapOperatorTest extends PipelineTestCase
{
    public function testAddToEachElementOfAnArray(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                adjustedGrades: Expression::map(
                    input: Expression::arrayFieldPath('quizzes'),
                    as: 'grade',
                    in: [
                        '$add' => [
                            '$$grade',
                            2,
                        ],
                    ],
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MapAddToEachElementOfAnArray, $pipeline);
    }

    public function testConvertCelsiusTemperaturesToFahrenheit(): void
    {
        $pipeline = new Pipeline(
            Stage::addFields(
                tempsF: Expression::map(
                    input: Expression::arrayFieldPath('tempsC'),
                    as: 'tempInCelsius',
                    in: Expression::add(
                        Expression::multiply(
                            Expression::variable('tempInCelsius'),
                            1.8,
                        ),
                        32,
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MapConvertCelsiusTemperaturesToFahrenheit, $pipeline);
    }

    public function testTruncateEachArrayElement(): void
    {
        $pipeline = new Pipeline(
            Stage::project(
                city: Expression::stringFieldPath('city'),
                integerValues: Expression::map(
                    input: Expression::arrayFieldPath('distances'),
                    as: 'decimalValue',
                    in: Expression::trunc(
                        Expression::variable('decimalValue'),
                    ),
                ),
            ),
        );

        $this->assertSamePipeline(Pipelines::MapTruncateEachArrayElement, $pipeline);
    }
}
