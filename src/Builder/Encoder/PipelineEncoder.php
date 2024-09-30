<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Builder\Pipeline;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;

/** @template-extends AbstractExpressionEncoder<list<mixed>, Pipeline> */
class PipelineEncoder extends AbstractExpressionEncoder
{
    /** @template-use EncodeIfSupported<list<mixed>, Pipeline> */
    use EncodeIfSupported;

    /** @psalm-assert-if-true Pipeline $value */
    public function canEncode(mixed $value): bool
    {
        return $value instanceof Pipeline;
    }

    /** @return list<mixed> */
    public function encode(mixed $value): array
    {
        if (! $this->canEncode($value)) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        $encoded = [];
        foreach ($value->getIterator() as $stage) {
            $encoded[] = $this->encoder->encodeIfSupported($stage);
        }

        return $encoded;
    }
}
