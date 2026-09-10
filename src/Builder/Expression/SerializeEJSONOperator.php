<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Type;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function is_string;
use function str_starts_with;

/**
 * Converts BSON values to Extended JSON (EJSON) format. The result is a
 * BSON document with EJSON type wrappers that can then be converted to
 * a JSON string using $toString.
 *
 * New in MongoDB 8.3
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/serializeEJSON/
 * @internal
 */
final class SerializeEJSONOperator implements ResolvesToObject, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$serializeEJSON';
    public const PROPERTIES = ['input' => 'input', 'relaxed' => 'relaxed', 'onError' => 'onError'];

    /** @var DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input The BSON value to convert to Extended JSON format. */
    public readonly DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input;

    /**
     * @var Optional|ResolvesToBool|bool|string $relaxed Specifies whether to use Relaxed Extended JSON format. If true, numeric types
     * (Int32, Int64, Double) are represented as native JSON numbers for better readability.
     * If false or unspecified, uses Canonical Extended JSON format which preserves type
     * information for all BSON types. Defaults to false.
     */
    public readonly Optional|ResolvesToBool|bool|string $relaxed;

    /**
     * @var Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError The value to return if the operation encounters an error during conversion.
     * If unspecified, the operation throws an error and stops.
     */
    public readonly Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError;

    /**
     * @param DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $input The BSON value to convert to Extended JSON format.
     * @param Optional|ResolvesToBool|bool|string $relaxed Specifies whether to use Relaxed Extended JSON format. If true, numeric types
     * (Int32, Int64, Double) are represented as native JSON numbers for better readability.
     * If false or unspecified, uses Canonical Extended JSON format which preserves type
     * information for all BSON types. Defaults to false.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError The value to return if the operation encounters an error during conversion.
     * If unspecified, the operation throws an error and stops.
     */
    public function __construct(
        DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $input,
        Optional|ResolvesToBool|bool|string $relaxed = Optional::Undefined,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
    ) {
        $this->input = $input;
        if (is_string($relaxed) && ! str_starts_with($relaxed, '$')) {
            throw new InvalidArgumentException('Argument $relaxed can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->relaxed = $relaxed;
        $this->onError = $onError;
    }
}
