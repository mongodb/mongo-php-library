<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

enum Pipelines: string
{
    /**
     * Perform a LIKE Match
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/regex/#perform-a-like-match
     */
    case RegexPerformALIKEMatch = <<<'JSON'
    [
        {
            "$match": {
                "sku": {
                    "$regularExpression": {
                        "pattern": "789$",
                        "options": ""
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Perform Case-Insensitive Regular Expression Match
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/regex/#perform-case-insensitive-regular-expression-match
     */
    case RegexPerformCaseInsensitiveRegularExpressionMatch = <<<'JSON'
    [
        {
            "$match": {
                "sku": {
                    "$regularExpression": {
                        "pattern": "^ABC",
                        "options": "i"
                    }
                }
            }
        }
    ]
    JSON;
}
