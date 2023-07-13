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

namespace MongoDB\Exception;

use InvalidArgumentException;

use function get_debug_type;
use function sprintf;

class UnsupportedValueException extends InvalidArgumentException implements Exception
{
    /** @var mixed */
    private $value;

    /** @return mixed */
    public function getValue()
    {
        return $this->value;
    }

    /** @param mixed $value */
    public static function invalidDecodableValue($value): self
    {
        return new self(sprintf('Could not decode value of type "%s".', get_debug_type($value)), $value);
    }

    /** @param mixed $value */
    public static function invalidEncodableValue($value): self
    {
        return new self(sprintf('Could not encode value of type "%s".', get_debug_type($value)), $value);
    }

    /** @param mixed $value */
    private function __construct(string $message, $value)
    {
        parent::__construct($message);

        $this->value = $value;
    }
}
