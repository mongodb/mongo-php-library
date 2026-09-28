<?php
/*
 * Copyright 2015-present MongoDB, Inc.
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

use MongoDB\Driver\Exception\InvalidArgumentException as DriverInvalidArgumentException;

use function array_pop;
use function assert;
use function count;
use function get_debug_type;
use function implode;
use function is_array;
use function sprintf;

class InvalidArgumentException extends DriverInvalidArgumentException implements Exception
{
    /** @internal */
    public static function cannotCombineCodecAndTypeMap(): self
    {
        return new self('Cannot provide both "codec" and "typeMap" options');
    }

    /**
     * Thrown when an argument or option is expected to be a string or a document.
     *
     * @internal
     *
     * @param string $name  Name of the argument or option
     * @param mixed  $value Actual value (used to derive the type)
     */
    public static function expectedDocumentOrStringType(string $name, mixed $value): self
    {
        return new self(sprintf('Expected %s to have type "string" or "document" (array or object) but found "%s"', $name, get_debug_type($value)));
    }

    /**
     * Thrown when an argument or option is expected to be a document.
     *
     * @param string $name  Name of the argument or option
     * @param mixed  $value Actual value (used to derive the type)
     * @internal
     */
    public static function expectedDocumentType(string $name, mixed $value): self
    {
        return new self(sprintf('Expected %s to have type "document" (array or object) but found "%s"', $name, get_debug_type($value)));
    }

    /**
     * Thrown when an argument or option has an invalid type.
     *
     * @param string              $name         Name of the argument or option
     * @param mixed               $value        Actual value (used to derive the type)
     * @param string|list<string> $expectedType Expected type as a string or an array containing one or more strings
     * @internal
     */
    public static function invalidType(string $name, mixed $value, string|array $expectedType): self
    {
        if (is_array($expectedType)) {
            $expectedType = self::expectedTypesToString($expectedType);
        }

        return new self(sprintf('Expected %s to have type "%s" but found "%s"', $name, $expectedType, get_debug_type($value)));
    }

    /** @param list<string> $types */
    private static function expectedTypesToString(array $types): string
    {
        assert(count($types) > 0);

        if (count($types) < 3) {
            return implode('" or "', $types);
        }

        $lastType = array_pop($types);

        return sprintf('%s", or "%s', implode('", "', $types), $lastType);
    }
}
