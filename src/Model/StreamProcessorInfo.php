<?php
/*
 * Copyright 2026-present MongoDB, Inc.
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

use ArrayAccess;
use MongoDB\BSON\UTCDateTimeInterface;
use MongoDB\Exception\BadMethodCallException;

use function array_key_exists;

/**
 * Stream processor info model.
 *
 * Models the response of the getStreamProcessor command. Fields that the spec
 * marks as Optional may be absent depending on server version; getters return
 * null in that case.
 *
 * @template-implements ArrayAccess<string, mixed>
 */
final class StreamProcessorInfo implements ArrayAccess
{
    public function __construct(private array $info)
    {
    }

    /** @see https://php.net/oop5.magic#language.oop5.magic.debuginfo */
    public function __debugInfo(): array
    {
        return $this->info;
    }

    /**
     * Processor id. Optional: not returned by all server versions.
     */
    public function getId(): ?string
    {
        return isset($this->info['id']) ? (string) $this->info['id'] : null;
    }

    public function getName(): string
    {
        return (string) $this->info['name'];
    }

    public function getState(): string
    {
        return (string) $this->info['state'];
    }

    public function getPipeline(): array
    {
        return (array) ($this->info['pipeline'] ?? []);
    }

    /**
     * Pipeline version. Optional: not returned by all server versions.
     */
    public function getPipelineVersion(): ?int
    {
        return isset($this->info['pipelineVersion']) ? (int) $this->info['pipelineVersion'] : null;
    }

    public function getTier(): ?string
    {
        return isset($this->info['tier']) ? (string) $this->info['tier'] : null;
    }

    public function getDlq(): mixed
    {
        return $this->info['dlq'] ?? null;
    }

    public function getStreamMetaFieldName(): ?string
    {
        return isset($this->info['streamMetaFieldName']) ? (string) $this->info['streamMetaFieldName'] : null;
    }

    public function isAutoScalingEnabled(): bool
    {
        return (bool) ($this->info['enableAutoScaling'] ?? false);
    }

    public function isFailoverEnabled(): bool
    {
        return (bool) ($this->info['failoverEnabled'] ?? false);
    }

    public function getActiveRegion(): ?string
    {
        return isset($this->info['activeRegion']) ? (string) $this->info['activeRegion'] : null;
    }

    public function getWorkspaceDefaultRegion(): ?string
    {
        return isset($this->info['workspaceDefaultRegion']) ? (string) $this->info['workspaceDefaultRegion'] : null;
    }

    public function getLastStateChange(): ?UTCDateTimeInterface
    {
        /** @psalm-suppress MixedAssignment */
        $value = $this->info['lastStateChange'] ?? null;

        return $value instanceof UTCDateTimeInterface ? $value : null;
    }

    public function getLastModifiedAt(): ?UTCDateTimeInterface
    {
        /** @psalm-suppress MixedAssignment */
        $value = $this->info['lastModifiedAt'] ?? null;

        return $value instanceof UTCDateTimeInterface ? $value : null;
    }

    public function getModifiedBy(): ?string
    {
        return isset($this->info['modifiedBy']) ? (string) $this->info['modifiedBy'] : null;
    }

    public function hasStarted(): bool
    {
        return (bool) ($this->info['hasStarted'] ?? false);
    }

    /**
     * Error message. Per spec this is always present; an empty string indicates
     * no error has occurred.
     */
    public function getErrorMsg(): string
    {
        return (string) ($this->info['errorMsg'] ?? '');
    }

    public function isErrorRetryable(): bool
    {
        return (bool) ($this->info['errorRetryable'] ?? false);
    }

    public function getErrorCode(): ?int
    {
        return isset($this->info['errorCode']) ? (int) $this->info['errorCode'] : null;
    }

    /** @see https://php.net/arrayaccess.offsetexists */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->info);
    }

    /** @see https://php.net/arrayaccess.offsetget */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->info[$offset];
    }

    /** @see https://php.net/arrayaccess.offsetset */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw BadMethodCallException::classIsImmutable(self::class);
    }

    /** @see https://php.net/arrayaccess.offsetunset */
    public function offsetUnset(mixed $offset): void
    {
        throw BadMethodCallException::classIsImmutable(self::class);
    }
}
