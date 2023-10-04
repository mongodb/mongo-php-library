<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Pipeline;

class FacetStage implements StageInterface
{
    public const NAME = '$facet';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array<string, Pipeline|array> ...$facet */
    public array $facet;

    /**
     * @param Pipeline|array $facet
     */
    public function __construct(Pipeline|array ...$facet)
    {
        foreach($facet as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $facet arguments to be a map of Pipeline|array, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        if (\count($facet) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $facet, got %d.', 1, \count($facet)));
        }
        $this->facet = $facet;
    }
}
