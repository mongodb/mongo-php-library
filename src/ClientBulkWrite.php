<?php
/*
 * Copyright 2025-present MongoDB, Inc.
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

namespace MongoDB;

use MongoDB\BSON\Document;
use MongoDB\BSON\PackedArray;
use MongoDB\Codec\DocumentCodec;
use MongoDB\Codec\Encoder;
use MongoDB\Driver\BulkWriteCommand;
use MongoDB\Driver\Manager;
use MongoDB\Exception\InvalidArgumentException;
use NoDiscard;
use stdClass;

use function is_array;
use function is_bool;
use function is_string;

final class ClientBulkWrite
{
    private function __construct(
        public readonly BulkWriteCommand $bulkWriteCommand,
        private readonly Manager $manager,
        private readonly string $namespace,
        /** @psalm-var Encoder<array|stdClass|Document|PackedArray, mixed> */
        private readonly Encoder $builderEncoder,
        private readonly ?DocumentCodec $codec,
    ) {
    }

    #[NoDiscard]
    public static function createWithCollection(Collection $collection, array $options = []): self
    {
        $options += ['ordered' => true];

        if (isset($options['bypassDocumentValidation']) && ! is_bool($options['bypassDocumentValidation'])) {
            throw InvalidArgumentException::invalidType('"bypassDocumentValidation" option', $options['bypassDocumentValidation'], 'boolean');
        }

        if (isset($options['let']) && ! is_document($options['let'])) {
            throw InvalidArgumentException::expectedDocumentType('"let" option', $options['let']);
        }

        if (! is_bool($options['ordered'])) {
            throw InvalidArgumentException::invalidType('"ordered" option', $options['ordered'], 'boolean');
        }

        if (isset($options['verboseResults']) && ! is_bool($options['verboseResults'])) {
            throw InvalidArgumentException::invalidType('"verboseResults" option', $options['verboseResults'], 'boolean');
        }

        return new self(
            new BulkWriteCommand($options),
            $collection->getManager(),
            $collection->getNamespace(),
            $collection->getBuilderEncoder(),
            $collection->getCodec(),
        );
    }

    public function deleteMany(array|object $filter, array $options = []): self
    {
        $filter = $this->builderEncoder->encodeIfSupported($filter);

        if (isset($options['collation']) && ! is_document($options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $options['collation']);
        }

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_document($options['hint'])) {
            throw InvalidArgumentException::expectedDocumentOrStringType('"hint" option', $options['hint']);
        }

        $this->bulkWriteCommand->deleteMany($this->namespace, $filter, $options);

        return $this;
    }

    public function deleteOne(array|object $filter, array $options = []): self
    {
        $filter = $this->builderEncoder->encodeIfSupported($filter);

        if (isset($options['collation']) && ! is_document($options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $options['collation']);
        }

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_document($options['hint'])) {
            throw InvalidArgumentException::expectedDocumentOrStringType('"hint" option', $options['hint']);
        }

        $this->bulkWriteCommand->deleteOne($this->namespace, $filter, $options);

        return $this;
    }

    public function insertOne(array|object $document, mixed &$id = null): self
    {
        if ($this->codec) {
            $document = $this->codec->encode($document);
        }

        // Capture the document's _id, which may have been generated, in an optional output variable
        /** @var mixed $id */
        $id = $this->bulkWriteCommand->insertOne($this->namespace, $document);

        return $this;
    }

    public function replaceOne(array|object $filter, array|object $replacement, array $options = []): self
    {
        $filter = $this->builderEncoder->encodeIfSupported($filter);

        if ($this->codec) {
            $replacement = $this->codec->encode($replacement);
        }

        // Treat empty arrays as replacement documents for BC
        if ($replacement === []) {
            $replacement = (object) $replacement;
        }

        if (is_first_key_operator($replacement)) {
            throw new InvalidArgumentException('First key in $replacement is an update operator');
        }

        if (is_pipeline($replacement, true)) {
            throw new InvalidArgumentException('$replacement is an update pipeline');
        }

        if (isset($options['collation']) && ! is_document($options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $options['collation']);
        }

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_document($options['hint'])) {
            throw InvalidArgumentException::expectedDocumentOrStringType('"hint" option', $options['hint']);
        }

        if (isset($options['sort']) && ! is_document($options['sort'])) {
            throw InvalidArgumentException::expectedDocumentType('"sort" option', $options['sort']);
        }

        if (isset($options['upsert']) && ! is_bool($options['upsert'])) {
            throw InvalidArgumentException::invalidType('"upsert" option', $options['upsert'], 'boolean');
        }

        $this->bulkWriteCommand->replaceOne($this->namespace, $filter, $replacement, $options);

        return $this;
    }

    public function updateMany(array|object $filter, array|object $update, array $options = []): self
    {
        $filter = $this->builderEncoder->encodeIfSupported($filter);
        $update = $this->builderEncoder->encodeIfSupported($update);

        if (! is_first_key_operator($update) && ! is_pipeline($update)) {
            throw new InvalidArgumentException('Expected update operator(s) or non-empty pipeline for $update');
        }

        if (isset($options['arrayFilters']) && ! is_array($options['arrayFilters'])) {
            throw InvalidArgumentException::invalidType('"arrayFilters" option', $options['arrayFilters'], 'array');
        }

        if (isset($options['collation']) && ! is_document($options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $options['collation']);
        }

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_document($options['hint'])) {
            throw InvalidArgumentException::expectedDocumentOrStringType('"hint" option', $options['hint']);
        }

        if (isset($options['upsert']) && ! is_bool($options['upsert'])) {
            throw InvalidArgumentException::invalidType('"upsert" option', $options['upsert'], 'boolean');
        }

        $this->bulkWriteCommand->updateMany($this->namespace, $filter, $update, $options);

        return $this;
    }

    public function updateOne(array|object $filter, array|object $update, array $options = []): self
    {
        $filter = $this->builderEncoder->encodeIfSupported($filter);
        $update = $this->builderEncoder->encodeIfSupported($update);

        if (! is_first_key_operator($update) && ! is_pipeline($update)) {
            throw new InvalidArgumentException('Expected update operator(s) or non-empty pipeline for $update');
        }

        if (isset($options['arrayFilters']) && ! is_array($options['arrayFilters'])) {
            throw InvalidArgumentException::invalidType('"arrayFilters" option', $options['arrayFilters'], 'array');
        }

        if (isset($options['collation']) && ! is_document($options['collation'])) {
            throw InvalidArgumentException::expectedDocumentType('"collation" option', $options['collation']);
        }

        if (isset($options['hint']) && ! is_string($options['hint']) && ! is_document($options['hint'])) {
            throw InvalidArgumentException::expectedDocumentOrStringType('"hint" option', $options['hint']);
        }

        if (isset($options['sort']) && ! is_document($options['sort'])) {
            throw InvalidArgumentException::expectedDocumentType('"sort" option', $options['sort']);
        }

        if (isset($options['upsert']) && ! is_bool($options['upsert'])) {
            throw InvalidArgumentException::invalidType('"upsert" option', $options['upsert'], 'boolean');
        }

        $this->bulkWriteCommand->updateOne($this->namespace, $filter, $update, $options);

        return $this;
    }

    #[NoDiscard]
    public function withCollection(Collection $collection): self
    {
        /* Prohibit mixing Collections associated with different Manager
         * objects. This is not technically necessary, since the Collection is
         * only used to derive a namespace and encoding options; however, it
         * may prevent a user from inadvertently mixing writes destined for
         * different deployments. */
        if ($this->manager !== $collection->getManager()) {
            throw new InvalidArgumentException('$collection is associated with a different MongoDB\Driver\Manager');
        }

        return new self(
            $this->bulkWriteCommand,
            $this->manager,
            $collection->getNamespace(),
            $collection->getBuilderEncoder(),
            $collection->getCodec(),
        );
    }
}
