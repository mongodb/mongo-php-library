<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

enum Pipelines: string
{
    /**
     * Add Numbers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/add/#add-numbers
     */
    case AddAddNumbers = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "total": {
                    "$add": [
                        "$price",
                        "$fee"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Perform Addition on a Date
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/add/#perform-addition-on-a-date
     */
    case AddPerformAdditionOnADate = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "billing_date": {
                    "$add": [
                        "$date",
                        {
                            "$numberInt": "259200000"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/allElementsTrue/#example
     */
    case AllElementsTrueExample = <<<'JSON'
    [
        {
            "$project": {
                "responses": {
                    "$numberInt": "1"
                },
                "isAllTrue": {
                    "$allElementsTrue": [
                        "$responses"
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/and/#example
     */
    case AndExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "qty": {
                    "$numberInt": "1"
                },
                "result": {
                    "$and": [
                        {
                            "$gt": [
                                "$qty",
                                {
                                    "$numberInt": "100"
                                }
                            ]
                        },
                        {
                            "$lt": [
                                "$qty",
                                {
                                    "$numberInt": "250"
                                }
                            ]
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/anyElementTrue/#example
     */
    case AnyElementTrueExample = <<<'JSON'
    [
        {
            "$project": {
                "responses": {
                    "$numberInt": "1"
                },
                "isAnyTrue": {
                    "$anyElementTrue": [
                        "$responses"
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayElemAt/#example
     */
    case ArrayElemAtExample = <<<'JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "first": {
                    "$arrayElemAt": [
                        "$favorites",
                        {
                            "$numberInt": "0"
                        }
                    ]
                },
                "last": {
                    "$arrayElemAt": [
                        "$favorites",
                        {
                            "$numberInt": "-1"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * $arrayToObject Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/#-arraytoobject--example
     */
    case ArrayToObjectArrayToObjectExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "dimensions": {
                    "$arrayToObject": [
                        "$dimensions"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * $objectToArray and $arrayToObject Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/#-objecttoarray----arraytoobject-example
     */
    case ArrayToObjectObjectToArrayAndArrayToObjectExample = <<<'JSON'
    [
        {
            "$addFields": {
                "instock": {
                    "$objectToArray": "$instock"
                }
            }
        },
        {
            "$addFields": {
                "instock": {
                    "$concatArrays": [
                        "$instock",
                        [
                            {
                                "k": "total",
                                "v": {
                                    "$sum": [
                                        "$instock.v"
                                    ]
                                }
                            }
                        ]
                    ]
                }
            }
        },
        {
            "$addFields": {
                "instock": {
                    "$arrayToObject": [
                        "$instock"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/#use-in--project-stage
     */
    case AvgUseInProjectStage = <<<'JSON'
    [
        {
            "$project": {
                "quizAvg": {
                    "$avg": [
                        "$quizzes"
                    ]
                },
                "labAvg": {
                    "$avg": [
                        "$labs"
                    ]
                },
                "examAvg": {
                    "$avg": [
                        "$final",
                        "$midterm"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/binarySize/#example
     */
    case BinarySizeExample = <<<'JSON'
    [
        {
            "$project": {
                "name": "$name",
                "imageSize": {
                    "$binarySize": "$binary"
                }
            }
        }
    ]
    JSON;

    /**
     * Bitwise AND with Two Integers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitAnd/#bitwise-and-with-two-integers
     */
    case BitAndBitwiseANDWithTwoIntegers = <<<'JSON'
    [
        {
            "$project": {
                "result": {
                    "$bitAnd": [
                        "$a",
                        "$b"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Bitwise AND with a Long and Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitAnd/#bitwise-and-with-a-long-and-integer
     */
    case BitAndBitwiseANDWithALongAndInteger = <<<'JSON'
    [
        {
            "$project": {
                "result": {
                    "$bitAnd": [
                        "$a",
                        {
                            "$numberLong": "63"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitNot/#example
     */
    case BitNotExample = <<<'JSON'
    [
        {
            "$project": {
                "result": {
                    "$bitNot": "$a"
                }
            }
        }
    ]
    JSON;

    /**
     * Bitwise OR with Two Integers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitOr/#bitwise-or-with-two-integers
     */
    case BitOrBitwiseORWithTwoIntegers = <<<'JSON'
    [
        {
            "$project": {
                "result": {
                    "$bitOr": [
                        "$a",
                        "$b"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Bitwise OR with a Long and Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitOr/#bitwise-or-with-a-long-and-integer
     */
    case BitOrBitwiseORWithALongAndInteger = <<<'JSON'
    [
        {
            "$project": {
                "result": {
                    "$bitOr": [
                        "$a",
                        {
                            "$numberLong": "63"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitXor/#example
     */
    case BitXorExample = <<<'JSON'
    [
        {
            "$project": {
                "result": {
                    "$bitXor": [
                        "$a",
                        "$b"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Return Sizes of Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/#return-sizes-of-documents
     */
    case BsonSizeReturnSizesOfDocuments = <<<'JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "object_size": {
                    "$bsonSize": "$$ROOT"
                }
            }
        }
    ]
    JSON;

    /**
     * Return Combined Size of All Documents in a Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/#return-combined-size-of-all-documents-in-a-collection
     */
    case BsonSizeReturnCombinedSizeOfAllDocumentsInACollection = <<<'JSON'
    [
        {
            "$group": {
                "_id": null,
                "combined_object_size": {
                    "$sum": {
                        "$bsonSize": "$$ROOT"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Return Document with Largest Specified Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/#return-document-with-largest-specified-field
     */
    case BsonSizeReturnDocumentWithLargestSpecifiedField = <<<'JSON'
    [
        {
            "$project": {
                "name": "$name",
                "task_object_size": {
                    "$bsonSize": "$current_task"
                }
            }
        },
        {
            "$sort": {
                "task_object_size": {
                    "$numberInt": "-1"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "1"
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cmp/#example
     */
    case CmpExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "qty": {
                    "$numberInt": "1"
                },
                "cmpTo250": {
                    "$cmp": [
                        "$qty",
                        {
                            "$numberInt": "250"
                        }
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concatArrays/#example
     */
    case ConcatArraysExample = <<<'JSON'
    [
        {
            "$project": {
                "items": {
                    "$concatArrays": [
                        "$instock",
                        "$ordered"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cond/#example
     */
    case CondExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "discount": {
                    "$cond": {
                        "if": {
                            "$gte": [
                                "$qty",
                                {
                                    "$numberInt": "250"
                                }
                            ]
                        },
                        "then": {
                            "$numberInt": "30"
                        },
                        "else": {
                            "$numberInt": "20"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/eq/#example
     */
    case EqExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "qty": {
                    "$numberInt": "1"
                },
                "qtyEq250": {
                    "$eq": [
                        "$qty",
                        {
                            "$numberInt": "250"
                        }
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#examples
     */
    case FilterExample = <<<'JSON'
    [
        {
            "$project": {
                "items": {
                    "$filter": {
                        "input": "$items",
                        "as": "item",
                        "cond": {
                            "$gte": [
                                "$$item.price",
                                {
                                    "$numberInt": "100"
                                }
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Using the limit field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#using-the-limit-field
     */
    case FilterUsingTheLimitField = <<<'JSON'
    [
        {
            "$project": {
                "items": {
                    "$filter": {
                        "input": "$items",
                        "cond": {
                            "$gte": [
                                "$$item.price",
                                {
                                    "$numberInt": "100"
                                }
                            ]
                        },
                        "as": "item",
                        "limit": {
                            "$numberInt": "1"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * limit as a Numeric Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#limit-as-a-numeric-expression
     */
    case FilterLimitAsANumericExpression = <<<'JSON'
    [
        {
            "$project": {
                "items": {
                    "$filter": {
                        "input": "$items",
                        "cond": {
                            "$lte": [
                                "$$item.price",
                                {
                                    "$numberInt": "150"
                                }
                            ]
                        },
                        "as": "item",
                        "limit": {
                            "$numberInt": "2"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * limit Greater than Possible Matches
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#limit-greater-than-possible-matches
     */
    case FilterLimitGreaterThanPossibleMatches = <<<'JSON'
    [
        {
            "$project": {
                "items": {
                    "$filter": {
                        "input": "$items",
                        "cond": {
                            "$gte": [
                                "$$item.price",
                                {
                                    "$numberInt": "100"
                                }
                            ]
                        },
                        "as": "item",
                        "limit": {
                            "$numberInt": "5"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /** Use in $addFields Stage */
    case FirstUseInAddFieldsStage = <<<'JSON'
    [
        {
            "$addFields": {
                "firstItem": {
                    "$first": "$items"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN-array-element/#example
     */
    case FirstNExample = <<<'JSON'
    [
        {
            "$addFields": {
                "firstScores": {
                    "$firstN": {
                        "n": {
                            "$numberInt": "3"
                        },
                        "input": "$score"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Using $firstN as an Aggregation Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#using--firstn-as-an-aggregation-expression
     */
    case FirstNUsingFirstNAsAnAggregationExpression = <<<'JSON'
    [
        {
            "$documents": [
                {
                    "array": [
                        {
                            "$numberInt": "10"
                        },
                        {
                            "$numberInt": "20"
                        },
                        {
                            "$numberInt": "30"
                        },
                        {
                            "$numberInt": "40"
                        }
                    ]
                }
            ]
        },
        {
            "$project": {
                "firstThreeElements": {
                    "$firstN": {
                        "input": "$array",
                        "n": {
                            "$numberInt": "3"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Usage Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/function/#example-1--usage-example
     */
    case FunctionUsageExample = <<<'JSON'
    [
        {
            "$addFields": {
                "isFound": {
                    "$function": {
                        "body": {
                            "$code": "function(name) {\n    return hex_md5(name) == \"15b0a220baa16331e8d80e15367677ad\"\n}"
                        },
                        "args": [
                            "$name"
                        ],
                        "lang": "js"
                    }
                },
                "message": {
                    "$function": {
                        "body": {
                            "$code": "function(name, scores) {\n    let total = Array.sum(scores);\n    return `Hello ${name}. Your total score is ${total}.`\n}"
                        },
                        "args": [
                            "$name",
                            "$scores"
                        ],
                        "lang": "js"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Alternative to $where
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/function/#example-2--alternative-to--where
     */
    case FunctionAlternativeToWhere = <<<'JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$function": {
                        "body": {
                            "$code": "function(name) {\n    return hex_md5(name) == \"15b0a220baa16331e8d80e15367677ad\";\n}"
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

    /**
     * Query Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/#query-fields-that-contain-periods--.-
     */
    case GetFieldQueryFieldsThatContainPeriods = <<<'JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$gt": [
                        {
                            "$getField": {
                                "field": "price.usd"
                            }
                        },
                        {
                            "$numberInt": "200"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Query Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/#query-fields-that-start-with-a-dollar-sign----
     */
    case GetFieldQueryFieldsThatStartWithADollarSign = <<<'JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$gt": [
                        {
                            "$getField": {
                                "field": {
                                    "$literal": "$price"
                                }
                            }
                        },
                        {
                            "$numberInt": "200"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Query a Field in a Sub-document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/#query-a-field-in-a-sub-document
     */
    case GetFieldQueryAFieldInASubdocument = <<<'JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$lte": [
                        {
                            "$getField": {
                                "field": {
                                    "$literal": "$small"
                                },
                                "input": "$quantity"
                            }
                        },
                        {
                            "$numberInt": "20"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gt/#example
     */
    case GtExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "qty": {
                    "$numberInt": "1"
                },
                "qtyGt250": {
                    "$gt": [
                        "$qty",
                        {
                            "$numberInt": "250"
                        }
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gte/#example
     */
    case GteExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "qty": {
                    "$numberInt": "1"
                },
                "qtyGte250": {
                    "$gte": [
                        "$qty",
                        {
                            "$numberInt": "250"
                        }
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Single Input Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ifNull/#single-input-expression
     */
    case IfNullSingleInputExpression = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "description": {
                    "$ifNull": [
                        "$description",
                        "Unspecified"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Multiple Input Expressions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ifNull/#multiple-input-expressions
     */
    case IfNullMultipleInputExpressions = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "value": {
                    "$ifNull": [
                        "$description",
                        "$quantity",
                        "Unspecified"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/in/#example
     */
    case InExample = <<<'JSON'
    [
        {
            "$project": {
                "store location": "$location",
                "has bananas": {
                    "$in": [
                        "bananas",
                        "$in_stock"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfArray/#example
     */
    case IndexOfArrayExample = <<<'JSON'
    [
        {
            "$project": {
                "index": {
                    "$indexOfArray": [
                        "$items",
                        {
                            "$numberInt": "2"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isArray/#example
     */
    case IsArrayExample = <<<'JSON'
    [
        {
            "$project": {
                "items": {
                    "$cond": {
                        "if": {
                            "$and": [
                                {
                                    "$isArray": [
                                        "$instock"
                                    ]
                                },
                                {
                                    "$isArray": [
                                        "$ordered"
                                    ]
                                }
                            ]
                        },
                        "then": {
                            "$concatArrays": [
                                "$instock",
                                "$ordered"
                            ]
                        },
                        "else": "One or more fields is not an array."
                    }
                }
            }
        }
    ]
    JSON;

    /** Use in $addFields Stage */
    case LastUseInAddFieldsStage = <<<'JSON'
    [
        {
            "$addFields": {
                "lastItem": {
                    "$last": "$items"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN-array-element/#example
     */
    case LastNExample = <<<'JSON'
    [
        {
            "$addFields": {
                "lastScores": {
                    "$lastN": {
                        "n": {
                            "$numberInt": "3"
                        },
                        "input": "$score"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Using $lastN as an Aggregation Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#using--lastn-as-an-aggregation-expression
     */
    case LastNUsingLastNAsAnAggregationExpression = <<<'JSON'
    [
        {
            "$documents": [
                {
                    "array": [
                        {
                            "$numberInt": "10"
                        },
                        {
                            "$numberInt": "20"
                        },
                        {
                            "$numberInt": "30"
                        },
                        {
                            "$numberInt": "40"
                        }
                    ]
                }
            ]
        },
        {
            "$project": {
                "lastThreeElements": {
                    "$lastN": {
                        "input": "$array",
                        "n": {
                            "$numberInt": "3"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lt/#example
     */
    case LtExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "qty": {
                    "$numberInt": "1"
                },
                "qtyLt250": {
                    "$lt": [
                        "$qty",
                        {
                            "$numberInt": "250"
                        }
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lte/#example
     */
    case LteExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "qty": {
                    "$numberInt": "1"
                },
                "qtyLte250": {
                    "$lte": [
                        "$qty",
                        {
                            "$numberInt": "250"
                        }
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Add to Each Element of an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/#add-to-each-element-of-an-array
     */
    case MapAddToEachElementOfAnArray = <<<'JSON'
    [
        {
            "$project": {
                "adjustedGrades": {
                    "$map": {
                        "input": "$quizzes",
                        "as": "grade",
                        "in": {
                            "$add": [
                                "$$grade",
                                {
                                    "$numberInt": "2"
                                }
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Truncate Each Array Element
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/#truncate-each-array-element
     */
    case MapTruncateEachArrayElement = <<<'JSON'
    [
        {
            "$project": {
                "city": "$city",
                "integerValues": {
                    "$map": {
                        "input": "$distances",
                        "as": "decimalValue",
                        "in": {
                            "$trunc": [
                                "$$decimalValue"
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Convert Celsius Temperatures to Fahrenheit
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/#convert-celsius-temperatures-to-fahrenheit
     */
    case MapConvertCelsiusTemperaturesToFahrenheit = <<<'JSON'
    [
        {
            "$addFields": {
                "tempsF": {
                    "$map": {
                        "input": "$tempsC",
                        "as": "tempInCelsius",
                        "in": {
                            "$add": [
                                {
                                    "$multiply": [
                                        "$$tempInCelsius",
                                        {
                                            "$numberDouble": "1.8000000000000000444"
                                        }
                                    ]
                                },
                                {
                                    "$numberInt": "32"
                                }
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/#use-in--project-stage
     */
    case MaxUseInProjectStage = <<<'JSON'
    [
        {
            "$project": {
                "quizMax": {
                    "$max": [
                        "$quizzes"
                    ]
                },
                "labMax": {
                    "$max": [
                        "$labs"
                    ]
                },
                "examMax": {
                    "$max": [
                        "$final",
                        "$midterm"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN-array-element/#example
     */
    case MaxNExample = <<<'JSON'
    [
        {
            "$addFields": {
                "maxScores": {
                    "$maxN": {
                        "n": {
                            "$numberInt": "2"
                        },
                        "input": "$score"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Use $median in a $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/#use-operatorname-in-a--project-stage
     */
    case MedianUseMedianInAProjectStage = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "studentId": {
                    "$numberInt": "1"
                },
                "testMedians": {
                    "$median": {
                        "input": [
                            "$test01",
                            "$test02",
                            "$test03"
                        ],
                        "method": "approximate"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * $mergeObjects
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/#-mergeobjects
     */
    case MergeObjectsMergeObjects = <<<'JSON'
    [
        {
            "$lookup": {
                "from": "items",
                "localField": "item",
                "foreignField": "item",
                "as": "fromItems"
            }
        },
        {
            "$replaceRoot": {
                "newRoot": {
                    "$mergeObjects": [
                        {
                            "$arrayElemAt": [
                                "$fromItems",
                                {
                                    "$numberInt": "0"
                                }
                            ]
                        },
                        "$$ROOT"
                    ]
                }
            }
        },
        {
            "$project": {
                "fromItems": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/#use-in--project-stage
     */
    case MinUseInProjectStage = <<<'JSON'
    [
        {
            "$project": {
                "quizMin": {
                    "$min": [
                        "$quizzes"
                    ]
                },
                "labMin": {
                    "$min": [
                        "$labs"
                    ]
                },
                "examMin": {
                    "$min": [
                        "$final",
                        "$midterm"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN-array-element/#example
     */
    case MinNExample = <<<'JSON'
    [
        {
            "$addFields": {
                "minScores": {
                    "$minN": {
                        "n": {
                            "$numberInt": "2"
                        },
                        "input": "$score"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ne/#example
     */
    case NeExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "qty": {
                    "$numberInt": "1"
                },
                "qtyNe250": {
                    "$ne": [
                        "$qty",
                        {
                            "$numberInt": "250"
                        }
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/not/#example
     */
    case NotExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "result": {
                    "$not": [
                        {
                            "$gt": [
                                "$qty",
                                {
                                    "$numberInt": "250"
                                }
                            ]
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * $objectToArray Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/#-objecttoarray-example
     */
    case ObjectToArrayObjectToArrayExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "dimensions": {
                    "$objectToArray": "$dimensions"
                }
            }
        }
    ]
    JSON;

    /**
     * $objectToArray to Sum Nested Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/#-objecttoarray-to-sum-nested-fields
     */
    case ObjectToArrayObjectToArrayToSumNestedFields = <<<'JSON'
    [
        {
            "$project": {
                "warehouses": {
                    "$objectToArray": "$instock"
                }
            }
        },
        {
            "$unwind": {
                "path": "$warehouses"
            }
        },
        {
            "$group": {
                "_id": "$warehouses.k",
                "total": {
                    "$sum": "$warehouses.v"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/or/#example
     */
    case OrExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "result": {
                    "$or": [
                        {
                            "$gt": [
                                "$qty",
                                {
                                    "$numberInt": "250"
                                }
                            ]
                        },
                        {
                            "$lt": [
                                "$qty",
                                {
                                    "$numberInt": "200"
                                }
                            ]
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Use $percentile in a $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/#use-operatorname-in-a--project-stage
     */
    case PercentileUsePercentileInAProjectStage = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "studentId": {
                    "$numberInt": "1"
                },
                "testPercentiles": {
                    "$percentile": {
                        "input": [
                            "$test01",
                            "$test02",
                            "$test03"
                        ],
                        "p": [
                            {
                                "$numberDouble": "0.5"
                            },
                            {
                                "$numberDouble": "0.94999999999999995559"
                            }
                        ],
                        "method": "approximate"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Generate Random Data Points
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rand/#generate-random-data-points
     */
    case RandGenerateRandomDataPoints = <<<'JSON'
    [
        {
            "$set": {
                "amount": {
                    "$multiply": [
                        {
                            "$rand": {}
                        },
                        {
                            "$numberInt": "100"
                        }
                    ]
                }
            }
        },
        {
            "$set": {
                "amount": {
                    "$floor": "$amount"
                }
            }
        },
        {
            "$merge": {
                "into": "donors"
            }
        }
    ]
    JSON;

    /**
     * Select Random Items From a Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rand/#select-random-items-from-a-collection
     */
    case RandSelectRandomItemsFromACollection = <<<'JSON'
    [
        {
            "$match": {
                "district": {
                    "$numberInt": "3"
                }
            }
        },
        {
            "$match": {
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
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/range/#example
     */
    case RangeExample = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "city": {
                    "$numberInt": "1"
                },
                "Rest stops": {
                    "$range": [
                        {
                            "$numberInt": "0"
                        },
                        "$distance",
                        {
                            "$numberInt": "25"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Multiplication
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#multiplication
     */
    case ReduceMultiplication = <<<'JSON'
    [
        {
            "$group": {
                "_id": "$experimentId",
                "probabilityArr": {
                    "$push": "$probability"
                }
            }
        },
        {
            "$project": {
                "description": {
                    "$numberInt": "1"
                },
                "results": {
                    "$reduce": {
                        "input": "$probabilityArr",
                        "initialValue": {
                            "$numberInt": "1"
                        },
                        "in": {
                            "$multiply": [
                                "$$value",
                                "$$this"
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Discounted Merchandise
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#discounted-merchandise
     */
    case ReduceDiscountedMerchandise = <<<'JSON'
    [
        {
            "$project": {
                "discountedPrice": {
                    "$reduce": {
                        "input": "$discounts",
                        "initialValue": "$price",
                        "in": {
                            "$multiply": [
                                "$$value",
                                {
                                    "$subtract": [
                                        {
                                            "$numberInt": "1"
                                        },
                                        "$$this"
                                    ]
                                }
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * String Concatenation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#string-concatenation
     */
    case ReduceStringConcatenation = <<<'JSON'
    [
        {
            "$match": {
                "hobbies": {
                    "$gt": []
                }
            }
        },
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "bio": {
                    "$reduce": {
                        "input": "$hobbies",
                        "initialValue": "My hobbies include:",
                        "in": {
                            "$concat": [
                                "$$value",
                                {
                                    "$cond": {
                                        "if": {
                                            "$eq": [
                                                "$$value",
                                                "My hobbies include:"
                                            ]
                                        },
                                        "then": " ",
                                        "else": ", "
                                    }
                                },
                                "$$this"
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Array Concatenation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#array-concatenation
     */
    case ReduceArrayConcatenation = <<<'JSON'
    [
        {
            "$project": {
                "collapsed": {
                    "$reduce": {
                        "input": "$arr",
                        "initialValue": [],
                        "in": {
                            "$concatArrays": [
                                "$$value",
                                "$$this"
                            ]
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Computing a Multiple Reductions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#computing-a-multiple-reductions
     */
    case ReduceComputingAMultipleReductions = <<<'JSON'
    [
        {
            "$project": {
                "results": {
                    "$reduce": {
                        "input": "$arr",
                        "initialValue": [],
                        "in": {
                            "collapsed": {
                                "$concatArrays": [
                                    "$$value.collapsed",
                                    "$$this"
                                ]
                            },
                            "firstValues": {
                                "$concatArrays": [
                                    "$$value.firstValues",
                                    {
                                        "$slice": [
                                            "$$this",
                                            {
                                                "$numberInt": "1"
                                            }
                                        ]
                                    }
                                ]
                            }
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reverseArray/#example
     */
    case ReverseArrayExample = <<<'JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "reverseFavorites": {
                    "$reverseArray": "$favorites"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setDifference/#example
     */
    case SetDifferenceExample = <<<'JSON'
    [
        {
            "$project": {
                "flowerFieldA": {
                    "$numberInt": "1"
                },
                "flowerFieldB": {
                    "$numberInt": "1"
                },
                "inBOnly": {
                    "$setDifference": [
                        "$flowerFieldB",
                        "$flowerFieldA"
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setEquals/#example
     */
    case SetEqualsExample = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "cakes": {
                    "$numberInt": "1"
                },
                "cupcakes": {
                    "$numberInt": "1"
                },
                "sameFlavors": {
                    "$setEquals": [
                        "$cakes",
                        "$cupcakes"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Add Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#add-fields-that-contain-periods--.-
     */
    case SetFieldAddFieldsThatContainPeriods = <<<'JSON'
    [
        {
            "$replaceWith": {
                "$setField": {
                    "field": "price.usd",
                    "input": "$$ROOT",
                    "value": "$price"
                }
            }
        },
        {
            "$unset": [
                "price"
            ]
        }
    ]
    JSON;

    /**
     * Add Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#add-fields-that-start-with-a-dollar-sign----
     */
    case SetFieldAddFieldsThatStartWithADollarSign = <<<'JSON'
    [
        {
            "$replaceWith": {
                "$setField": {
                    "field": {
                        "$literal": "$price"
                    },
                    "input": "$$ROOT",
                    "value": "$price"
                }
            }
        },
        {
            "$unset": [
                "price"
            ]
        }
    ]
    JSON;

    /**
     * Update Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#update-fields-that-contain-periods--.-
     */
    case SetFieldUpdateFieldsThatContainPeriods = <<<'JSON'
    [
        {
            "$match": {
                "_id": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$replaceWith": {
                "$setField": {
                    "field": "price.usd",
                    "input": "$$ROOT",
                    "value": {
                        "$numberDouble": "49.99000000000000199"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Update Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#update-fields-that-start-with-a-dollar-sign----
     */
    case SetFieldUpdateFieldsThatStartWithADollarSign = <<<'JSON'
    [
        {
            "$match": {
                "_id": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$replaceWith": {
                "$setField": {
                    "field": {
                        "$literal": "$price"
                    },
                    "input": "$$ROOT",
                    "value": {
                        "$numberDouble": "49.99000000000000199"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Remove Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#remove-fields-that-contain-periods--.-
     */
    case SetFieldRemoveFieldsThatContainPeriods = <<<'JSON'
    [
        {
            "$replaceWith": {
                "$setField": {
                    "field": "price.usd",
                    "input": "$$ROOT",
                    "value": "$$REMOVE"
                }
            }
        }
    ]
    JSON;

    /**
     * Remove Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#remove-fields-that-start-with-a-dollar-sign----
     */
    case SetFieldRemoveFieldsThatStartWithADollarSign = <<<'JSON'
    [
        {
            "$replaceWith": {
                "$setField": {
                    "field": {
                        "$literal": "$price"
                    },
                    "input": "$$ROOT",
                    "value": "$$REMOVE"
                }
            }
        }
    ]
    JSON;

    /**
     * Elements Array Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIntersection/#elements-array-example
     */
    case SetIntersectionElementsArrayExample = <<<'JSON'
    [
        {
            "$project": {
                "flowerFieldA": {
                    "$numberInt": "1"
                },
                "flowerFieldB": {
                    "$numberInt": "1"
                },
                "commonToBoth": {
                    "$setIntersection": [
                        "$flowerFieldA",
                        "$flowerFieldB"
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Retrieve Documents for Roles Granted to the Current User
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIntersection/#retrieve-documents-for-roles-granted-to-the-current-user
     */
    case SetIntersectionRetrieveDocumentsForRolesGrantedToTheCurrentUser = <<<'JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$not": [
                        {
                            "$eq": [
                                {
                                    "$setIntersection": [
                                        "$allowedRoles",
                                        "$$USER_ROLES.role"
                                    ]
                                },
                                []
                            ]
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIsSubset/#example
     */
    case SetIsSubsetExample = <<<'JSON'
    [
        {
            "$project": {
                "flowerFieldA": {
                    "$numberInt": "1"
                },
                "flowerFieldB": {
                    "$numberInt": "1"
                },
                "AisSubset": {
                    "$setIsSubset": [
                        "$flowerFieldA",
                        "$flowerFieldB"
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setUnion/#example
     */
    case SetUnionExample = <<<'JSON'
    [
        {
            "$project": {
                "flowerFieldA": {
                    "$numberInt": "1"
                },
                "flowerFieldB": {
                    "$numberInt": "1"
                },
                "allValues": {
                    "$setUnion": [
                        "$flowerFieldA",
                        "$flowerFieldB"
                    ]
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/size/#example
     */
    case SizeExample = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "numberOfColors": {
                    "$cond": {
                        "if": {
                            "$isArray": [
                                "$colors"
                            ]
                        },
                        "then": {
                            "$size": "$colors"
                        },
                        "else": "NA"
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/slice/#example
     */
    case SliceExample = <<<'JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "threeFavorites": {
                    "$slice": [
                        "$favorites",
                        {
                            "$numberInt": "3"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Sort on a Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-on-a-field
     */
    case SortArraySortOnAField = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "result": {
                    "$sortArray": {
                        "input": "$team",
                        "sortBy": {
                            "name": {
                                "$numberInt": "1"
                            }
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Sort on a Subfield
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-on-a-subfield
     */
    case SortArraySortOnASubfield = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "result": {
                    "$sortArray": {
                        "input": "$team",
                        "sortBy": {
                            "address.city": {
                                "$numberInt": "-1"
                            }
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Sort on Multiple Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-on-multiple-fields
     */
    case SortArraySortOnMultipleFields = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "result": {
                    "$sortArray": {
                        "input": "$team",
                        "sortBy": {
                            "age": {
                                "$numberInt": "-1"
                            },
                            "name": {
                                "$numberInt": "1"
                            }
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Sort an Array of Integers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-an-array-of-integers
     */
    case SortArraySortAnArrayOfIntegers = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "result": {
                    "$sortArray": {
                        "input": [
                            {
                                "$numberInt": "1"
                            },
                            {
                                "$numberInt": "4"
                            },
                            {
                                "$numberInt": "1"
                            },
                            {
                                "$numberInt": "6"
                            },
                            {
                                "$numberInt": "12"
                            },
                            {
                                "$numberInt": "5"
                            }
                        ],
                        "sortBy": {
                            "$numberInt": "1"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Sort on Mixed Type Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-on-mixed-type-fields
     */
    case SortArraySortOnMixedTypeFields = <<<'JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "result": {
                    "$sortArray": {
                        "input": [
                            {
                                "$numberInt": "20"
                            },
                            {
                                "$numberInt": "4"
                            },
                            {
                                "a": "Free"
                            },
                            {
                                "$numberInt": "6"
                            },
                            {
                                "$numberInt": "21"
                            },
                            {
                                "$numberInt": "5"
                            },
                            "Gratis",
                            {
                                "a": null
                            },
                            {
                                "a": {
                                    "sale": true,
                                    "price": {
                                        "$numberInt": "19"
                                    }
                                }
                            },
                            {
                                "$numberDouble": "10.230000000000000426"
                            },
                            {
                                "a": "On sale"
                            }
                        ],
                        "sortBy": {
                            "$numberInt": "1"
                        }
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/#use-in--project-stage
     */
    case StdDevPopUseInProjectStage = <<<'JSON'
    [
        {
            "$project": {
                "stdDev": {
                    "$stdDevPop": [
                        "$scores.score"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Subtract Numbers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/#subtract-numbers
     */
    case SubtractSubtractNumbers = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "total": {
                    "$subtract": [
                        {
                            "$add": [
                                "$price",
                                "$fee"
                            ]
                        },
                        "$discount"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Subtract Two Dates
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/#subtract-two-dates
     */
    case SubtractSubtractTwoDates = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "dateDifference": {
                    "$subtract": [
                        "$$NOW",
                        "$date"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Subtract Milliseconds from a Date
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/#subtract-milliseconds-from-a-date
     */
    case SubtractSubtractMillisecondsFromADate = <<<'JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "dateDifference": {
                    "$subtract": [
                        "$date",
                        {
                            "$numberInt": "300000"
                        }
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/#use-in--project-stage
     */
    case SumUseInProjectStage = <<<'JSON'
    [
        {
            "$project": {
                "quizTotal": {
                    "$sum": [
                        "$quizzes"
                    ]
                },
                "labTotal": {
                    "$sum": [
                        "$labs"
                    ]
                },
                "examTotal": {
                    "$sum": [
                        "$final",
                        "$midterm"
                    ]
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/#example
     */
    case SwitchExample = <<<'JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "summary": {
                    "$switch": {
                        "branches": [
                            {
                                "case": {
                                    "$gte": [
                                        {
                                            "$avg": [
                                                "$scores"
                                            ]
                                        },
                                        {
                                            "$numberInt": "90"
                                        }
                                    ]
                                },
                                "then": "Doing great!"
                            },
                            {
                                "case": {
                                    "$and": [
                                        {
                                            "$gte": [
                                                {
                                                    "$avg": [
                                                        "$scores"
                                                    ]
                                                },
                                                {
                                                    "$numberInt": "80"
                                                }
                                            ]
                                        },
                                        {
                                            "$lt": [
                                                {
                                                    "$avg": [
                                                        "$scores"
                                                    ]
                                                },
                                                {
                                                    "$numberInt": "90"
                                                }
                                            ]
                                        }
                                    ]
                                },
                                "then": "Doing pretty well."
                            },
                            {
                                "case": {
                                    "$lt": [
                                        {
                                            "$avg": [
                                                "$scores"
                                            ]
                                        },
                                        {
                                            "$numberInt": "80"
                                        }
                                    ]
                                },
                                "then": "Needs improvement."
                            }
                        ],
                        "default": "No scores found."
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toHashedIndexKey/#example
     */
    case ToHashedIndexKeyExample = <<<'JSON'
    [
        {
            "$documents": [
                {
                    "val": "string to hash"
                }
            ]
        },
        {
            "$addFields": {
                "hashedVal": {
                    "$toHashedIndexKey": "$val"
                }
            }
        }
    ]
    JSON;

    /**
     * Remove Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/#remove-fields-that-contain-periods--.-
     */
    case UnsetFieldRemoveFieldsThatContainPeriods = <<<'JSON'
    [
        {
            "$replaceWith": {
                "$unsetField": {
                    "field": "price.usd",
                    "input": "$$ROOT"
                }
            }
        }
    ]
    JSON;

    /**
     * Remove Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/#remove-fields-that-start-with-a-dollar-sign----
     */
    case UnsetFieldRemoveFieldsThatStartWithADollarSign = <<<'JSON'
    [
        {
            "$replaceWith": {
                "$unsetField": {
                    "field": {
                        "$literal": "$price"
                    },
                    "input": "$$ROOT"
                }
            }
        }
    ]
    JSON;

    /**
     * Remove A Subfield
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/#remove-a-subfield
     */
    case UnsetFieldRemoveASubfield = <<<'JSON'
    [
        {
            "$replaceWith": {
                "$setField": {
                    "field": "price",
                    "input": "$$ROOT",
                    "value": {
                        "$unsetField": {
                            "field": "euro",
                            "input": {
                                "$getField": {
                                    "field": "price"
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
     * Matrix Transposition
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/zip/#matrix-transposition
     */
    case ZipMatrixTransposition = <<<'JSON'
    [
        {
            "$project": {
                "_id": false,
                "transposed": {
                    "$zip": {
                        "inputs": [
                            {
                                "$arrayElemAt": [
                                    "$matrix",
                                    {
                                        "$numberInt": "0"
                                    }
                                ]
                            },
                            {
                                "$arrayElemAt": [
                                    "$matrix",
                                    {
                                        "$numberInt": "1"
                                    }
                                ]
                            },
                            {
                                "$arrayElemAt": [
                                    "$matrix",
                                    {
                                        "$numberInt": "2"
                                    }
                                ]
                            }
                        ]
                    }
                }
            }
        }
    ]
    JSON;

    /**
     * Filtering and Preserving Indexes
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/zip/#filtering-and-preserving-indexes
     */
    case ZipFilteringAndPreservingIndexes = <<<'JSON'
    [
        {
            "$project": {
                "_id": false,
                "pages": {
                    "$filter": {
                        "input": {
                            "$zip": {
                                "inputs": [
                                    "$pages",
                                    {
                                        "$range": [
                                            {
                                                "$numberInt": "0"
                                            },
                                            {
                                                "$size": "$pages"
                                            }
                                        ]
                                    }
                                ]
                            }
                        },
                        "as": "pageWithIndex",
                        "cond": {
                            "$let": {
                                "vars": {
                                    "page": {
                                        "$arrayElemAt": [
                                            "$$pageWithIndex",
                                            {
                                                "$numberInt": "0"
                                            }
                                        ]
                                    }
                                },
                                "in": {
                                    "$gte": [
                                        "$$page.reviews",
                                        {
                                            "$numberInt": "1"
                                        }
                                    ]
                                }
                            }
                        }
                    }
                }
            }
        }
    ]
    JSON;
}
