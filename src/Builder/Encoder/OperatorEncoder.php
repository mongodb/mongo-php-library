<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use LogicException;
use MongoDB\Builder\Stage\GroupStage;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function array_key_exists;
use function assert;
use function get_object_vars;
use function is_array;
use function is_object;
use function property_exists;
use function sprintf;

/** @template-extends AbstractExpressionEncoder<stdClass, OperatorInterface> */
class OperatorEncoder extends AbstractExpressionEncoder
{
    /** @template-use EncodeIfSupported<stdClass, OperatorInterface> */
    use EncodeIfSupported;

    public function canEncode(mixed $value): bool
    {
        return $value instanceof OperatorInterface;
    }

    public function encode(mixed $value): stdClass
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        switch ($value::ENCODE) {
            case Encode::Single:
                return $this->encodeAsSingle($value);

            case Encode::Array:
                return $this->encodeAsArray($value);

            case Encode::Object:
            case Encode::FlatObject:
                return $this->encodeAsObject($value);

            case Encode::DollarObject:
                return $this->encodeAsDollarObject($value);

            case Encode::Group:
                assert($value instanceof GroupStage);

                return $this->encodeAsGroup($value);
        }

        throw new LogicException(sprintf('Class "%s" does not have a valid ENCODE constant.', $value::class));
    }

    /**
     * Encode the value as an array of properties, in the order they are defined in the class.
     */
    private function encodeAsArray(OperatorInterface $value): stdClass
    {
        $result = [];
        /** @var mixed $val */
        foreach (get_object_vars($value) as $val) {
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
     * $group stage have a specific encoding because the _id argument is required and others are variadic
     */
    private function encodeAsGroup(GroupStage $value): stdClass
    {
        $result = new stdClass();
        $result->_id = $this->recursiveEncode($value->_id);

        foreach (get_object_vars($value->field) as $key => $val) {
            $result->{$key} = $this->recursiveEncode($val);
        }

        return $this->wrap($value, $result);
    }

    private function encodeAsObject(OperatorInterface $value): stdClass
    {
        $result = new stdClass();
        foreach (get_object_vars($value) as $key => $val) {
            // Skip optional arguments. If they have a default value, it is resolved by the server.
            if ($val === Optional::Undefined) {
                continue;
            }

            $result->{$key} = $this->recursiveEncode($val);
        }

        return $value::ENCODE === Encode::FlatObject
            ? $result
            : $this->wrap($value, $result);
    }

    private function encodeAsDollarObject(OperatorInterface $value): stdClass
    {
        $result = new stdClass();
        foreach (get_object_vars($value) as $key => $val) {
            // Skip optional arguments. If they have a default value, it is resolved by the server.
            if ($val === Optional::Undefined) {
                continue;
            }

            $val = $this->recursiveEncode($val);

            if ($key === 'geometry') {
                if (is_object($val) && property_exists($val, '$geometry')) {
                    $result->{'$geometry'} = $val->{'$geometry'};
                } elseif (is_array($val) && array_key_exists('$geometry', $val)) {
                    $result->{'$geometry'} = $val['$geometry'];
                } else {
                    $result->{'$geometry'} = $val;
                }
            } else {
                $result->{'$' . $key} = $val;
            }
        }

        return $this->wrap($value, $result);
    }

    /**
     * Get the unique property of the operator as value
     */
    private function encodeAsSingle(OperatorInterface $value): stdClass
    {
        foreach (get_object_vars($value) as $val) {
            $result = $this->recursiveEncode($val);

            return $this->wrap($value, $result);
        }

        throw new LogicException(sprintf('Class "%s" does not have a single property.', $value::class));
    }

    private function wrap(OperatorInterface $value, mixed $result): stdClass
    {
        $object = new stdClass();
        $object->{$value->getOperator()} = $result;

        return $object;
    }
}
