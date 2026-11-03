<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Expression;

use DateTimeInterface;
use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
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
 * Converts Extended JSON (EJSON) format to native BSON values. Use this expression to
 * transform EJSON type wrappers into their corresponding BSON types after parsing a JSON
 * string with $convert.
 *
 * New in MongoDB 8.3
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/deserializeEJSON/
 * @internal
 */
final class DeserializeEJSONOperator implements ResolvesToObject, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$deserializeEJSON';
    public const PROPERTIES = ['input' => 'input', 'onError' => 'onError'];

    /**
     * @var Document|ResolvesToObject|Serializable|array|stdClass|string $input The Extended JSON value to convert to native BSON format. This should be a BSON
     * document containing EJSON type wrappers.
     */
    public readonly Document|Serializable|ResolvesToObject|stdClass|array|string $input;

    /**
     * @var Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError The value to return if the operation encounters an error during conversion.
     * If unspecified, the operation throws an error and stops.
     */
    public readonly Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError;

    /**
     * @param Document|ResolvesToObject|Serializable|array|stdClass|string $input The Extended JSON value to convert to native BSON format. This should be a BSON
     * document containing EJSON type wrappers.
     * @param Optional|DateTimeInterface|ExpressionInterface|Type|array|bool|float|int|null|stdClass|string $onError The value to return if the operation encounters an error during conversion.
     * If unspecified, the operation throws an error and stops.
     */
    public function __construct(
        Document|Serializable|ResolvesToObject|stdClass|array|string $input,
        Optional|DateTimeInterface|Type|ExpressionInterface|stdClass|array|bool|float|int|null|string $onError = Optional::Undefined,
    ) {
        if (is_string($input) && ! str_starts_with($input, '$')) {
            throw new InvalidArgumentException('Argument $input can be an expression, field paths and variable names must be prefixed by "$" or "$$".');
        }

        $this->input = $input;
        $this->onError = $onError;
    }
}
