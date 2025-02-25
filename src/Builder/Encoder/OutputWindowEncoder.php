<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use LogicException;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\OutputWindow;
use MongoDB\Builder\Type\WindowInterface;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;
use stdClass;

use function array_key_first;
use function get_debug_type;
use function MongoDB\is_first_key_operator;
use function sprintf;

/**
 * @template-implements Encoder<stdClass, OutputWindow>
 * @internal
 */
final class OutputWindowEncoder implements Encoder
{
    /** @template-use EncodeIfSupported<stdClass, OutputWindow> */
    use EncodeIfSupported;
    use RecursiveEncode;

    public function canEncode(mixed $value): bool
    {
        return $value instanceof OutputWindow;
    }

    public function encode(mixed $value): stdClass
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $result = $this->recursiveEncode($value->operator);

        // Transform the result into an stdClass if a document is provided
        if (! $value->operator instanceof WindowInterface) {
            if (! is_first_key_operator($result)) {
                $firstKey = array_key_first((array) $result);

                throw new LogicException(sprintf('Expected OutputWindow::$operator to be an operator. Got "%s"', $firstKey ?? 'null'));
            }

            $result = (object) $result;
        }

        if (! $result instanceof stdClass) {
            throw new LogicException(sprintf('Expected OutputWindow::$operator to be an stdClass, array or WindowInterface. Got "%s"', get_debug_type($result)));
        }

        if ($value->window !== Optional::Undefined) {
            $result->window = $this->recursiveEncode($value->window);
        }

        return $result;
    }
}
