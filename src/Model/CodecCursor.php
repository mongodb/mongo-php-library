<?php
/*
 * Copyright 2023-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Model;

use Iterator;
use MongoDB\BSON\Document;
use MongoDB\BSON\Int64;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\CursorId;
use MongoDB\Driver\CursorInterface;
use MongoDB\Driver\Server;
use ReturnTypeWillChange;

use function assert;
use function iterator_to_array;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;
use const E_USER_WARNING;

/**
 * @template TValue of object
 * @template-implements CursorInterface<int, TValue>
 * @template-implements Iterator<int, TValue>
 */
class CodecCursor implements CursorInterface, Iterator
{
    private const TYPEMAP = ['root' => 'bson'];

    private Cursor $cursor;

    /** @var DocumentCodec<TValue> */
    private DocumentCodec $codec;

    /** @var TValue|null */
    private ?object $current = null;

    /** @return TValue */
    public function current(): ?object
    {
        if (! $this->current && $this->valid()) {
            $value = $this->cursor->current();
            assert($value instanceof Document);
            $this->current = $this->codec->decode($value);
        }

        return $this->current;
    }

    /**
     * @template NativeClass of Object
     * @param DocumentCodec<NativeClass> $codec
     * @return self<NativeClass>
     */
    public static function fromCursor(Cursor $cursor, DocumentCodec $codec): self
    {
        $cursor->setTypeMap(self::TYPEMAP);

        return new self($cursor, $codec);
    }

    /**
     * @return CursorId|Int64
     * @psalm-return ($asInt64 is true ? Int64 : CursorId)
     */
    #[ReturnTypeWillChange]
    public function getId(bool $asInt64 = false)
    {
        if (! $asInt64) {
            @trigger_error(
                sprintf(
                    'The method "%s" will no longer return a "%s" instance in the future. Pass "true" as argument to change to the new behavior and receive a "%s" instance instead.',
                    __METHOD__,
                    CursorId::class,
                    Int64::class,
                ),
                E_USER_DEPRECATED,
            );
        }

        return $this->cursor->getId($asInt64);
    }

    public function getServer(): Server
    {
        return $this->cursor->getServer();
    }

    public function isDead(): bool
    {
        return $this->cursor->isDead();
    }

    public function key(): int
    {
        return $this->cursor->key();
    }

    public function next(): void
    {
        $this->current = null;
        $this->cursor->next();
    }

    public function rewind(): void
    {
        $this->current = null;
        $this->cursor->rewind();
    }

    public function setTypeMap(array $typemap): void
    {
        // Not supported
        trigger_error(sprintf('Discarding type map for %s', __METHOD__), E_USER_WARNING);
    }

    /** @return array<int, TValue> */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    public function valid(): bool
    {
        return $this->cursor->valid();
    }

    /** @param DocumentCodec<TValue> $codec */
    private function __construct(Cursor $cursor, DocumentCodec $codec)
    {
        $this->cursor = $cursor;
        $this->codec = $codec;
    }
}
