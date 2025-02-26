<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Search;

use DateTimeInterface;
use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\SearchOperatorInterface;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Model\BSONArray;
use stdClass;

use function array_is_list;
use function is_array;

/**
 * The in operator performs a search for an array of BSON values in a field.
 *
 * @see https://www.mongodb.com/docs/atlas/atlas-search/in/
 * @internal
 */
final class InOperator implements SearchOperatorInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = 'in';
    public const PROPERTIES = ['path' => 'path', 'value' => 'value', 'score' => 'score'];

    /** @var array|string $path */
    public readonly array|string $path;

    /** @var BSONArray|DateTimeInterface|PackedArray|Type|array|bool|float|int|null|stdClass|string $value */
    public readonly DateTimeInterface|PackedArray|Type|BSONArray|stdClass|array|bool|float|int|null|string $value;

    /** @var Optional|Document|Serializable|array|stdClass $score */
    public readonly Optional|Document|Serializable|stdClass|array $score;

    /**
     * @param array|string $path
     * @param BSONArray|DateTimeInterface|PackedArray|Type|array|bool|float|int|null|stdClass|string $value
     * @param Optional|Document|Serializable|array|stdClass $score
     */
    public function __construct(
        array|string $path,
        DateTimeInterface|PackedArray|Type|BSONArray|stdClass|array|bool|float|int|null|string $value,
        Optional|Document|Serializable|stdClass|array $score = Optional::Undefined,
    ) {
        $this->path = $path;
        if (is_array($value) && ! array_is_list($value)) {
            throw new InvalidArgumentException('Expected $value argument to be a list, got an associative array.');
        }

        $this->value = $value;
        $this->score = $score;
    }
}
