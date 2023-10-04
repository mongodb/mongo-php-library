<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Encode;
use MongoDB\Builder\Expression\ExpressionInterface;

class SetStage implements StageInterface
{
    public const NAME = '$set';
    public const ENCODE = \MongoDB\Builder\Encode::Single;

    /** @param array<string, ExpressionInterface|mixed> ...$field */
    public array $field;

    /**
     * @param ExpressionInterface|mixed $field
     */
    public function __construct(mixed ...$field)
    {
        foreach($field as $key => $value) {
            if (! \is_string($key)) {
                throw new \InvalidArgumentException('Expected $field arguments to be a map of ExpressionInterface|mixed, named arguments (<name>:<value>) or array unpacking ...[\'<name>\' => <value>] must be used');
            }
        }
        if (\count($field) < 1) {
            throw new \InvalidArgumentException(\sprintf('Expected at least %d values for $field, got %d.', 1, \count($field)));
        }
        $this->field = $field;
    }
}
