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
    case AllUseAllToMatchValues = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use $all with $elemMatch
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/all/#use--all-with--elemmatch
     */
    case AllUseAllWithElemMatch = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * AND Queries With Multiple Expressions Specifying the Same Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/and/#and-queries-with-multiple-expressions-specifying-the-same-field
     */
    case AndANDQueriesWithMultipleExpressionsSpecifyingTheSameField = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * AND Queries With Multiple Expressions Specifying the Same Operator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/and/#and-queries-with-multiple-expressions-specifying-the-same-operator
     */
    case AndANDQueriesWithMultipleExpressionsSpecifyingTheSameOperator = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Bit Position Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllClear/#bit-position-array
     */
    case BitsAllClearBitPositionArray = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Integer Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllClear/#integer-bitmask
     */
    case BitsAllClearIntegerBitmask = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * BinData Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllClear/#bindata-bitmask
     */
    case BitsAllClearBinDataBitmask = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Bit Position Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllSet/#bit-position-array
     */
    case BitsAllSetBitPositionArray = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Integer Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllSet/#integer-bitmask
     */
    case BitsAllSetIntegerBitmask = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * BinData Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAllSet/#bindata-bitmask
     */
    case BitsAllSetBinDataBitmask = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Bit Position Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnyClear/#bit-position-array
     */
    case BitsAnyClearBitPositionArray = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Integer Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnyClear/#integer-bitmask
     */
    case BitsAnyClearIntegerBitmask = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * BinData Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnyClear/#bindata-bitmask
     */
    case BitsAnyClearBinDataBitmask = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Bit Position Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnySet/#bit-position-array
     */
    case BitsAnySetBitPositionArray = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Integer Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnySet/#integer-bitmask
     */
    case BitsAnySetIntegerBitmask = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * BinData Bitmask
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/bitsAnySet/#bindata-bitmask
     */
    case BitsAnySetBinDataBitmask = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Attach a Comment to an Aggregation Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/comment/#attach-a-comment-to-an-aggregation-expression
     */
    case CommentAttachACommentToAnAggregationExpression = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Element Match
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/#element-match
     */
    case ElemMatchElementMatch = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Array of Embedded Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/#array-of-embedded-documents
     */
    case ElemMatchArrayOfEmbeddedDocuments = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Single Query Condition
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/elemMatch/#single-query-condition
     */
    case ElemMatchSingleQueryCondition = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /** Using $or with $elemMatch */
    case ElemMatchUsingOrWithElemMatch = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /** Single field operator */
    case ElemMatchSingleFieldOperator = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Equals a Specified Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/#equals-a-specified-value
     */
    case EqEqualsASpecifiedValue = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Field in Embedded Document Equals a Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/#field-in-embedded-document-equals-a-value
     */
    case EqFieldInEmbeddedDocumentEqualsAValue = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "item.name": {
                    "$eq": "ab"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Equals an Array Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/#equals-an-array-value
     */
    case EqEqualsAnArrayValue = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Regex Match Behaviour
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/eq/#regex-match-behaviour
     */
    case EqRegexMatchBehaviour = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Exists and Not Equal To
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/exists/#exists-and-not-equal-to
     */
    case ExistsExistsAndNotEqualTo = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Null Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/exists/#null-values
     */
    case ExistsNullValues = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "qty": {
                    "$exists": true
                }
            }
        }
    ]
    EXTENDED_JSON;

    /** Missing Field */
    case ExistsMissingField = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "qty": {
                    "$exists": false
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Compare Two Fields from A Single Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/expr/#compare-two-fields-from-a-single-document
     */
    case ExprCompareTwoFieldsFromASingleDocument = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Using $expr With Conditional Statements
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/expr/#using--expr-with-conditional-statements
     */
    case ExprUsingExprWithConditionalStatements = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Intersects a Polygon
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoIntersects/#intersects-a-polygon
     */
    case GeoIntersectsIntersectsAPolygon = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Intersects a Big Polygon
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoIntersects/#intersects-a--big--polygon
     */
    case GeoIntersectsIntersectsABigPolygon = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Within a Polygon
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/#within-a-polygon
     */
    case GeoWithinWithinAPolygon = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Within a Big Polygon
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/geoWithin/#within-a--big--polygon
     */
    case GeoWithinWithinABigPolygon = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/gt/#match-document-fields
     */
    case GtMatchDocumentFields = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/gte/#match-document-fields
     */
    case GteMatchDocumentFields = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use the $in Operator to Match Values in an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/in/#use-the--in-operator-to-match-values
     */
    case InUseTheInOperatorToMatchValuesInAnArray = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use the $in Operator with a Regular Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/in/#use-the--in-operator-with-a-regular-expression
     */
    case InUseTheInOperatorWithARegularExpression = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/jsonSchema/#syntax
     */
    case JsonSchemaExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/lt/#match-document-fields
     */
    case LtMatchDocumentFields = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/lte/#match-document-fields
     */
    case LteMatchDocumentFields = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use $mod to Select Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/#use--mod-to-select-documents
     */
    case ModUseModToSelectDocuments = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Floating Point Arguments
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/mod/#floating-point-arguments
     */
    case ModFloatingPointArguments = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Match Document Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/ne/#match-document-fields
     */
    case NeMatchDocumentFields = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Query on GeoJSON Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/near/#query-on-geojson-data
     */
    case NearQueryOnGeoJSONData = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Specify Center Point Using GeoJSON
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nearSphere/#specify-center-point-using-geojson
     */
    case NearSphereSpecifyCenterPointUsingGeoJSON = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Select on Unmatching Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nin/#select-on-unmatching-documents
     */
    case NinSelectOnUnmatchingDocuments = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Select on Elements Not in an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nin/#select-on-elements-not-in-an-array
     */
    case NinSelectOnElementsNotInAnArray = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Query with Two Expressions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nor/#-nor-query-with-two-expressions
     */
    case NorQueryWithTwoExpressions = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Additional Comparisons
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nor/#-nor-and-additional-comparisons
     */
    case NorAdditionalComparisons = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * $nor and $exists
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/nor/#-nor-and--exists
     */
    case NorNorAndExists = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Syntax
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/#syntax
     */
    case NotSyntax = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Regular Expressions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/not/#-not-and-regular-expressions
     */
    case NotRegularExpressions = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * $or Clauses
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/or/#-or-clauses-and-indexes
     */
    case OrOrClauses = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Error Handling
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/or/#error-handling
     */
    case OrErrorHandling = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Select Random Items From a Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/rand/#select-random-items-from-a-collection
     */
    case RandSelectRandomItemsFromACollection = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Perform a LIKE Match
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/regex/#perform-a-like-match
     */
    case RegexPerformALIKEMatch = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Perform Case-Insensitive Regular Expression Match
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/regex/#perform-case-insensitive-regular-expression-match
     */
    case RegexPerformCaseInsensitiveRegularExpressionMatch = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sampleRate/#examples
     */
    case SampleRateExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Query an Array by Array Length
     *
     * @see https://www.mongodb.com/docs/manual/tutorial/query-arrays/#query-an-array-by-array-length
     */
    case SizeQueryAnArrayByArrayLength = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Search for a Single Word
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#search-for-a-single-word
     */
    case TextSearchForASingleWord = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "coffee"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Match Any of the Search Terms
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#search-for-a-single-word
     */
    case TextMatchAnyOfTheSearchTerms = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "bake coffee cake"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Search a Different Language
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#search-a-different-language
     */
    case TextSearchADifferentLanguage = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Case and Diacritic Insensitive Search
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#case-and-diacritic-insensitive-search
     */
    case TextCaseAndDiacriticInsensitiveSearch = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "\u0441\u044b\u0301\u0440\u043d\u0438\u043a\u0438 CAF\u00c9S"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Perform Case Sensitive Search
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#perform-case-sensitive-search
     */
    case TextPerformCaseSensitiveSearch = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Diacritic Sensitive Search
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#perform-case-sensitive-search
     */
    case TextDiacriticSensitiveSearch = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Text Search Score Examples
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/#perform-case-sensitive-search
     */
    case TextTextSearchScoreExamples = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Querying by Data Type
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/#querying-by-data-type
     */
    case TypeQueryingByDataType = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Querying by Multiple Data Type
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/#querying-by-multiple-data-type
     */
    case TypeQueryingByMultipleDataType = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Querying by MinKey and MaxKey
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/#querying-by-minkey-and-maxkey
     */
    case TypeQueryingByMinKeyAndMaxKey = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Querying by Array Type
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/type/#querying-by-array-type
     */
    case TypeQueryingByArrayType = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/query/where/#example
     */
    case WhereExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;
}
