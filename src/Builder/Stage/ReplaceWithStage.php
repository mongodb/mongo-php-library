<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToObject;
use stdClass;

class ReplaceWithStage implements StageInterface
{
    public const NAME = '$replaceWith';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param Document|ResolvesToObject|Serializable|array|stdClass $expression */
    public Document|Serializable|ResolvesToObject|stdClass|array $expression;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass $expression
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array $expression)
    {
        $this->expression = $expression;
    }
}
