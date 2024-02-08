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
     * AND Queries With Multiple Expressions Specifying the Same Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/and/#and-queries-with-multiple-expressions-specifying-the-same-field
     */
    case AndANDQueriesWithMultipleExpressionsSpecifyingTheSameField = <<<'JSON'
    [
        {
            "$match": {
                "$and": [
                    {
                        "price": {
                            "$ne": {
                                "$numberDouble": "1.9899999999999999911"
                            }
                        }
                    },
                    {
                        "price": {
                            "$exists": true
                        }
                    }
                ]
            }
        }
    ]
    JSON;

    /**
     * AND Queries With Multiple Expressions Specifying the Same Operator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/and/#and-queries-with-multiple-expressions-specifying-the-same-operator
     */
    case AndANDQueriesWithMultipleExpressionsSpecifyingTheSameOperator = <<<'JSON'
    [
        {
            "$match": {
                "$and": [
                    {
                        "$or": [
                            {
                                "qty": {
                                    "$lt": {
                                        "$numberInt": "10"
                                    }
                                }
                            },
                            {
                                "qty": {
                                    "$gt": {
                                        "$numberInt": "50"
                                    }
                                }
                            }
                        ]
                    },
                    {
                        "$or": [
                            {
                                "sale": true
                            },
                            {
                                "price": {
                                    "$lt": {
                                        "$numberInt": "5"
                                    }
                                }
                            }
                        ]
                    }
                ]
            }
        }
    ]
    JSON;

    /**
     * Bit Position Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllClear/#bit-position-array
     */
    case BitsAllClearBitPositionArray = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAllClear": [
                        {
                            "$numberInt": "1"
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
     * Integer Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllClear/#integer-bitmask
     */
    case BitsAllClearIntegerBitmask = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAllClear": {
                        "$numberInt": "35"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * BinData Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllClear/#bindata-bitmask
     */
    case BitsAllClearBinDataBitmask = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAllClear": {
                        "$binary": {
                            "base64": "IA==",
                            "subType": "00"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Bit Position Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllSet/#bit-position-array
     */
    case BitsAllSetBitPositionArray = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAllSet": [
                        {
                            "$numberInt": "1"
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
     * Integer Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllSet/#integer-bitmask
     */
    case BitsAllSetIntegerBitmask = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAllSet": {
                        "$numberInt": "50"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * BinData Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllSet/#bindata-bitmask
     */
    case BitsAllSetBinDataBitmask = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAllSet": {
                        "$binary": {
                            "base64": "MA==",
                            "subType": "00"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Bit Position Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnyClear/#bit-position-array
     */
    case BitsAnyClearBitPositionArray = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAnyClear": [
                        {
                            "$numberInt": "1"
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
     * Integer Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnyClear/#integer-bitmask
     */
    case BitsAnyClearIntegerBitmask = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAnyClear": {
                        "$numberInt": "35"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * BinData Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnyClear/#bindata-bitmask
     */
    case BitsAnyClearBinDataBitmask = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAnyClear": {
                        "$binary": {
                            "base64": "MA==",
                            "subType": "00"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Bit Position Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnySet/#bit-position-array
     */
    case BitsAnySetBitPositionArray = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAnySet": [
                        {
                            "$numberInt": "1"
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
     * Integer Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnySet/#integer-bitmask
     */
    case BitsAnySetIntegerBitmask = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAnySet": {
                        "$numberInt": "35"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * BinData Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnySet/#bindata-bitmask
     */
    case BitsAnySetBinDataBitmask = <<<'JSON'
    [
        {
            "$match": {
                "a": {
                    "$bitsAnySet": {
                        "$binary": {
                            "base64": "MA==",
                            "subType": "00"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Attach a Comment to an Aggregation Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/comment/#attach-a-comment-to-an-aggregation-expression
     */
    case CommentAttachACommentToAnAggregationExpression = <<<'JSON'
    [
        {
            "$match": {
                "x": {
                    "$gt": {
                        "$numberInt": "0"
                    }
                },
                "$comment": "Don't allow negative inputs."
            }
        },
        {
            "$group": {
                "_id": {
                    "$mod": [
                        "$x",
                        {
                            "$numberInt": "2"
                        }
                    ]
                },
                "total": {
                    "$sum": "$x"
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
                        "pattern": "^MongoDB",
                        "options": ""
                    }
                }
            }
        },
        {
            "$match": {
                "company": {
                    "$eq": {
                        "$regularExpression": {
                            "pattern": "^MongoDB",
                            "options": ""
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Exists and Not Equal To
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/exists/#exists-and-not-equal-to
     */
    case ExistsExistsAndNotEqualTo = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$exists": true,
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
     * Null Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/exists/#null-values
     */
    case ExistsNullValues = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$exists": true
                }
            }
        }
    ]
    JSON;

    /** Missing Field */
    case ExistsMissingField = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$exists": false
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
     * Intersects a Polygon
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoIntersects/#intersects-a-polygon
     */
    case GeoIntersectsIntersectsAPolygon = <<<'JSON'
    [
        {
            "$match": {
                "loc": {
                    "$geoIntersects": {
                        "$geometry": {
                            "type": "Polygon",
                            "coordinates": [
                                [
                                    [
                                        {
                                            "$numberInt": "0"
                                        },
                                        {
                                            "$numberInt": "0"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "3"
                                        },
                                        {
                                            "$numberInt": "6"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "6"
                                        },
                                        {
                                            "$numberInt": "1"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "0"
                                        },
                                        {
                                            "$numberInt": "0"
                                        }
                                    ]
                                ]
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Intersects a Big Polygon
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoIntersects/#intersects-a--big--polygon
     */
    case GeoIntersectsIntersectsABigPolygon = <<<'JSON'
    [
        {
            "$match": {
                "loc": {
                    "$geoIntersects": {
                        "$geometry": {
                            "type": "Polygon",
                            "coordinates": [
                                [
                                    [
                                        {
                                            "$numberInt": "-100"
                                        },
                                        {
                                            "$numberInt": "60"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "-100"
                                        },
                                        {
                                            "$numberInt": "0"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "-100"
                                        },
                                        {
                                            "$numberInt": "-60"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "100"
                                        },
                                        {
                                            "$numberInt": "-60"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "100"
                                        },
                                        {
                                            "$numberInt": "60"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "-100"
                                        },
                                        {
                                            "$numberInt": "60"
                                        }
                                    ]
                                ]
                            ],
                            "crs": {
                                "type": "name",
                                "properties": {
                                    "name": "urn:x-mongodb:crs:strictwinding:EPSG:4326"
                                }
                            }
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Within a Polygon
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/#within-a-polygon
     */
    case GeoWithinWithinAPolygon = <<<'JSON'
    [
        {
            "$match": {
                "loc": {
                    "$geoWithin": {
                        "$geometry": {
                            "type": "Polygon",
                            "coordinates": [
                                [
                                    [
                                        {
                                            "$numberInt": "0"
                                        },
                                        {
                                            "$numberInt": "0"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "3"
                                        },
                                        {
                                            "$numberInt": "6"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "6"
                                        },
                                        {
                                            "$numberInt": "1"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "0"
                                        },
                                        {
                                            "$numberInt": "0"
                                        }
                                    ]
                                ]
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Within a Big Polygon
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/#within-a--big--polygon
     */
    case GeoWithinWithinABigPolygon = <<<'JSON'
    [
        {
            "$match": {
                "loc": {
                    "$geoWithin": {
                        "$geometry": {
                            "type": "Polygon",
                            "coordinates": [
                                [
                                    [
                                        {
                                            "$numberInt": "-100"
                                        },
                                        {
                                            "$numberInt": "60"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "-100"
                                        },
                                        {
                                            "$numberInt": "0"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "-100"
                                        },
                                        {
                                            "$numberInt": "-60"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "100"
                                        },
                                        {
                                            "$numberInt": "-60"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "100"
                                        },
                                        {
                                            "$numberInt": "60"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberInt": "-100"
                                        },
                                        {
                                            "$numberInt": "60"
                                        }
                                    ]
                                ]
                            ],
                            "crs": {
                                "type": "name",
                                "properties": {
                                    "name": "urn:x-mongodb:crs:strictwinding:EPSG:4326"
                                }
                            }
                        }
                    }
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
                                "pattern": "^be",
                                "options": ""
                            }
                        },
                        {
                            "$regularExpression": {
                                "pattern": "^st",
                                "options": ""
                            }
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/jsonSchema/#syntax
     */
    case JsonSchemaExample = <<<'JSON'
    [
        {
            "$match": {
                "$jsonSchema": {
                    "required": [
                        "name",
                        "major",
                        "gpa",
                        "address"
                    ],
                    "properties": {
                        "name": {
                            "bsonType": "string",
                            "description": "must be a string and is required"
                        },
                        "address": {
                            "bsonType": "object",
                            "required": [
                                "zipcode"
                            ],
                            "properties": {
                                "street": {
                                    "bsonType": "string"
                                },
                                "zipcode": {
                                    "bsonType": "string"
                                }
                            }
                        }
                    }
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
     * Use $mod to Select Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/#use--mod-to-select-documents
     */
    case ModUseModToSelectDocuments = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$mod": [
                        {
                            "$numberInt": "4"
                        },
                        {
                            "$numberInt": "0"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Floating Point Arguments
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/#floating-point-arguments
     */
    case ModFloatingPointArguments = <<<'JSON'
    [
        {
            "$match": {
                "qty": {
                    "$mod": [
                        {
                            "$numberDouble": "4.0"
                        },
                        {
                            "$numberInt": "0"
                        }
                    ]
                }
            }
        },
        {
            "$match": {
                "qty": {
                    "$mod": [
                        {
                            "$numberDouble": "4.5"
                        },
                        {
                            "$numberInt": "0"
                        }
                    ]
                }
            }
        },
        {
            "$match": {
                "qty": {
                    "$mod": [
                        {
                            "$numberDouble": "4.9900000000000002132"
                        },
                        {
                            "$numberInt": "0"
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
     * Query on GeoJSON Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/near/#query-on-geojson-data
     */
    case NearQueryOnGeoJSONData = <<<'JSON'
    [
        {
            "$match": {
                "location": {
                    "$near": {
                        "$geometry": {
                            "type": "Point",
                            "coordinates": [
                                {
                                    "$numberDouble": "-73.966700000000003001"
                                },
                                {
                                    "$numberDouble": "40.780000000000001137"
                                }
                            ]
                        },
                        "$minDistance": {
                            "$numberInt": "1000"
                        },
                        "$maxDistance": {
                            "$numberInt": "5000"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Specify Center Point Using GeoJSON
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nearSphere/#specify-center-point-using-geojson
     */
    case NearSphereSpecifyCenterPointUsingGeoJSON = <<<'JSON'
    [
        {
            "$match": {
                "location": {
                    "$nearSphere": {
                        "$geometry": {
                            "type": "Point",
                            "coordinates": [
                                {
                                    "$numberDouble": "-73.966700000000003001"
                                },
                                {
                                    "$numberDouble": "40.780000000000001137"
                                }
                            ]
                        },
                        "$minDistance": {
                            "$numberInt": "1000"
                        },
                        "$maxDistance": {
                            "$numberInt": "5000"
                        }
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
     * Query with Two Expressions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nor/#-nor-query-with-two-expressions
     */
    case NorQueryWithTwoExpressions = <<<'JSON'
    [
        {
            "$match": {
                "$nor": [
                    {
                        "price": {
                            "$numberDouble": "1.9899999999999999911"
                        }
                    },
                    {
                        "sale": true
                    }
                ]
            }
        }
    ]
    JSON;

    /**
     * Additional Comparisons
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nor/#-nor-and-additional-comparisons
     */
    case NorAdditionalComparisons = <<<'JSON'
    [
        {
            "$match": {
                "$nor": [
                    {
                        "price": {
                            "$numberDouble": "1.9899999999999999911"
                        }
                    },
                    {
                        "qty": {
                            "$lt": {
                                "$numberInt": "20"
                            }
                        }
                    },
                    {
                        "sale": true
                    }
                ]
            }
        }
    ]
    JSON;

    /**
     * $nor and $exists
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nor/#-nor-and--exists
     */
    case NorNorAndExists = <<<'JSON'
    [
        {
            "$match": {
                "$nor": [
                    {
                        "price": {
                            "$numberDouble": "1.9899999999999999911"
                        }
                    },
                    {
                        "price": {
                            "$exists": false
                        }
                    },
                    {
                        "sale": true
                    },
                    {
                        "sale": {
                            "$exists": false
                        }
                    }
                ]
            }
        }
    ]
    JSON;

    /**
     * Syntax
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/#syntax
     */
    case NotSyntax = <<<'JSON'
    [
        {
            "$match": {
                "price": {
                    "$not": {
                        "$gt": {
                            "$numberDouble": "1.9899999999999999911"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Regular Expressions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/#-not-and-regular-expressions
     */
    case NotRegularExpressions = <<<'JSON'
    [
        {
            "$match": {
                "price": {
                    "$not": {
                        "$regularExpression": {
                            "pattern": "^p.*",
                            "options": ""
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * $or Clauses
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/or/#-or-clauses-and-indexes
     */
    case OrOrClauses = <<<'JSON'
    [
        {
            "$match": {
                "$or": [
                    {
                        "quantity": {
                            "$lt": {
                                "$numberInt": "20"
                            }
                        }
                    },
                    {
                        "price": {
                            "$numberInt": "10"
                        }
                    }
                ]
            }
        }
    ]
    JSON;

    /**
     * Error Handling
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/or/#error-handling
     */
    case OrErrorHandling = <<<'JSON'
    [
        {
            "$match": {
                "$or": [
                    {
                        "x": {
                            "$eq": {
                                "$numberInt": "0"
                            }
                        }
                    },
                    {
                        "$expr": {
                            "$eq": [
                                {
                                    "$divide": [
                                        {
                                            "$numberInt": "1"
                                        },
                                        "$x"
                                    ]
                                },
                                {
                                    "$numberInt": "3"
                                }
                            ]
                        }
                    }
                ]
            }
        }
    ]
    JSON;

    /**
     * Select Random Items From a Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/rand/#select-random-items-from-a-collection
     */
    case RandSelectRandomItemsFromACollection = <<<'JSON'
    [
        {
            "$match": {
                "district": {
                    "$numberInt": "3"
                },
                "$expr": {
                    "$lt": [
                        {
                            "$numberDouble": "0.5"
                        },
                        {
                            "$rand": {}
                        }
                    ]
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "name": {
                    "$numberInt": "1"
                },
                "registered": {
                    "$numberInt": "1"
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

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sampleRate/#examples
     */
    case SampleRateExample = <<<'JSON'
    [
        {
            "$match": {
                "$sampleRate": {
                    "$numberDouble": "0.33000000000000001554"
                }
            }
        },
        {
            "$count": "numMatches"
        }
    ]
    JSON;

    /**
     * Query an Array by Array Length
     *
     * @see https://www.mongodb.com/docs/manual/tutorial/query-arrays/#query-an-array-by-array-length
     */
    case SizeQueryAnArrayByArrayLength = <<<'JSON'
    [
        {
            "$match": {
                "tags": {
                    "$size": {
                        "$numberInt": "3"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Search for a Single Word
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#search-for-a-single-word
     */
    case TextSearchForASingleWord = <<<'JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "coffee"
                }
            }
        }
    ]
    JSON;

    /**
     * Match Any of the Search Terms
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#search-for-a-single-word
     */
    case TextMatchAnyOfTheSearchTerms = <<<'JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "bake coffee cake"
                }
            }
        }
    ]
    JSON;

    /**
     * Search a Different Language
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#search-a-different-language
     */
    case TextSearchADifferentLanguage = <<<'JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "leche",
                    "$language": "es"
                }
            }
        }
    ]
    JSON;

    /**
     * Case and Diacritic Insensitive Search
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#case-and-diacritic-insensitive-search
     */
    case TextCaseAndDiacriticInsensitiveSearch = <<<'JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "\u0441\u044b\u0301\u0440\u043d\u0438\u043a\u0438 CAF\u00c9S"
                }
            }
        }
    ]
    JSON;

    /**
     * Perform Case Sensitive Search
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#perform-case-sensitive-search
     */
    case TextPerformCaseSensitiveSearch = <<<'JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "Coffee",
                    "$caseSensitive": true
                }
            }
        },
        {
            "$match": {
                "$text": {
                    "$search": "\\\"Caf\u00e9 Con Leche\\\"",
                    "$caseSensitive": true
                }
            }
        }
    ]
    JSON;

    /**
     * Diacritic Sensitive Search
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#perform-case-sensitive-search
     */
    case TextDiacriticSensitiveSearch = <<<'JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "CAF\u00c9",
                    "$diacriticSensitive": true
                }
            }
        }
    ]
    JSON;

    /**
     * Text Search Score Examples
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#perform-case-sensitive-search
     */
    case TextTextSearchScoreExamples = <<<'JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "CAF\u00c9",
                    "$diacriticSensitive": true
                }
            }
        },
        {
            "$project": {
                "score": {
                    "$meta": "textScore"
                }
            }
        },
        {
            "$sort": {
                "score": {
                    "$meta": "textScore"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "5"
            }
        }
    ]
    JSON;

    /**
     * Querying by Data Type
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/#querying-by-data-type
     */
    case TypeQueryingByDataType = <<<'JSON'
    [
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        {
                            "$numberInt": "2"
                        }
                    ]
                }
            }
        },
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        "string"
                    ]
                }
            }
        },
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        {
                            "$numberInt": "1"
                        }
                    ]
                }
            }
        },
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        "double"
                    ]
                }
            }
        },
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        "number"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Querying by Multiple Data Type
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/#querying-by-multiple-data-type
     */
    case TypeQueryingByMultipleDataType = <<<'JSON'
    [
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        {
                            "$numberInt": "2"
                        },
                        {
                            "$numberInt": "1"
                        }
                    ]
                }
            }
        },
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        "string",
                        "double"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Querying by MinKey and MaxKey
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/#querying-by-minkey-and-maxkey
     */
    case TypeQueryingByMinKeyAndMaxKey = <<<'JSON'
    [
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        "minKey"
                    ]
                }
            }
        },
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        "maxKey"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Querying by Array Type
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/#querying-by-array-type
     */
    case TypeQueryingByArrayType = <<<'JSON'
    [
        {
            "$match": {
                "zipCode": {
                    "$type": [
                        "array"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/where/#example
     */
    case WhereExample = <<<'JSON'
    [
        {
            "$match": {
                "$where": {
                    "$code": "function() {\n    return hex_md5(this.name) == \"9b53e667f30cd329dca1ec9e6a83e994\"\n}"
                }
            }
        },
        {
            "$match": {
                "$expr": {
                    "$function": {
                        "body": {
                            "$code": "function(name) {\n    return hex_md5(name) == \"9b53e667f30cd329dca1ec9e6a83e994\";\n}"
                        },
                        "args": [
                            "$name"
                        ],
                        "lang": "js"
                    }
                }
            }
        }
    ]
    JSON;
}
