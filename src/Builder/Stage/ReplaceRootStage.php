<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Expression\ResolvesToObject;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Replaces a document with the specified embedded document. The operation replaces all existing fields in the input document, including the _id field. Specify a document embedded in the input document to promote the embedded document to the top level.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/
 */
class ReplaceRootStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;

    /** @var Document|ResolvesToObject|Serializable|array|stdClass $newRoot */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array $newRoot;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass $newRoot
     */
    public function __construct(Document|Serializable|ResolvesToObject|stdClass|array $newRoot)
    {
        $this->newRoot = $newRoot;
    }

    public function getOperator(): string
    {
        return '$replaceRoot';
    }
}
