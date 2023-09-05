<?php

namespace MongoDB\Tests\Fixtures\Codec;

use DateTimeImmutable;
use MongoDB\BSON\Document;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Codec\DecodeIfSupported;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\EncodeIfSupported;
use MongoDB\Exception\UnsupportedValueException;
use MongoDB\Tests\Fixtures\Document\TestFile;

final class TestFileCodec implements DocumentCodec
{
    use DecodeIfSupported;
    use EncodeIfSupported;

    public function canDecode($value): bool
    {
        return $value instanceof Document;
    }

    public function decode($value): TestFile
    {
        if (! $value instanceof Document) {
            throw UnsupportedValueException::invalidDecodableValue($value);
        }

        $fileObject = new TestFile();
        $fileObject->id = $value->get('_id');
        $fileObject->length = (int) $value->get('length');
        $fileObject->chunkSize = (int) $value->get('chunkSize');
        $fileObject->uploadDate = DateTimeImmutable::createFromMutable($value->get('uploadDate')->toDateTime());
        $fileObject->filename = $value->get('filename');

        if ($value->has('metadata')) {
            $fileObject->metadata = $value->get('metadata');
        }

        return $fileObject;
    }

    public function canEncode($value): bool
    {
        return $value instanceof TestFile;
    }

    public function encode($value): Document
    {
        if (! $value instanceof TestFile) {
            throw UnsupportedValueException::invalidEncodableValue($value);
        }

        return Document::fromPHP([
            '_id' => $value->id,
            'length' => $value->length,
            'chunkSize' => $value->chunkSize,
            'uploadDate' => new UTCDateTime($value->uploadDate),
            'filename' => $value->filename,
        ]);
    }
}
