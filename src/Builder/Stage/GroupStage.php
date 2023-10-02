<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Expression\ExpressionInterface;

class GroupStage implements StageInterface
{
    public const NAME = '$group';
    public const ENCODE = 'object';

    public mixed $_id;

    /** @param list<ExpressionInterface|mixed> ...$fields */
    public array $fields;

    /**
     * @param ExpressionInterface|mixed|null $_id
     * @param ExpressionInterface|mixed $fields
     */
    public function __construct(mixed $_id, mixed ...$fields)
    {
        $this->_id = $_id;
        $this->fields = $fields;
    }
}
