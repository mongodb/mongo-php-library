<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use LogicException;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function get_object_vars;
use function property_exists;
use function sprintf;

/**
 * @template-implements Encoder<stdClass, QueryObject>
 * @internal
 */
final class QueryEncoder implements Encoder
{
    /** @template-use EncodeIfSupported<stdClass, QueryObject> */
    use EncodeIfSupported;
    use RecursiveEncode;

    public function canEncode(mixed $value): bool
    {
        return $value instanceof QueryObject;
    }

    public function encode(mixed $value): stdClass
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $result = new stdClass();
        foreach ($value->queries as $key => $value) {
            if ($value instanceof QueryInterface) {
                // The sub-objects is merged into the main object, replacing duplicate keys
                foreach (get_object_vars($this->recursiveEncode($value)) as $subKey => $subValue) {
                    if (property_exists($result, $subKey)) {
                        throw new LogicException(sprintf('Duplicate key "%s" in query object', $subKey));
                    }

                    $result->{$subKey} = $subValue;
                }
            } else {
                if (property_exists($result, (string) $key)) {
                    throw new LogicException(sprintf('Duplicate key "%s" in query object', $key));
                }

                $result->{$key} = $this->recursiveEncode($value);
            }
        }

        return $result;
    }
}
