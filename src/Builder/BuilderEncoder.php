<?php

namespace MongoDB\Builder;

use LogicException;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\Variable;
use MongoDB\Builder\Query\QueryInterface;
use MongoDB\Builder\Stage\GroupStage;
use MongoDB\Builder\Stage\StageInterface;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function get_object_vars;
use function is_array;
use function sprintf;

/** @template-implements Encoder<Pipeline|StageInterface|ExpressionInterface, stdClass|array|string> */
class BuilderEncoder implements Encoder
{
    use EncodeIfSupported;

    /**
     * {@inheritdoc}
     */
    public function canEncode($value): bool
    {
        return $value instanceof Pipeline
            || $value instanceof StageInterface
            || $value instanceof ExpressionInterface
            || $value instanceof QueryInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function encode($value): stdClass|array|string
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        // A pipeline is encoded as a list of stages
        if ($value instanceof Pipeline) {
            $encoded = [];
            foreach ($value->getIterator() as $stage) {
                $encoded[] = $this->encodeIfSupported($stage);
            }

            return $encoded;
        }

        // This specific encoding code if temporary until we have a generic way to encode stages and operators
        if ($value instanceof FieldPath) {
            return '$' . $value->expression;
        }

        if ($value instanceof Variable) {
            return '$$' . $value->expression;
        }

        // The generic but incomplete encoding code
        switch ($value::ENCODE) {
            case Encode::Single:
                return $this->encodeAsSingle($value);

            case Encode::Array:
                return $this->encodeAsArray($value);

            case Encode::Object:
                return $this->encodeAsObject($value);

            case Encode::Group:
                return $this->encodeAsGroup($value);
        }

        throw new LogicException(sprintf('Class "%s" does not have a valid ENCODE constant.', $value::class));
    }

    private function encodeAsArray(ExpressionInterface|StageInterface|QueryInterface $value): stdClass
    {
        $result = [];
        /** @var mixed $val */
        foreach (get_object_vars($value) as $val) {
            // Skip optional arguments.
            // $slice operator has the optional <position> argument in the middle of the array
            if ($val === Optional::Undefined) {
                continue;
            }

            $result[] = $this->recursiveEncode($val);
        }

        return $this->wrap($value, $result);
    }

    private function encodeAsObject(ExpressionInterface|StageInterface|QueryInterface $value): stdClass
    {
        $result = new stdClass();
        foreach (get_object_vars($value) as $key => $val) {
            // Skip optional arguments. If they have a default value, it is resolved by the server.
            if ($val === Optional::Undefined) {
                continue;
            }

            $result->{$key} = $this->recursiveEncode($val);
        }

        return $this->wrap($value, $result);
    }

    /**
     * Get the unique property of the operator as value
     */
    private function encodeAsSingle(ExpressionInterface|StageInterface|QueryInterface $value): stdClass
    {
        $result = [];
        foreach (get_object_vars($value) as $val) {
            $result = $this->recursiveEncode($val);
            break;
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

        foreach ($value->field ?? [] as $key => $val) {
            $result->{$key} = $this->recursiveEncode($val);
        }

        return $this->wrap($value, $result);
    }

    /**
     * Nested arrays and objects must be encoded recursively.
     */
    private function recursiveEncode(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->recursiveEncode($val);
            }

            return $value;
        }

        if ($value instanceof stdClass) {
            foreach (get_object_vars($value) as $key => $val) {
                $value->{$key} = $this->recursiveEncode($val);
            }
        }

        return $this->encodeIfSupported($value);
    }

    private function wrap(ExpressionInterface|StageInterface|QueryInterface $value, mixed $result): stdClass
    {
        $object = new stdClass();
        $object->{$value::NAME} = $result;

        return $object;
    }
}
