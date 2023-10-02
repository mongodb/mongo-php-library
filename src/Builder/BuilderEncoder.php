<?php

namespace MongoDB\Builder;

use LogicException;
use MongoDB\Builder\Expression\ExpressionInterface;
use MongoDB\Builder\Expression\FieldPath;
use MongoDB\Builder\Expression\Variable;
use MongoDB\Builder\Query\OrQuery;
use MongoDB\Builder\Stage\GroupStage;
use MongoDB\Builder\Stage\ProjectStage;
use MongoDB\Builder\Stage\StageInterface;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function array_is_list;
use function array_merge;
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
        return $value instanceof Pipeline || $value instanceof StageInterface || $value instanceof ExpressionInterface;
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

        if ($value instanceof GroupStage) {
            $result = new stdClass();
            $result->_id = $this->encodeIfSupported($value->_id);
            // Specific: fields are encoded as a map of properties to their values at the top level as _id
            foreach ($value->fields ?? [] as $key => $val) {
                $result->{$key} = $this->encodeIfSupported($val);
            }

            return $this->wrap($value, $result);
        }

        if ($value instanceof ProjectStage) {
            $result = new stdClass();
            // Specific: fields are encoded as a map of properties to their values at the top level as _id
            foreach ($value->specifications as $key => $val) {
                $result->{$key} = $this->encodeIfSupported($val);
            }

            return $this->wrap($value, $result);
        }

        if ($value instanceof OrQuery) {
            $result = [];
            foreach ($value->query as $query) {
                $encodedQuery = new stdClass();
                foreach ($query as $field => $expression) {
                    // Specific: $or queries are encoded as a list of expressions
                    // We need to merge query expressions into a single object
                    if (is_array($expression) && array_is_list($expression)) {
                        $mergedExpressions = [];
                        foreach ($expression as $expr) {
                            $mergedExpressions = array_merge($mergedExpressions, (array) $this->encodeIfSupported($expr));
                        }

                        $encodedQuery->{$field} = (object) $mergedExpressions;
                    } else {
                        $encodedQuery->{$field} = $this->encodeIfSupported($expression);
                    }
                }

                $result[] = $encodedQuery;
            }

            return $this->wrap($value, $result);
        }

        // The generic but incomplete encoding code
        switch ($value::ENCODE) {
            case 'single':
                return $this->encodeAsSingle($value);

            case 'array':
                return $this->encodeAsArray($value);

            case 'object':
                return $this->encodeAsObject($value);
        }

        throw new LogicException(sprintf('Class "%s" does not have a valid ENCODE constant.', $value::class));
    }

    private function encodeAsArray(ExpressionInterface|StageInterface $value): stdClass
    {
        $result = [];
        /** @var mixed $val */
        foreach (get_object_vars($value) as $val) {
            $result[] = $this->encodeIfSupported($val);
        }

        return $this->wrap($value, $result);
    }

    private function encodeAsObject(ExpressionInterface|StageInterface $value): stdClass
    {
        $result = new stdClass();
        /** @var mixed $val */
        foreach (get_object_vars($value) as $key => $val) {
            /** @var mixed $val */
            $val = $this->encodeIfSupported($val);
            if ($val !== Optional::Undefined) {
                $result->{$key} = $val;
            }
        }

        return $this->wrap($value, $result);
    }

    private function encodeAsSingle(ExpressionInterface|StageInterface $value): stdClass
    {
        $result = [];
        /** @var mixed $val */
        foreach (get_object_vars($value) as $val) {
            $result = $this->encodeIfSupported($val);
            break;
        }

        return $this->wrap($value, $result);
    }

    private function wrap(ExpressionInterface|StageInterface $value, mixed $result): stdClass
    {
        $object = new stdClass();
        $object->{$value::NAME} = $result;

        return $object;
    }
}
