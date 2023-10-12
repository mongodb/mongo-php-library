<?php

namespace MongoDB\Builder;

use LogicException;
use MongoDB\Builder\Expression\Variable;
use MongoDB\Builder\Stage\GroupStage;
use MongoDB\Builder\Type\AccumulatorInterface;
use MongoDB\Builder\Type\CombinedFieldQuery;
use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\ExpressionInterface;
use MongoDB\Builder\Type\FieldPathInterface;
use MongoDB\Builder\Type\FieldQueryInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\OutputWindow;
use MongoDB\Builder\Type\ProjectionInterface;
use MongoDB\Builder\Type\QueryInterface;
use MongoDB\Builder\Type\QueryObject;
use MongoDB\Builder\Type\StageInterface;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function array_key_first;
use function assert;
use function get_debug_type;
use function get_object_vars;
use function is_array;
use function is_object;
use function MongoDB\is_first_key_operator;
use function property_exists;
use function sprintf;

/** @template-implements Encoder<Pipeline|StageInterface|ExpressionInterface|QueryInterface, stdClass|array|string> */
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
            || $value instanceof QueryInterface
            || $value instanceof FieldQueryInterface
            || $value instanceof AccumulatorInterface
            || $value instanceof ProjectionInterface
            || $value instanceof WindowInterface;
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
        if ($value instanceof FieldPathInterface) {
            return '$' . $value->name;
        }

        if ($value instanceof Variable) {
            return '$$' . $value->name;
        }

        if ($value instanceof QueryObject) {
            return $this->encodeQueryObject($value);
        }

        if ($value instanceof CombinedFieldQuery) {
            return $this->encodeCombinedFilter($value);
        }

        if ($value instanceof OutputWindow) {
            return $this->encodeOutputWindow($value);
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
                assert($value instanceof GroupStage);

                return $this->encodeAsGroup($value);
        }

        throw new LogicException(sprintf('Class "%s" does not have a valid ENCODE constant.', $value::class));
    }

    /**
     * Encode the value as an array of properties, in the order they are defined in the class.
     */
    private function encodeAsArray(object $value): stdClass
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

    private function encodeAsObject(object $value): stdClass
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
    private function encodeAsSingle(AccumulatorInterface|ExpressionInterface|StageInterface|QueryInterface|FieldQueryInterface|WindowInterface $value): stdClass
    {
        foreach (get_object_vars($value) as $val) {
            $result = $this->recursiveEncode($val);

            return $this->wrap($value, $result);
        }

        throw new LogicException(sprintf('Class "%s" does not have a single property.', $value::class));
    }

    private function encodeCombinedFilter(CombinedFieldQuery $filter): stdClass
    {
        $result = new stdClass();
        foreach ($filter->filters as $filter) {
            $filter = $this->recursiveEncode($filter);
            if (is_object($filter)) {
                $filter = get_object_vars($filter);
            } elseif (! is_array($filter)) {
                throw new LogicException(sprintf('Query filters must an array or an object. Got "%s"', get_debug_type($filter)));
            }

            foreach ($filter as $key => $value) {
                $result->{$key} = $value;
            }
        }

        return $result;
    }

    /**
     * Query objects are encoded by merging query operator with field path to filter operators in the same object.
     */
    private function encodeQueryObject(QueryObject $query): stdClass
    {
        $result = new stdClass();
        foreach ($query->queries as $key => $value) {
            if ($value instanceof QueryInterface) {
                // The sub-objects is merged into the main object, replacing duplicate keys
                foreach (get_object_vars($this->recursiveEncode($value)) as $subKey => $subValue) {
                    if (property_exists($result, $subKey)) {
                        throw new LogicException(sprintf('Duplicate key "%s" in query object', $subKey));
                    }

                    $result->{$subKey} = $subValue;
                }
            } else {
                if (property_exists($result, $key)) {
                    throw new LogicException(sprintf('Duplicate key "%s" in query object', $key));
                }

                $result->{$key} = $this->encodeIfSupported($value);
            }
        }

        return $result;
    }

    /**
     * For the $setWindowFields stage output parameter, the optional window parameter is encoded in the same object
     * of the window operator.
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/
     */
    private function encodeOutputWindow(OutputWindow $outputWindow): stdClass
    {
        $result = $this->recursiveEncode($outputWindow->operator);

        // Transform the result into an stdClass if a document is provided
        if (! $outputWindow->operator instanceof WindowInterface && (is_array($result) || is_object($result))) {
            if (! is_first_key_operator($result)) {
                throw new LogicException(sprintf('Expected OutputWindow::$operator to be an operator. Got "%s"', array_key_first($result)));
            }

            $result = (object) $result;
        }

        if (! $result instanceof stdClass) {
            throw new LogicException(sprintf('Expected OutputWindow::$operator to be an stdClass, array or WindowInterface. Got "%s"', get_debug_type($result)));
        }

        if ($outputWindow->window !== Optional::Undefined) {
            $result->window = $this->recursiveEncode($outputWindow->window);
        }

        return $result;
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

    private function wrap(object $value, mixed $result): stdClass
    {
        $object = new stdClass();
        $object->{$value::NAME} = $result;

        return $object;
    }
}
