<?php

namespace MongoDB\Codec;

use MongoDB\BSON\PackedArray;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Model\LazyBSONArray;

use function array_values;
use function sprintf;

/**
 * Codec for lazy decoding of BSON PackedArray instances
 *
 * @template-implements Codec<PackedArray, LazyBSONArray>
 */
class LazyBSONArrayCodec implements Codec, KnowsCodecLibrary
{
    /** @var CodecLibrary|null */
    private $library = null;

    public function attachLibrary(CodecLibrary $library): void
    {
        $this->library = $library;
    }

    /** @inheritDoc */
    public function canDecode($value): bool
    {
        return $value instanceof PackedArray;
    }

    /** @inheritDoc */
    public function canEncode($value): bool
    {
        return $value instanceof LazyBSONArray;
    }

    /** @inheritDoc */
    public function decode($value): LazyBSONArray
    {
        if (! $value instanceof PackedArray) {
            throw new UnexpectedValueException(sprintf('"%s" can only decode from "%s" instances', self::class, PackedArray::class));
        }

        return new LazyBSONArray($value, $this->getLibrary());
    }

    /** @inheritDoc */
    public function encode($value): PackedArray
    {
        if (! $value instanceof LazyBSONArray) {
            throw new UnexpectedValueException(sprintf('"%s" can only encode "%s" instances', self::class, LazyBSONArray::class));
        }

        $return = [];
        foreach ($value as $offset => $offsetValue) {
            $return[$offset] = $this->getLibrary()->canEncode($offsetValue) ? $this->getLibrary()->encode($offsetValue) : $offsetValue;
        }

        return PackedArray::fromPHP(array_values($return));
    }

    private function getLibrary(): CodecLibrary
    {
        return $this->library ?? $this->library = new CodecLibrary(new LazyBSONDocumentCodec(), $this);
    }
}
