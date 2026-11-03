<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

use function MongoDB\object;

/**
 * Test $push update
 */
class PushOperatorTest extends UpdateTestCase
{
    public function testAppendAValueToAnArray(): void
    {
        $update = Update::push(scores: 89);

        $this->assertSameUpdate(Pipelines::PushAppendAValueToAnArray, $update);
    }

    public function testAppendAValueToArraysInMultipleDocuments(): void
    {
        $update = Update::push(scores: 95);

        $this->assertSameUpdate(Pipelines::PushAppendAValueToArraysInMultipleDocuments, $update);
    }

    public function testAppendMultipleValuesToAnArray(): void
    {
        $update = Update::push(scores: ['$each' => [90, 92, 85]]);

        $this->assertSameUpdate(Pipelines::PushAppendMultipleValuesToAnArray, $update);
    }

    public function testUsePushWithMultipleModifiers(): void
    {
        $update = Update::push(
            quizzes: [
                '$each' => [
                    object(wk: 5, score: 8),
                    object(wk: 6, score: 7),
                    object(wk: 7, score: 6),
                ],
                '$sort' => object(score: -1),
                '$slice' => 3,
            ],
        );

        $this->assertSameUpdate(Pipelines::PushUsePushWithMultipleModifiers, $update);
    }
}
