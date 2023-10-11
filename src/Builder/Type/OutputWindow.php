<?php

namespace MongoDB\Builder\Type;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\Exception\InvalidArgumentException;
use stdClass;

use function array_is_list;
use function count;

/**
 * Specifies the window boundaries and parameters. Window boundaries are inclusive.
 * Default is an unbounded window, which includes all documents in the partition.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
 */
class OutputWindow implements WindowInterface
{
    public const ENCODE = Encode::Object;

    /** @param Document|Serializable|WindowInterface|array|stdClass $operator Function used to initialize the state. The init function receives its arguments from the initArgs array expression. You can specify the function definition as either BSON type Code or String. */
    public Document|Serializable|WindowInterface|stdClass|array $operator;

    public Optional|stdClass $window;

    /**
     * @param Document|Serializable|WindowInterface|array|stdClass $operator  Window operator to use in the $setWindowFields stage.
     * @param Optional|array{string|int,string|int}                $documents A window where the lower and upper boundaries are specified relative to the position of the current document read from the collection.
     * @param Optional|array{string|numeric,string|numeric}        $range     Arguments passed to the init function.
     * @param Optional|non-empty-string                            $unit      Specifies the units for time range window boundaries. If omitted, default numeric range window boundaries are used.
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
            if (! array_is_list($documents) || ! count($documents) === 2) {
                throw new InvalidArgumentException('Expected $documents argument to be a list of 2 string or int.');
            }

            $window ??= new stdClass();
            $window->documents = $documents;
        }

        if ($range !== Optional::Undefined) {
            if (! array_is_list($range) || ! count($range) === 2) {
                throw new InvalidArgumentException('Expected $range argument to be a list of 2 string or int.');
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
