<?php

declare(strict_types=1);

namespace MongoDB\Builder\Type;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_is_list;
use function count;
use function get_debug_type;
use function is_int;
use function is_numeric;
use function is_string;
use function sprintf;

/**
 * Specifies the window boundaries and parameters. Window boundaries are inclusive.
 * Default is an unbounded window, which includes all documents in the partition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
 */
class OutputWindow implements WindowInterface
{
    public const ENCODE = Encode::Object;

    /**
     * Function used to initialize the state. The init function receives its arguments from the initArgs array expression.
     * You can specify the function definition as either BSON type Code or String.
     */
    public Document|Serializable|WindowInterface|stdClass|array $operator;

    /**
     * Specifies the window boundaries and parameters.
     */
    public Optional|stdClass $window;

    /**
     * @param Document|Serializable|WindowInterface|array<string, mixed>|stdClass $operator  Window operator to use in the $setWindowFields stage.
     * @param Optional|array{string|int,string|int}                               $documents A window where the lower and upper boundaries are specified relative to the position of the current document read from the collection.
     * @param Optional|array{string|numeric,string|numeric}                       $range     Arguments passed to the init function.
     * @param Optional|non-empty-string                                           $unit      Specifies the units for time range window boundaries. If omitted, default numeric range window boundaries are used.
     */
    public function __construct(
        Document|Serializable|WindowInterface|stdClass|array $operator,
        Optional|array $documents = Optional::Undefined,
        Optional|array $range = Optional::Undefined,
        Optional|string $unit = Optional::Undefined,
    ) {
        $this->operator = $operator;

        $window = null;
        if ($documents !== Optional::Undefined) {
            if (! array_is_list($documents) || count($documents) !== 2) {
                throw new InvalidArgumentException('Expected $documents argument to be a list of 2 string or int');
            }

            if (! is_string($documents[0]) && ! is_int($documents[0]) || ! is_string($documents[1]) && ! is_int($documents[1])) {
                throw new InvalidArgumentException(sprintf('Expected $documents argument to be a list of 2 string or int. Got [%s, %s]', get_debug_type($documents[0]), get_debug_type($documents[1])));
            }

            $window = new stdClass();
            $window->documents = $documents;
        }

        if ($range !== Optional::Undefined) {
            if (! array_is_list($range) || count($range) !== 2) {
                throw new InvalidArgumentException('Expected $range argument to be a list of 2 string or numeric');
            }

            if (! is_string($range[0]) && ! is_numeric($range[0]) || ! is_string($range[1]) && ! is_numeric($range[1])) {
                throw new InvalidArgumentException(sprintf('Expected $range argument to be a list of 2 string or numeric. Got [%s, %s]', get_debug_type($range[0]), get_debug_type($range[1])));
            }

            $window ??= new stdClass();
            $window->range = $range;
        }

        if ($unit !== Optional::Undefined) {
            $window ??= new stdClass();
            $window->unit = $unit;
        }

        $this->window = $window ?? Optional::Undefined;
    }
}
