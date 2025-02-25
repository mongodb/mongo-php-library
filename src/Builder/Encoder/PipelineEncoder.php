<?php

declare(strict_types=1);

namespace MongoDB\Builder\Encoder;

use MongoDB\Builder\Pipeline;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Codec\Encoder;
use MongoDB\Exception\UnsupportedValueException;

/**
 * @template-implements Encoder<list<mixed>, Pipeline>
 * @internal
 */
final class PipelineEncoder implements Encoder
{
    /** @template-use EncodeIfSupported<list<mixed>, Pipeline> */
    use EncodeIfSupported;
    use RecursiveEncode;

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
            $encoded[] = $this->recursiveEncode($stage);
        }

        return $encoded;
    }
}
