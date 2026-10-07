<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Query;
use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

use function MongoDB\object;

/**
 * Test $pull update
 */
class PullOperatorTest extends UpdateTestCase
{
    public function testRemoveAllItemsThatEqualASpecifiedValue(): void
    {
        $update = Update::pull(fruits: Query::in(['apples', 'oranges']), vegetables: 'carrots');

        $this->assertSameUpdate(Pipelines::PullRemoveAllItemsThatEqualASpecifiedValue, $update);
    }

    public function testRemoveAllItemsThatMatchASpecifiedPullCondition(): void
    {
        $update = Update::pull(votes: Query::gte(6));

        $this->assertSameUpdate(Pipelines::PullRemoveAllItemsThatMatchASpecifiedPullCondition, $update);
    }

    public function testRemoveDocumentsFromNestedArrays(): void
    {
        $update = Update::pull(results: object(answers: Query::elemMatch(object(q: 2, a: Query::gte(8)))));

        $this->assertSameUpdate(Pipelines::PullRemoveDocumentsFromNestedArrays, $update);
    }

    public function testRemoveItemsFromAnArrayOfDocuments(): void
    {
        $update = Update::pull(results: object(score: 8, item: 'B'));

        $this->assertSameUpdate(Pipelines::PullRemoveItemsFromAnArrayOfDocuments, $update);
    }
}
