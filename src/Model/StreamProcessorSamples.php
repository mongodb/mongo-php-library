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

/**
 * Result of a call to MongoDB\StreamProcessor::getStreamProcessorSamples().
 *
 * Callers MUST check the cursorId: a value of 0 indicates the cursor is
 * exhausted and no further calls should be made.
 */
final class StreamProcessorSamples
{
    /**
     * @param int   $cursorId  Cursor id to pass to the next call (0 when exhausted).
     * @param array $documents Sampled documents returned by this call.
     */
    public function __construct(private int $cursorId, private array $documents)
    {
    }

    public function getCursorId(): int
    {
        return $this->cursorId;
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function isExhausted(): bool
    {
        return $this->cursorId === 0;
    }
}
