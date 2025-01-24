<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Stage;

use MongoDB\Builder\Expression\ArrayFieldPath;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\StageInterface;

/**
 * Deconstructs an array field from the input documents to output a document for each element. Each output document replaces the array with an element value. For each input document, outputs n documents where n is the number of array elements and can be zero for an empty array.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/
 * @internal
 */
final class UnwindStage implements StageInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$unwind';

    public const PROPERTIES = [
        'path' => 'path',
        'includeArrayIndex' => 'includeArrayIndex',
        'preserveNullAndEmptyArrays' => 'preserveNullAndEmptyArrays',
    ];

    /** @var ArrayFieldPath|string $path Field path to an array field. */
    public readonly ArrayFieldPath|string $path;

    /** @var Optional|string $includeArrayIndex The name of a new field to hold the array index of the element. The name cannot start with a dollar sign $. */
    public readonly Optional|string $includeArrayIndex;

    /**
     * @var Optional|bool $preserveNullAndEmptyArrays If true, if the path is null, missing, or an empty array, $unwind outputs the document.
     * If false, if path is null, missing, or an empty array, $unwind does not output a document.
     * The default value is false.
     */
    public readonly Optional|bool $preserveNullAndEmptyArrays;

    /**
     * @param ArrayFieldPath|string $path Field path to an array field.
     * @param Optional|string $includeArrayIndex The name of a new field to hold the array index of the element. The name cannot start with a dollar sign $.
     * @param Optional|bool $preserveNullAndEmptyArrays If true, if the path is null, missing, or an empty array, $unwind outputs the document.
     * If false, if path is null, missing, or an empty array, $unwind does not output a document.
     * The default value is false.
     */
    public function __construct(
        ArrayFieldPath|string $path,
        Optional|string $includeArrayIndex = Optional::Undefined,
        Optional|bool $preserveNullAndEmptyArrays = Optional::Undefined,
    ) {
        $this->path = $path;
        $this->includeArrayIndex = $includeArrayIndex;
        $this->preserveNullAndEmptyArrays = $preserveNullAndEmptyArrays;
    }
}
