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

namespace MongoDB\Codec;

/**
 * @internal
 * @psalm-template BSONType
 * @psalm-template NativeType
 */
interface Encoder
{
    /**
     * @param mixed $value
     * @psalm-assert-if-true NativeType $value
     */
    public function canEncode($value): bool;

    /**
     * @param mixed $value
     * @psalm-param NativeType $value
     * @return mixed
     * @psalm-return BSONType
     */
    public function encode($value);

    /**
     * @param mixed $value
     * @psalm-param mixed $value
     * @return mixed
     * @psalm-return ($value is NativeType ? BSONType : $value)
     */
    public function encodeIfSupported($value);
}
