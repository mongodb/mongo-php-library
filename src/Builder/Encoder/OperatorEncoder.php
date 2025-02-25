<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use LogicException;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function assert;
use function is_string;
use function sprintf;

/**
 * @template-implements Encoder<stdClass, OperatorInterface>
 * @internal
 */
final class OperatorEncoder implements Encoder
{
    /** @template-use EncodeIfSupported<stdClass, OperatorInterface> */
    use EncodeIfSupported;
    use RecursiveEncode;

    public function canEncode(mixed $value): bool
    {
        return $value instanceof OperatorInterface;
    }

    public function encode(mixed $value): stdClass
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return match ($value::ENCODE) {
            Encode::Single => $this->encodeAsSingle($value),
            Encode::Array => $this->encodeAsArray($value),
            Encode::Object => $this->encodeAsObject($value),
            default => throw new LogicException(sprintf('Class "%s" does not have a valid ENCODE constant.', $value::class)),
        };
    }

    /**
     * Encode the value as an array of properties, in the order they are defined in the class.
     */
    private function encodeAsArray(OperatorInterface $value): stdClass
    {
        $result = [];
        foreach ($value::PROPERTIES as $prop => $name) {
            $val = $value->$prop;
            // Skip optional arguments. For example, the $slice expression operator has an optional <position> argument
            // in the middle of the array.
            if ($val === Optional::Undefined) {
                continue;
            }

            $result[] = $this->recursiveEncode($val);
        }

        return $this->wrap($value, $result);
    }

    /**
     * Encode the value as an object with properties. Property names are
     * mapped by the PROPERTIES constant.
     */
    private function encodeAsObject(OperatorInterface $value): stdClass
    {
        $result = new stdClass();
        foreach ($value::PROPERTIES as $prop => $name) {
            $val = $value->$prop;

            // Skip optional arguments. If they have a default value, it is resolved by the server.
            if ($val === Optional::Undefined) {
                continue;
            }

            // The name is null for arguments with "mergeObject: true" in the YAML file,
            // the value properties are merged into the parent object.
            if ($name === null) {
                $val = $this->recursiveEncode($val);
                foreach ($val as $k => $v) {
                    $result->{$k} = $v;
                }
            } else {
                $result->{$name} = $this->recursiveEncode($val);
            }
        }

        if ($value::NAME === null) {
            return $result;
        }

        return $this->wrap($value, $result);
    }

    /**
     * Get the unique property of the operator as value
     */
    private function encodeAsSingle(OperatorInterface $value): stdClass
    {
        foreach ($value::PROPERTIES as $prop => $name) {
            $result = $this->recursiveEncode($value->$prop);

            return $this->wrap($value, $result);
        }

        throw new LogicException(sprintf('Class "%s" does not have a single property.', $value::class));
    }

    private function wrap(OperatorInterface $value, mixed $result): stdClass
    {
        assert(is_string($value::NAME));

        $object = new stdClass();
        $object->{$value::NAME} = $result;

        return $object;
    }
}
