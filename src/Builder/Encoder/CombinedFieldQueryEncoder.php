<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use LogicException;
use MongoDB\Builder\Type\CombinedFieldQuery;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function get_debug_type;
use function get_object_vars;
use function is_array;
use function is_object;
use function sprintf;

/** @template-extends AbstractExpressionEncoder<stdClass, CombinedFieldQuery> */
class CombinedFieldQueryEncoder extends AbstractExpressionEncoder
{
    /** @template-use EncodeIfSupported<stdClass, CombinedFieldQuery> */
    use EncodeIfSupported;

    public function canEncode(mixed $value): bool
    {
        return $value instanceof CombinedFieldQuery;
    }

    public function encode(mixed $value): stdClass
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $result = new stdClass();
        foreach ($value->fieldQueries as $filter) {
            $filter = $this->recursiveEncode($filter);
            if (is_object($filter)) {
                $filter = get_object_vars($filter);
            } elseif (! is_array($filter)) {
                throw new LogicException(sprintf('Query filters must an array or an object. Got "%s"', get_debug_type($filter)));
            }

            foreach ($filter as $key => $filterValue) {
                $result->{$key} = $filterValue;
            }
        }

        return $result;
    }
}
