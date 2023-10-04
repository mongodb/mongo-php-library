<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToObject;

class ReplaceWithStage implements StageInterface
{
    public const NAME = '$replaceWith';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|ResolvesToObject|Serializable|array|object $expression */
    public array|object $expression;

    /**
     * @param Document|ResolvesToObject|Serializable|array|object $expression
     */
    public function __construct(array|object $expression)
    {
        $this->expression = $expression;
    }
}
