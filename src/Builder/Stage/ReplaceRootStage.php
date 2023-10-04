<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ResolvesToObject;

class ReplaceRootStage implements StageInterface
{
    public const NAME = '$replaceRoot';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Document|ResolvesToObject|Serializable|array|object $newRoot */
    public array|object $newRoot;

    /**
     * @param Document|ResolvesToObject|Serializable|array|object $newRoot
     */
    public function __construct(array|object $newRoot)
    {
        $this->newRoot = $newRoot;
    }
}
