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

/**
 * Replaces a document with the specified embedded document. The operation replaces all existing fields in the input document, including the _id field. Specify a document embedded in the input document to promote the embedded document to the top level.
 * Alias for $replaceRoot.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/
 */
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
