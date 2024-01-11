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
     * Equals a Specified Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/#equals-a-specified-value
     */
    case EqEqualsASpecifiedValue = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$eq": {
                        "$numberInt": "20"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Field in Embedded Document Equals a Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/#field-in-embedded-document-equals-a-value
     */
    case EqFieldInEmbeddedDocumentEqualsAValue = <<<'JSON'
    [
        {
            "$match": {
                "item.name": {
                    "$eq": "ab"
                }
            }
        }
    ]
    JSON;

    /**
     * Equals an Array Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/#equals-an-array-value
     */
    case EqEqualsAnArrayValue = <<<'JSON'
    [
        {
            "$match": {
                "tags": {
                    "$eq": [
                        "A",
                        "B"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Regex Match Behaviour
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/#regex-match-behaviour
     */
    case EqRegexMatchBehaviour = <<<'JSON'
    [
        {
            "$match": {
                "company": "MongoDB"
            }
        },
        {
            "$match": {
                "company": {
                    "$eq": "MongoDB"
                }
            }
        },
        {
            "$match": {
                "company": {
                    "$regularExpression": {
                        "options": "",
                        "pattern": "^MongoDB"
                    }
                }
            }
        },
        {
            "$match": {
                "company": {
                    "$eq": {
                        "$regularExpression": {
                            "options": "",
                            "pattern": "^MongoDB"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Compare Two Fields from A Single Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/expr/#compare-two-fields-from-a-single-document
     */
    case ExprCompareTwoFieldsFromASingleDocument = <<<'JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$gt": [
                        "$spent",
                        "$budget"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Using $expr With Conditional Statements
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/expr/#using--expr-with-conditional-statements
     */
    case ExprUsingExprWithConditionalStatements = <<<'JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$lt": [
                        {
                            "$cond": {
                                "if": {
                                    "$gte": [
                                        "$qty",
                                        {
                                            "$numberInt": "100"
                                        }
                                    ]
                                },
                                "then": {
                                    "$multiply": [
                                        "$price",
                                        {
                                            "$numberDouble": "0.5"
                                        }
                                    ]
                                },
                                "else": {
                                    "$multiply": [
                                        "$price",
                                        {
                                            "$numberDouble": "0.75"
                                        }
                                    ]
                                }
                            }
                        },
                        {
                            "$numberInt": "5"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/gt/#match-document-fields
     */
    case GtMatchDocumentFields = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$gt": {
                        "$numberInt": "20"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/gte/#match-document-fields
     */
    case GteMatchDocumentFields = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$gte": {
                        "$numberInt": "20"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Use the $in Operator to Match Values in an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/in/#use-the--in-operator-to-match-values
     */
    case InUseTheInOperatorToMatchValuesInAnArray = <<<'JSON'
    [
        {
            "$match": {
                "tags": {
                    "$in": [
                        "home",
                        "school"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Use the $in Operator with a Regular Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/in/#use-the--in-operator-with-a-regular-expression
     */
    case InUseTheInOperatorWithARegularExpression = <<<'JSON'
    [
        {
            "$match": {
                "tags": {
                    "$in": [
                        {
                            "$regularExpression": {
                                "options": "",
                                "pattern": "^be"
                            }
                        },
                        {
                            "$regularExpression": {
                                "options": "",
                                "pattern": "^st"
                            }
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/lt/#match-document-fields
     */
    case LtMatchDocumentFields = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$lt": {
                        "$numberInt": "20"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/lte/#match-document-fields
     */
    case LteMatchDocumentFields = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$lte": {
                        "$numberInt": "20"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/ne/#match-document-fields
     */
    case NeMatchDocumentFields = <<<'JSON'
    [
        {
            "$match": {
                "quantity": {
                    "$ne": {
                        "$numberInt": "20"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Select on Unmatching Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nin/#select-on-unmatching-documents
     */
    case NinSelectOnUnmatchingDocuments = <<<'JSON'
    [
        {
            "$match": {
                "quantity": {
                    "$nin": [
                        {
                            "$numberInt": "5"
                        },
                        {
                            "$numberInt": "15"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Select on Elements Not in an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nin/#select-on-elements-not-in-an-array
     */
    case NinSelectOnElementsNotInAnArray = <<<'JSON'
    [
        {
            "$match": {
                "tags": {
                    "$nin": [
                        "school"
                    ]
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
