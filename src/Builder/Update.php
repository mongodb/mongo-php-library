<?php

declare(strict_types=1);

namespace MongoDB\Builder;

use MongoDB\Builder\Type\FieldPathInterface;
use MongoDB\Builder\Type\UpdateInterface;
use MongoDB\Builder\Update\UnsetOperator;

use function array_fill_keys;
use function array_map;

/**
 * Factories for Update Operators
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/update/
 */
final class Update
{
    use Update\FactoryTrait;

    public array $update = [];

    public function __construct(UpdateInterface ...$update)
    {
        $this->update = $update;
    }

    /**
     * Removes the specified field from a document.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/unset/
     */
    public static function unset(
        FieldPathInterface|string ...$field,
    ): UnsetOperator {
        $field = array_fill_keys(
            array_map(
                static function (FieldPathInterface|string $field): string {
                    if ($field instanceof FieldPathInterface) {
                        return $field->name;
                    }

                    return $field;
                },
                $field,
            ),
            '',
        );

        return new UnsetOperator(...$field);
    }
}
