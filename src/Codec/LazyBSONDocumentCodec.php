<?php

namespace MongoDB\Codec;

use MongoDB\BSON\Document;
use MongoDB\Exception\UnexpectedValueException;
use MongoDB\Model\LazyBSONDocument;

use function sprintf;

/**
 * Codec for lazy decoding of BSON Document instances
 *
 * @template-implements Codec<Document, LazyBSONDocument>
 */
class LazyBSONDocumentCodec implements Codec, KnowsCodecLibrary
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
        return $value instanceof Document;
    }

    /** @inheritDoc */
    public function canEncode($value): bool
    {
        return $value instanceof LazyBSONDocument;
    }

    /** @inheritDoc */
    public function decode($value): LazyBSONDocument
    {
        if (! $value instanceof Document) {
            throw new UnexpectedValueException(sprintf('"%s" can only decode from "%s" instances', self::class, Document::class));
        }

        return new LazyBSONDocument($value, $this->getLibrary());
    }

    /** @inheritDoc */
    public function encode($value): Document
    {
        if (! $value instanceof LazyBSONDocument) {
            throw new UnexpectedValueException(sprintf('"%s" can only encode "%s" instances', self::class, LazyBSONDocument::class));
        }

        $return = [];
        foreach ($value as $field => $fieldValue) {
            $return[$field] = $this->getLibrary()->canEncode($fieldValue) ? $this->getLibrary()->encode($fieldValue) : $fieldValue;
        }

        return Document::fromPHP($return);
    }

    private function getLibrary(): CodecLibrary
    {
        return $this->library ?? $this->library = new CodecLibrary($this, new LazyBSONArrayCodec());
    }
}
