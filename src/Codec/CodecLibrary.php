<?php

namespace MongoDB\Codec;

use MongoDB\Exception\UnexpectedValueException;

use function array_map;
use function get_debug_type;
use function sprintf;

class CodecLibrary implements Codec
{
    /** @var array<Decoder> */
    private $decoders = [];

    /** @var array<Encoder> */
    private $encoders = [];

    /** @param Decoder|Encoder $items */
    public function __construct(...$items)
    {
        array_map(
            function ($item) {
                if ($item instanceof Decoder) {
                    $this->attachDecoder($item);
                }

                if ($item instanceof Encoder) {
                    $this->attachEncoder($item);
                }

                // Yes, we'll silently discard everything. Please let me already have union types...
            },
            $items
        );
    }

    /** @return static */
    final public function attachCodec(Codec $codec): self
    {
        $this->decoders[] = $codec;
        $this->encoders[] = $codec;
        if ($codec instanceof KnowsCodecLibrary) {
            $codec->attachLibrary($this);
        }

        return $this;
    }

    /** @return static */
    final public function attachDecoder(Decoder $decoder): self
    {
        $this->decoders[] = $decoder;
        if ($decoder instanceof KnowsCodecLibrary) {
            $decoder->attachLibrary($this);
        }

        return $this;
    }

    /** @return static */
    final public function attachEncoder(Encoder $encoder): self
    {
        $this->encoders[] = $encoder;
        if ($encoder instanceof KnowsCodecLibrary) {
            $encoder->attachLibrary($this);
        }

        return $this;
    }

    /** @param mixed $value */
    final public function canDecode($value): bool
    {
        foreach ($this->decoders as $decoder) {
            if ($decoder->canDecode($value)) {
                return true;
            }
        }

        return $value === null;
    }

    /** @param mixed $value */
    final public function canEncode($value): bool
    {
        foreach ($this->encoders as $encoder) {
            if ($encoder->canEncode($value)) {
                return true;
            }
        }

        return $value === null;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    final public function decode($value)
    {
        foreach ($this->decoders as $decoder) {
            if (! $decoder->canDecode($value)) {
                continue;
            }

            return $decoder->decode($value);
        }

        if ($value === null) {
            return null;
        }

        throw new UnexpectedValueException(sprintf('No decoder found for value of type "%s"', get_debug_type($value)));
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    final public function encode($value)
    {
        foreach ($this->encoders as $encoder) {
            if (! $encoder->canEncode($value)) {
                continue;
            }

            return $encoder->encode($value);
        }

        if ($value === null) {
            return null;
        }

        throw new UnexpectedValueException(sprintf('No encoder found for value of type "%s"', get_debug_type($value)));
    }
}
