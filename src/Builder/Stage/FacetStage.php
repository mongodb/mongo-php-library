<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\PackedArray;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function is_string;

/**
 * Processes multiple aggregation pipelines within a single stage on the same set of input documents. Enables the creation of multi-faceted aggregations capable of characterizing data across multiple dimensions, or facets, in a single stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/facet/
 */
class FacetStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Single;

    /** @var stdClass<BSONArray|PackedArray|Pipeline|array> $facet */
    public readonly stdClass $facet;

    /**
     * @param BSONArray|PackedArray|Pipeline|array ...$facet
     */
    public function __construct(PackedArray|Pipeline|BSONArray|array ...$facet)
    {
        if (\count($facet) < 1) {
            throw new InvalidArgumentException(\sprintf('Expected at least %d values for $facet, got %d.', 1, \count($facet)));
        }

        foreach($facet as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('Expected $facet arguments to be a map (object), named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }

        $facet = (object) $facet;
        $this->facet = $facet;
    }

    public function getOperator(): string
    {
        return '$facet';
    }
}
