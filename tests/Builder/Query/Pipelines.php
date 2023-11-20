<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Query;

enum Pipelines: string
{
    /**
     * Use $all to Match Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/all/#use--all-to-match-values
     */
    case AllUseAllToMatchValues = <<<'JSON'
    [
        {
            "$match": {
                "tags": {
                    "$all": [
                        "appliance",
                        "school",
                        "book"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Use $all with $elemMatch
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/all/#use--all-with--elemmatch
     */
    case AllUseAllWithElemMatch = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$all": [
                        {
                            "$elemMatch": {
                                "size": "M",
                                "num": {
                                    "$gt": {
                                        "$numberInt": "50"
                                    }
                                }
                            }
                        },
                        {
                            "$elemMatch": {
                                "num": {
                                    "$numberInt": "100"
                                },
                                "color": "green"
                            }
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Element Match
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/#element-match
     */
    case ElemMatchElementMatch = <<<'JSON'
    [
        {
            "$match": {
                "results": {
                    "$elemMatch": {
                        "$gte": {
                            "$numberInt": "80"
                        },
                        "$lt": {
                            "$numberInt": "85"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Array of Embedded Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/#array-of-embedded-documents
     */
    case ElemMatchArrayOfEmbeddedDocuments = <<<'JSON'
    [
        {
            "$match": {
                "results": {
                    "$elemMatch": {
                        "product": "xyz",
                        "score": {
                            "$gte": {
                                "$numberInt": "8"
                            }
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Single Query Condition
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/#single-query-condition
     */
    case ElemMatchSingleQueryCondition = <<<'JSON'
    [
        {
            "$match": {
                "results": {
                    "$elemMatch": {
                        "product": {
                            "$ne": "xyz"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /** Using $or with $elemMatch */
    case ElemMatchUsingOrWithElemMatch = <<<'JSON'
    [
        {
            "$match": {
                "game": {
                    "$elemMatch": {
                        "$or": [
                            {
                                "score": {
                                    "$gt": {
                                        "$numberInt": "10"
                                    }
                                }
                            },
                            {
                                "score": {
                                    "$lt": {
                                        "$numberInt": "5"
                                    }
                                }
                            }
                        ]
                    }
                }
            }
        }
    ]
    JSON;

    /** Single field operator */
    case ElemMatchSingleFieldOperator = <<<'JSON'
    [
        {
            "$match": {
                "results": {
                    "$elemMatch": {
                        "$gt": {
                            "$numberInt": "10"
                        }
                    }
                }
            }
        }
    ]
    JSON;

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
                    "$regex": {
                        "$regularExpression": {
                            "pattern": "789$",
                            "options": ""
                        }
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
                    "$regex": {
                        "$regularExpression": {
                            "pattern": "^ABC",
                            "options": "i"
                        }
                    }
                }
            }
        }
    ]
    JSON;
}
