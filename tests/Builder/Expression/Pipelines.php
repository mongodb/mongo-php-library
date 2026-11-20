<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Expression;

enum Pipelines: string
{
    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/abs/#example
     */
    case AbsExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "delta": {
                    "$abs": {
                        "$subtract": [
                            "$startTemp",
                            "$endTemp"
                        ]
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acos/#example
     */
    case AcosExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "angle_a": {
                    "$radiansToDegrees": {
                        "$acos": {
                            "$divide": [
                                "$side_b",
                                "$hypotenuse"
                            ]
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acosh/#example
     */
    case AcoshExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "y-coordinate": {
                    "$radiansToDegrees": {
                        "$acosh": "$x-coordinate"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Add Numbers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/add/#add-numbers
     */
    case AddAddNumbers = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Perform Addition on a Date
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/add/#perform-addition-on-a-date
     */
    case AddPerformAdditionOnADate = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/allElementsTrue/#example
     */
    case AllElementsTrueExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/and/#example
     */
    case AndExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/anyElementTrue/#example
     */
    case AnyElementTrueExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayElemAt/#example
     */
    case ArrayElemAtExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * $arrayToObject Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/#-arraytoobject--example
     */
    case ArrayToObjectArrayToObjectExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * $objectToArray and $arrayToObject Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/arrayToObject/#-objecttoarray----arraytoobject-example
     */
    case ArrayToObjectObjectToArrayAndArrayToObjectExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asin/#example
     */
    case AsinExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "angle_a": {
                    "$radiansToDegrees": {
                        "$asin": {
                            "$divide": [
                                "$side_a",
                                "$hypotenuse"
                            ]
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asinh/#example
     */
    case AsinhExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "y-coordinate": {
                    "$radiansToDegrees": {
                        "$asinh": "$x-coordinate"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atan/#example
     */
    case AtanExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "angle_a": {
                    "$radiansToDegrees": {
                        "$atan": {
                            "$divide": [
                                "$side_b",
                                "$side_a"
                            ]
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atan2/#example
     */
    case Atan2Example = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "angle_a": {
                    "$radiansToDegrees": {
                        "$atan2": [
                            "$side_b",
                            "$side_a"
                        ]
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atanh/#example
     */
    case AtanhExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "y-coordinate": {
                    "$radiansToDegrees": {
                        "$atanh": "$x-coordinate"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/#use-in--project-stage
     */
    case AvgUseInProjectStage = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/binarySize/#example
     */
    case BinarySizeExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Bitwise AND with Two Integers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitAnd/#bitwise-and-with-two-integers
     */
    case BitAndBitwiseANDWithTwoIntegers = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Bitwise AND with a Long and Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitAnd/#bitwise-and-with-a-long-and-integer
     */
    case BitAndBitwiseANDWithALongAndInteger = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitNot/#example
     */
    case BitNotExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "result": {
                    "$bitNot": "$a"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Bitwise OR with Two Integers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitOr/#bitwise-or-with-two-integers
     */
    case BitOrBitwiseORWithTwoIntegers = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Bitwise OR with a Long and Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitOr/#bitwise-or-with-a-long-and-integer
     */
    case BitOrBitwiseORWithALongAndInteger = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bitXor/#example
     */
    case BitXorExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom-array-operator/#example
     */
    case BottomExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "bottomScore": {
                    "$bottom": {
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        },
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "input": "$results"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN-array-operator/#example
     */
    case BottomNExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "bottomScores": {
                    "$bottomN": {
                        "n": {
                            "$numberInt": "3"
                        },
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        },
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "input": "$results"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Return Sizes of Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/#return-sizes-of-documents
     */
    case BsonSizeReturnSizesOfDocuments = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Return Combined Size of All Documents in a Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/#return-combined-size-of-all-documents-in-a-collection
     */
    case BsonSizeReturnCombinedSizeOfAllDocumentsInACollection = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Return Document with Largest Specified Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bsonSize/#return-document-with-largest-specified-field
     */
    case BsonSizeReturnDocumentWithLargestSpecifiedField = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ceil/#example
     */
    case CeilExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "value": {
                    "$numberInt": "1"
                },
                "ceilingValue": {
                    "$ceil": "$value"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cmp/#example
     */
    case CmpExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concat/#example
     */
    case ConcatExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "itemDescription": {
                    "$concat": [
                        "$item",
                        " - ",
                        "$description"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concatArrays/#example
     */
    case ConcatArraysExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cond/#example
     */
    case CondExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/#example
     */
    case ConvertExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "convertedPrice": {
                    "$convert": {
                        "input": "$price",
                        "to": "decimal",
                        "onError": "Error",
                        "onNull": {
                            "$numberDecimal": "0"
                        }
                    }
                },
                "convertedQty": {
                    "$convert": {
                        "input": "$qty",
                        "to": "int",
                        "onError": {
                            "$concat": [
                                "Could not convert ",
                                {
                                    "$toString": "$qty"
                                },
                                " to type integer."
                            ]
                        },
                        "onNull": {
                            "$numberInt": "0"
                        }
                    }
                }
            }
        },
        {
            "$project": {
                "totalPrice": {
                    "$switch": {
                        "branches": [
                            {
                                "case": {
                                    "$eq": [
                                        {
                                            "$type": "$convertedPrice"
                                        },
                                        "string"
                                    ]
                                },
                                "then": "NaN"
                            },
                            {
                                "case": {
                                    "$eq": [
                                        {
                                            "$type": "$convertedQty"
                                        },
                                        "string"
                                    ]
                                },
                                "then": "NaN"
                            }
                        ],
                        "default": {
                            "$multiply": [
                                "$convertedPrice",
                                "$convertedQty"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Convert Hexadecimal String to Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/#base-conversion
     */
    case ConvertConvertHexadecimalStringToInteger = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "decimalValue": {
                    "$convert": {
                        "input": "$hexString",
                        "to": "int",
                        "base": {
                            "$numberInt": "16"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Convert Integer to Binary String
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/#base-conversion
     */
    case ConvertConvertIntegerToBinaryString = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "binaryString": {
                    "$convert": {
                        "input": "$value",
                        "to": "string",
                        "base": {
                            "$numberInt": "2"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cos/#example
     */
    case CosExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "side_a": {
                    "$multiply": [
                        {
                            "$cos": {
                                "$degreesToRadians": "$angle_a"
                            }
                        },
                        "$hypotenuse"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cosh/#example
     */
    case CoshExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "cosh_output": {
                    "$cosh": {
                        "$degreesToRadians": "$angle"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/createObjectId/#example
     */
    case CreateObjectIdExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "objectId": {
                    "$createObjectId": {}
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Add a Future Date
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/#add-a-future-date
     */
    case DateAddAddAFutureDate = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "expectedDeliveryDate": {
                    "$dateAdd": {
                        "startDate": "$purchaseDate",
                        "unit": "day",
                        "amount": {
                            "$numberInt": "3"
                        }
                    }
                }
            }
        },
        {
            "$merge": {
                "into": "shipping"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Filter on a Date Range
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/#filter-on-a-date-range
     */
    case DateAddFilterOnADateRange = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$gt": [
                        "$deliveryDate",
                        {
                            "$dateAdd": {
                                "startDate": "$purchaseDate",
                                "unit": "day",
                                "amount": {
                                    "$numberInt": "5"
                                }
                            }
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
                "custId": {
                    "$numberInt": "1"
                },
                "purchased": {
                    "$dateToString": {
                        "format": "%Y-%m-%d",
                        "date": "$purchaseDate"
                    }
                },
                "delivery": {
                    "$dateToString": {
                        "format": "%Y-%m-%d",
                        "date": "$deliveryDate"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Adjust for Daylight Savings Time
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/#adjust-for-daylight-savings-time
     */
    case DateAddAdjustForDaylightSavingsTime = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "location": {
                    "$numberInt": "1"
                },
                "start": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": "$login"
                    }
                },
                "days": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": {
                            "$dateAdd": {
                                "startDate": "$login",
                                "unit": "day",
                                "amount": {
                                    "$numberInt": "1"
                                },
                                "timezone": "$location"
                            }
                        }
                    }
                },
                "hours": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": {
                            "$dateAdd": {
                                "startDate": "$login",
                                "unit": "hour",
                                "amount": {
                                    "$numberInt": "24"
                                },
                                "timezone": "$location"
                            }
                        }
                    }
                },
                "startTZInfo": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": "$login",
                        "timezone": "$location"
                    }
                },
                "daysTZInfo": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": {
                            "$dateAdd": {
                                "startDate": "$login",
                                "unit": "day",
                                "amount": {
                                    "$numberInt": "1"
                                },
                                "timezone": "$location"
                            }
                        },
                        "timezone": "$location"
                    }
                },
                "hoursTZInfo": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": {
                            "$dateAdd": {
                                "startDate": "$login",
                                "unit": "hour",
                                "amount": {
                                    "$numberInt": "24"
                                },
                                "timezone": "$location"
                            }
                        },
                        "timezone": "$location"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Elapsed Time
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/#elapsed-time
     */
    case DateDiffElapsedTime = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": null,
                "averageTime": {
                    "$avg": {
                        "$dateDiff": {
                            "startDate": "$purchased",
                            "endDate": "$delivered",
                            "unit": "day"
                        }
                    }
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "numDays": {
                    "$trunc": [
                        "$averageTime",
                        {
                            "$numberInt": "1"
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Result Precision
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/#result-precision
     */
    case DateDiffResultPrecision = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "Start": "$start",
                "End": "$end",
                "years": {
                    "$dateDiff": {
                        "startDate": "$start",
                        "endDate": "$end",
                        "unit": "year"
                    }
                },
                "months": {
                    "$dateDiff": {
                        "startDate": "$start",
                        "endDate": "$end",
                        "unit": "month"
                    }
                },
                "days": {
                    "$dateDiff": {
                        "startDate": "$start",
                        "endDate": "$end",
                        "unit": "day"
                    }
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Weeks Per Month
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/#weeks-per-month
     */
    case DateDiffWeeksPerMonth = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "wks_default": {
                    "$dateDiff": {
                        "startDate": "$start",
                        "endDate": "$end",
                        "unit": "week"
                    }
                },
                "wks_monday": {
                    "$dateDiff": {
                        "startDate": "$start",
                        "endDate": "$end",
                        "unit": "week",
                        "startOfWeek": "Monday"
                    }
                },
                "wks_friday": {
                    "$dateDiff": {
                        "startDate": "$start",
                        "endDate": "$end",
                        "unit": "week",
                        "startOfWeek": "fri"
                    }
                },
                "_id": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromParts/#example
     */
    case DateFromPartsExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "date": {
                    "$dateFromParts": {
                        "year": {
                            "$numberInt": "2017"
                        },
                        "month": {
                            "$numberInt": "2"
                        },
                        "day": {
                            "$numberInt": "8"
                        },
                        "hour": {
                            "$numberInt": "12"
                        }
                    }
                },
                "date_iso": {
                    "$dateFromParts": {
                        "isoWeekYear": {
                            "$numberInt": "2017"
                        },
                        "isoWeek": {
                            "$numberInt": "6"
                        },
                        "isoDayOfWeek": {
                            "$numberInt": "3"
                        },
                        "hour": {
                            "$numberInt": "12"
                        }
                    }
                },
                "date_timezone": {
                    "$dateFromParts": {
                        "year": {
                            "$numberInt": "2016"
                        },
                        "month": {
                            "$numberInt": "12"
                        },
                        "day": {
                            "$numberInt": "31"
                        },
                        "hour": {
                            "$numberInt": "23"
                        },
                        "minute": {
                            "$numberInt": "46"
                        },
                        "second": {
                            "$numberInt": "12"
                        },
                        "timezone": "America/New_York"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Converting Dates
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/#converting-dates
     */
    case DateFromStringConvertingDates = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "date": {
                    "$dateFromString": {
                        "dateString": "$date",
                        "timezone": "America/New_York"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * onError
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/#onerror
     */
    case DateFromStringOnError = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "date": {
                    "$dateFromString": {
                        "dateString": "$date",
                        "timezone": "$timezone",
                        "onError": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * onNull
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/#onnull
     */
    case DateFromStringOnNull = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "date": {
                    "$dateFromString": {
                        "dateString": "$date",
                        "timezone": "$timezone",
                        "onNull": {
                            "$date": {
                                "$numberLong": "0"
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Subtract A Fixed Amount
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/#subtract-a-fixed-amount
     */
    case DateSubtractSubtractAFixedAmount = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$eq": [
                        {
                            "$month": {
                                "date": "$logout"
                            }
                        },
                        {
                            "$numberInt": "1"
                        }
                    ]
                }
            }
        },
        {
            "$project": {
                "logoutTime": {
                    "$dateSubtract": {
                        "startDate": "$logout",
                        "unit": "hour",
                        "amount": {
                            "$numberInt": "3"
                        }
                    }
                }
            }
        },
        {
            "$merge": {
                "into": "connectionTime"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Filter by Relative Dates
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/#filter-by-relative-dates
     */
    case DateSubtractFilterByRelativeDates = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$gt": [
                        "$logoutTime",
                        {
                            "$dateSubtract": {
                                "startDate": "$$NOW",
                                "unit": "week",
                                "amount": {
                                    "$numberInt": "1"
                                }
                            }
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
                "custId": {
                    "$numberInt": "1"
                },
                "loggedOut": {
                    "$dateToString": {
                        "format": "%Y-%m-%d",
                        "date": "$logoutTime"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Adjust for Daylight Savings Time
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/#adjust-for-daylight-savings-time
     */
    case DateSubtractAdjustForDaylightSavingsTime = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "location": {
                    "$numberInt": "1"
                },
                "start": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": "$login"
                    }
                },
                "days": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": {
                            "$dateSubtract": {
                                "startDate": "$login",
                                "unit": "day",
                                "amount": {
                                    "$numberInt": "1"
                                },
                                "timezone": "$location"
                            }
                        }
                    }
                },
                "hours": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": {
                            "$dateSubtract": {
                                "startDate": "$login",
                                "unit": "hour",
                                "amount": {
                                    "$numberInt": "24"
                                },
                                "timezone": "$location"
                            }
                        }
                    }
                },
                "startTZInfo": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": "$login",
                        "timezone": "$location"
                    }
                },
                "daysTZInfo": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": {
                            "$dateSubtract": {
                                "startDate": "$login",
                                "unit": "day",
                                "amount": {
                                    "$numberInt": "1"
                                },
                                "timezone": "$location"
                            }
                        },
                        "timezone": "$location"
                    }
                },
                "hoursTZInfo": {
                    "$dateToString": {
                        "format": "%Y-%m-%d %H:%M",
                        "date": {
                            "$dateSubtract": {
                                "startDate": "$login",
                                "unit": "hour",
                                "amount": {
                                    "$numberInt": "24"
                                },
                                "timezone": "$location"
                            }
                        },
                        "timezone": "$location"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToParts/#example
     */
    case DateToPartsExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "date": {
                    "$dateToParts": {
                        "date": "$date"
                    }
                },
                "date_iso": {
                    "$dateToParts": {
                        "date": "$date",
                        "iso8601": true
                    }
                },
                "date_timezone": {
                    "$dateToParts": {
                        "date": "$date",
                        "timezone": "America/New_York"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToString/#example
     */
    case DateToStringExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "yearMonthDayUTC": {
                    "$dateToString": {
                        "format": "%Y-%m-%d",
                        "date": "$date"
                    }
                },
                "timewithOffsetNY": {
                    "$dateToString": {
                        "format": "%H:%M:%S:%L%z",
                        "date": "$date",
                        "timezone": "America/New_York"
                    }
                },
                "timewithOffset430": {
                    "$dateToString": {
                        "format": "%H:%M:%S:%L%z",
                        "date": "$date",
                        "timezone": "+04:30"
                    }
                },
                "minutesOffsetNY": {
                    "$dateToString": {
                        "format": "%Z",
                        "date": "$date",
                        "timezone": "America/New_York"
                    }
                },
                "minutesOffset430": {
                    "$dateToString": {
                        "format": "%Z",
                        "date": "$date",
                        "timezone": "+04:30"
                    }
                },
                "abbreviated_month": {
                    "$dateToString": {
                        "format": "%b",
                        "date": "$date",
                        "timezone": "+04:30"
                    }
                },
                "full_month": {
                    "$dateToString": {
                        "format": "%B",
                        "date": "$date",
                        "timezone": "+04:30"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Truncate Order Dates in a $project Pipeline Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateTrunc/#truncate-order-dates-in-a--project-pipeline-stage
     */
    case DateTruncTruncateOrderDatesInAProjectPipelineStage = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "1"
                },
                "orderDate": {
                    "$numberInt": "1"
                },
                "truncatedOrderDate": {
                    "$dateTrunc": {
                        "date": "$orderDate",
                        "unit": "week",
                        "binSize": {
                            "$numberInt": "2"
                        },
                        "timezone": "America/Los_Angeles",
                        "startOfWeek": "Monday"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Truncate Order Dates and Obtain Quantity Sum in a $group Pipeline Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateTrunc/#truncate-order-dates-and-obtain-quantity-sum-in-a--group-pipeline-stage
     */
    case DateTruncTruncateOrderDatesAndObtainQuantitySumInAGroupPipelineStage = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "truncatedOrderDate": {
                        "$dateTrunc": {
                            "date": "$orderDate",
                            "unit": "month",
                            "binSize": {
                                "$numberInt": "6"
                            }
                        }
                    }
                },
                "sumQuantity": {
                    "$sum": "$quantity"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfMonth/#example
     */
    case DayOfMonthExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "day": {
                    "$dayOfMonth": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfWeek/#example
     */
    case DayOfWeekExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "dayOfWeek": {
                    "$dayOfWeek": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfYear/#example
     */
    case DayOfYearExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "dayOfYear": {
                    "$dayOfYear": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/degreesToRadians/#example
     */
    case DegreesToRadiansExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "angle_a_rad": {
                    "$degreesToRadians": "$angle_a"
                },
                "angle_b_rad": {
                    "$degreesToRadians": "$angle_b"
                },
                "angle_c_rad": {
                    "$degreesToRadians": "$angle_c"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Deserialize Extended JSON Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/deserializeEJSON/#deserialize-extended-json-document
     */
    case DeserializeEJSONDeserializeExtendedJSONDocument = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "title": "Inception"
            }
        },
        {
            "$project": {
                "original": "$$ROOT",
                "serialized": {
                    "$serializeEJSON": {
                        "input": "$$ROOT"
                    }
                }
            }
        },
        {
            "$project": {
                "title": "$original.title",
                "deserialized": {
                    "$deserializeEJSON": {
                        "input": "$serialized"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Parse JSON String and Deserialize
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/deserializeEJSON/#parse-json-string-and-deserialize
     */
    case DeserializeEJSONParseJSONStringAndDeserialize = <<<'EXTENDED_JSON'
    [
        {
            "$documents": [
                {
                    "jsonData": "{\"_id\":{\"$oid\":\"507f1f77bcf86cd799439011\"},\"title\":\"The Matrix\",\"year\":{\"$numberInt\":\"1999\"},\"rating\":{\"$numberDouble\":\"8.7\"}}"
                }
            ]
        },
        {
            "$project": {
                "parsed": {
                    "$convert": {
                        "input": "$jsonData",
                        "to": "object"
                    }
                }
            }
        },
        {
            "$project": {
                "movie": {
                    "$deserializeEJSON": {
                        "input": "$parsed"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Deserialize Specific Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/deserializeEJSON/#deserialize-specific-fields
     */
    case DeserializeEJSONDeserializeSpecificFields = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "title": "Inception"
            }
        },
        {
            "$project": {
                "title": {
                    "$numberInt": "1"
                },
                "serializedMetadata": {
                    "$serializeEJSON": {
                        "input": {
                            "releaseDate": "$released",
                            "runtime": "$runtime",
                            "rating": "$imdb.rating"
                        }
                    }
                }
            }
        },
        {
            "$project": {
                "title": {
                    "$numberInt": "1"
                },
                "metadata": {
                    "$deserializeEJSON": {
                        "input": "$serializedMetadata"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use onError for Error Handling
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/deserializeEJSON/#use-onerror-for-error-handling
     */
    case DeserializeEJSONUseOnErrorForErrorHandling = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "result": {
                    "$deserializeEJSON": {
                        "input": "$ejsonField",
                        "onError": {
                            "error": "Invalid EJSON format"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/divide/#examples
     */
    case DivideExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "city": {
                    "$numberInt": "1"
                },
                "workdays": {
                    "$divide": [
                        "$hours",
                        {
                            "$numberInt": "8"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/eq/#example
     */
    case EqExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/exp/#example
     */
    case ExpExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "effectiveRate": {
                    "$subtract": [
                        {
                            "$exp": "$interestRate"
                        },
                        {
                            "$numberInt": "1"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#examples
     */
    case FilterExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Using the limit field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#use-the-limit-field
     */
    case FilterUsingTheLimitField = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * limit Greater than Possible Matches
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#limit-greater-than-possible-matches
     */
    case FilterLimitGreaterThanPossibleMatches = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use the arrayIndexAs Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/filter/#use-the-arrayindexas-field
     */
    case FilterUseTheArrayIndexAsField = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "name": {
                    "$numberInt": "1"
                },
                "secondaryHobbies": {
                    "$filter": {
                        "input": "$hobbies",
                        "arrayIndexAs": "myIndex",
                        "cond": {
                            "$eq": [
                                {
                                    "$mod": [
                                        "$$myIndex",
                                        {
                                            "$numberInt": "2"
                                        }
                                    ]
                                },
                                {
                                    "$numberInt": "0"
                                }
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /** Use in $addFields Stage */
    case FirstUseInAddFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "firstItem": {
                    "$first": "$items"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#example
     */
    case FirstNExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Using $firstN as an Aggregation Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#using--firstn-as-an-aggregation-expression
     */
    case FirstNUsingFirstNAsAnAggregationExpression = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/floor/#example
     */
    case FloorExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "value": {
                    "$numberInt": "1"
                },
                "floorValue": {
                    "$floor": "$value"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Usage Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/function/#example-1--usage-example
     */
    case FunctionUsageExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Alternative to $where
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/function/#example-2--alternative-to--where
     */
    case FunctionAlternativeToWhere = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Query Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/#query-fields-that-contain-periods--.-
     */
    case GetFieldQueryFieldsThatContainPeriods = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Query Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/#query-fields-that-start-with-a-dollar-sign----
     */
    case GetFieldQueryFieldsThatStartWithADollarSign = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Query a Field in a Sub-document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/getField/#query-a-field-in-a-sub-document
     */
    case GetFieldQueryAFieldInASubdocument = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gt/#example
     */
    case GtExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/gte/#example
     */
    case GteExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Hash a Field Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hash/#hash-a-field-value
     */
    case HashHashAFieldValue = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "filename": {
                    "$numberInt": "1"
                },
                "hash": {
                    "$hash": {
                        "input": "$filename",
                        "algorithm": "sha256"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Hash a Literal String
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hash/#hash-a-literal-string
     */
    case HashHashALiteralString = <<<'EXTENDED_JSON'
    [
        {
            "$documents": [
                {
                    "val": "hello"
                }
            ]
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "hash": {
                    "$hash": {
                        "input": "$val",
                        "algorithm": "xxh64"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Hash BinData
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hash/#hash-bindata
     */
    case HashHashBinData = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "hash": {
                    "$hash": {
                        "input": "$data",
                        "algorithm": "sha256"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Null or Missing Input
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hash/#null-or-missing-input
     */
    case HashNullOrMissingInput = <<<'EXTENDED_JSON'
    [
        {
            "$documents": [
                {
                    "val": null
                },
                {}
            ]
        },
        {
            "$project": {
                "hash": {
                    "$hash": {
                        "input": "$val",
                        "algorithm": "sha256"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Hash a Field Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hexHash/#hash-a-field-value
     */
    case HexHashHashAFieldValue = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "filename": {
                    "$numberInt": "1"
                },
                "hexHash": {
                    "$hexHash": {
                        "input": "$filename",
                        "algorithm": "sha256"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Null or Missing Input
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hexHash/#null-or-missing-input
     */
    case HexHashNullOrMissingInput = <<<'EXTENDED_JSON'
    [
        {
            "$documents": [
                {
                    "val": null
                },
                {}
            ]
        },
        {
            "$project": {
                "hexHash": {
                    "$hexHash": {
                        "input": "$val",
                        "algorithm": "sha256"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hour/#example
     */
    case HourExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "hour": {
                    "$hour": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Single Input Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ifNull/#single-input-expression
     */
    case IfNullSingleInputExpression = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Multiple Input Expressions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ifNull/#multiple-input-expressions
     */
    case IfNullMultipleInputExpressions = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/in/#example
     */
    case InExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfArray/#example
     */
    case IndexOfArrayExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfBytes/#examples
     */
    case IndexOfBytesExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "byteLocation": {
                    "$indexOfBytes": [
                        "$item",
                        "foo"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Examples
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfCP/#examples
     */
    case IndexOfCPExamples = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "cpLocation": {
                    "$indexOfCP": [
                        "$item",
                        "foo"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isArray/#example
     */
    case IsArrayExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use $isNumber to Check if a Field is Numeric
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isNumber/#use--isnumber-to-check-if-a-field-is-numeric
     */
    case IsNumberUseIsNumberToCheckIfAFieldIsNumeric = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "isNumber": {
                    "$isNumber": "$reading"
                },
                "hasType": {
                    "$type": "$reading"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Conditionally Modify Fields using $isNumber
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isNumber/#conditionally-modify-fields-using--isnumber
     */
    case IsNumberConditionallyModifyFieldsUsingIsNumber = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "points": {
                    "$cond": {
                        "if": {
                            "$isNumber": "$grade"
                        },
                        "then": "$grade",
                        "else": {
                            "$switch": {
                                "branches": [
                                    {
                                        "case": {
                                            "$eq": [
                                                "$grade",
                                                "A"
                                            ]
                                        },
                                        "then": {
                                            "$numberInt": "4"
                                        }
                                    },
                                    {
                                        "case": {
                                            "$eq": [
                                                "$grade",
                                                "B"
                                            ]
                                        },
                                        "then": {
                                            "$numberInt": "3"
                                        }
                                    },
                                    {
                                        "case": {
                                            "$eq": [
                                                "$grade",
                                                "C"
                                            ]
                                        },
                                        "then": {
                                            "$numberInt": "2"
                                        }
                                    },
                                    {
                                        "case": {
                                            "$eq": [
                                                "$grade",
                                                "D"
                                            ]
                                        },
                                        "then": {
                                            "$numberInt": "1"
                                        }
                                    },
                                    {
                                        "case": {
                                            "$eq": [
                                                "$grade",
                                                "F"
                                            ]
                                        },
                                        "then": {
                                            "$numberInt": "0"
                                        }
                                    }
                                ]
                            }
                        }
                    }
                }
            }
        },
        {
            "$group": {
                "_id": "$student_id",
                "GPA": {
                    "$avg": "$points"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoDayOfWeek/#example
     */
    case IsoDayOfWeekExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "name": "$name",
                "dayOfWeek": {
                    "$isoDayOfWeek": {
                        "date": "$birthday"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoWeek/#example
     */
    case IsoWeekExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "city": "$city",
                "weekNumber": {
                    "$isoWeek": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoWeekYear/#example
     */
    case IsoWeekYearExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "yearNumber": {
                    "$isoWeekYear": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /** Use in $addFields Stage */
    case LastUseInAddFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "lastItem": {
                    "$last": "$items"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#example
     */
    case LastNExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Using $lastN as an Aggregation Expression
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#using--lastn-as-an-aggregation-expression
     */
    case LastNUsingLastNAsAnAggregationExpression = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/let/#example
     */
    case LetExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "finalTotal": {
                    "$let": {
                        "vars": {
                            "total": {
                                "$add": [
                                    "$price",
                                    "$tax"
                                ]
                            },
                            "discounted": {
                                "$cond": {
                                    "if": "$applyDiscount",
                                    "then": {
                                        "$numberDouble": "0.9000000000000000222"
                                    },
                                    "else": {
                                        "$numberInt": "1"
                                    }
                                }
                            }
                        },
                        "in": {
                            "$multiply": [
                                "$$total",
                                "$$discounted"
                            ]
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ln/#example
     */
    case LnExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "x": "$year",
                "y": {
                    "$ln": "$sales"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log/#example
     */
    case LogExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "bitsNeeded": {
                    "$floor": {
                        "$add": [
                            {
                                "$numberInt": "1"
                            },
                            {
                                "$log": [
                                    "$int",
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log10/#example
     */
    case Log10Example = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "pH": {
                    "$multiply": [
                        {
                            "$numberInt": "-1"
                        },
                        {
                            "$log10": "$H3O"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lt/#example
     */
    case LtExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lte/#example
     */
    case LteExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ltrim/#example
     */
    case LtrimExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "description": {
                    "$ltrim": {
                        "input": "$description"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Add to Each Element of an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/#add-to-each-element-of-an-array
     */
    case MapAddToEachElementOfAnArray = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Truncate Each Array Element
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/#truncate-each-array-element
     */
    case MapTruncateEachArrayElement = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Convert Celsius Temperatures to Fahrenheit
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/#convert-celsius-temperatures-to-fahrenheit
     */
    case MapConvertCelsiusTemperaturesToFahrenheit = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use Array Index
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/map/
     */
    case MapUseArrayIndex = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "result": {
                    "$map": {
                        "input": "$scores",
                        "as": "score",
                        "arrayIndexAs": "idx",
                        "in": {
                            "$add": [
                                "$$score",
                                "$$idx"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/#use-in--project-stage
     */
    case MaxUseInProjectStage = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN-array-element/#example
     */
    case MaxNExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use $median in a $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/#use-operatorname-in-a--project-stage
     */
    case MedianUseMedianInAProjectStage = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * $mergeObjects
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/#-mergeobjects
     */
    case MergeObjectsMergeObjects = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * textScore
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/#-meta---textscore-
     */
    case MetaTextScore = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "cake"
                }
            }
        },
        {
            "$group": {
                "_id": {
                    "$meta": "textScore"
                },
                "count": {
                    "$sum": {
                        "$numberInt": "1"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * indexKey
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/#-meta---indexkey-
     */
    case MetaIndexKey = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "type": "apparel"
            }
        },
        {
            "$addFields": {
                "idxKey": {
                    "$meta": "indexKey"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/millisecond/#example
     */
    case MillisecondExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "milliseconds": {
                    "$millisecond": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/#use-in--project-stage
     */
    case MinUseInProjectStage = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN-array-element/#example
     */
    case MinNExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minute/#example
     */
    case MinuteExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "minutes": {
                    "$minute": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mod/#example
     */
    case ModExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "remainder": {
                    "$mod": [
                        "$hours",
                        "$tasks"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/month/#example
     */
    case MonthExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "month": {
                    "$month": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/multiply/#example
     */
    case MultiplyExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "date": {
                    "$numberInt": "1"
                },
                "item": {
                    "$numberInt": "1"
                },
                "total": {
                    "$multiply": [
                        "$price",
                        "$quantity"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ne/#example
     */
    case NeExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/not/#example
     */
    case NotExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * $objectToArray Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/#-objecttoarray-example
     */
    case ObjectToArrayObjectToArrayExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * $objectToArray to Sum Nested Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/objectToArray/#-objecttoarray-to-sum-nested-fields
     */
    case ObjectToArrayObjectToArrayToSumNestedFields = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/or/#example
     */
    case OrExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use $percentile in a $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/#use-operatorname-in-a--project-stage
     */
    case PercentileUsePercentileInAProjectStage = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/pow/#example
     */
    case PowExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "variance": {
                    "$pow": [
                        {
                            "$stdDevPop": [
                                "$scores.score"
                            ]
                        },
                        {
                            "$numberInt": "2"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/radiansToDegrees/#example
     */
    case RadiansToDegreesExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "angle_a_deg": {
                    "$radiansToDegrees": "$angle_a"
                },
                "angle_b_deg": {
                    "$radiansToDegrees": "$angle_b"
                },
                "angle_c_deg": {
                    "$radiansToDegrees": "$angle_c"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Generate Random Data Points
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rand/#generate-random-data-points
     */
    case RandGenerateRandomDataPoints = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Select Random Items From a Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rand/#select-random-items-from-a-collection
     */
    case RandSelectRandomItemsFromACollection = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/range/#example
     */
    case RangeExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Multiplication
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#multiplication
     */
    case ReduceMultiplication = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Discounted Merchandise
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#discounted-merchandise
     */
    case ReduceDiscountedMerchandise = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * String Concatenation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#string-concatenation
     */
    case ReduceStringConcatenation = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Array Concatenation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#array-concatenation
     */
    case ReduceArrayConcatenation = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Use as, valueAs, and arrayIndexAs
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#use-as--valueas--and-arrayindexas
     */
    case ReduceUseAsValueAsAndArrayIndexAs = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "name": {
                    "$numberInt": "1"
                },
                "text": {
                    "$reduce": {
                        "input": "$hobbies",
                        "initialValue": "My hobbies include:",
                        "in": {
                            "$concat": [
                                "$$accumulatedText",
                                " ",
                                {
                                    "$toString": {
                                        "$add": [
                                            "$$myIndex",
                                            {
                                                "$numberInt": "1"
                                            }
                                        ]
                                    }
                                },
                                ") ",
                                "$$hobby"
                            ]
                        },
                        "as": "hobby",
                        "valueAs": "accumulatedText",
                        "arrayIndexAs": "myIndex"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Computing a Multiple Reductions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reduce/#computing-a-multiple-reductions
     */
    case ReduceComputingAMultipleReductions = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * $regexFind and Its Options
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFind/#-regexfind-and-its-options
     */
    case RegexFindRegexFindAndItsOptions = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "returnObject": {
                    "$regexFind": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": ""
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * i Option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFind/#i-option
     */
    case RegexFindIOption = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "returnObject": {
                    "$regexFind": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": "i"
                            }
                        }
                    }
                }
            }
        },
        {
            "$addFields": {
                "returnObject": {
                    "$regexFind": {
                        "input": "$description",
                        "regex": "line",
                        "options": "i"
                    }
                }
            }
        },
        {
            "$addFields": {
                "returnObject": {
                    "$regexFind": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": ""
                            }
                        },
                        "options": "i"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * $regexFindAll and Its Options
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/#-regexfindall-and-its-options
     */
    case RegexFindAllRegexFindAllAndItsOptions = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "returnObject": {
                    "$regexFindAll": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": ""
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * i Option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/#i-option
     */
    case RegexFindAllIOption = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "returnObject": {
                    "$regexFindAll": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": "i"
                            }
                        }
                    }
                }
            }
        },
        {
            "$addFields": {
                "returnObject": {
                    "$regexFindAll": {
                        "input": "$description",
                        "regex": "line",
                        "options": "i"
                    }
                }
            }
        },
        {
            "$addFields": {
                "returnObject": {
                    "$regexFindAll": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": ""
                            }
                        },
                        "options": "i"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use $regexFindAll to Parse Email from String
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/#use--regexfindall-to-parse-email-from-string
     */
    case RegexFindAllUseRegexFindAllToParseEmailFromString = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "email": {
                    "$regexFindAll": {
                        "input": "$comment",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "[a-z0-9_.+-]+@[a-z0-9_.+-]+\\.[a-z0-9_.+-]+",
                                "options": "i"
                            }
                        }
                    }
                }
            }
        },
        {
            "$set": {
                "email": "$email.match"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use Captured Groupings to Parse User Name
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/#use-captured-groupings-to-parse-user-name
     */
    case RegexFindAllUseCapturedGroupingsToParseUserName = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "names": {
                    "$regexFindAll": {
                        "input": "$comment",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "([a-z0-9_.+-]+)@[a-z0-9_.+-]+\\.[a-z0-9_.+-]+",
                                "options": "i"
                            }
                        }
                    }
                }
            }
        },
        {
            "$set": {
                "names": {
                    "$reduce": {
                        "input": "$names.captures",
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
    EXTENDED_JSON;

    /**
     * $regexMatch and Its Options
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/#-regexmatch-and-its-options
     */
    case RegexMatchRegexMatchAndItsOptions = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "result": {
                    "$regexMatch": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": ""
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * i Option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/#i-option
     */
    case RegexMatchIOption = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "result": {
                    "$regexMatch": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": "i"
                            }
                        }
                    }
                }
            }
        },
        {
            "$addFields": {
                "result": {
                    "$regexMatch": {
                        "input": "$description",
                        "regex": "line",
                        "options": "i"
                    }
                }
            }
        },
        {
            "$addFields": {
                "result": {
                    "$regexMatch": {
                        "input": "$description",
                        "regex": {
                            "$regularExpression": {
                                "pattern": "line",
                                "options": ""
                            }
                        },
                        "options": "i"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use $regexMatch to Check Email Address
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/#use--regexmatch-to-check-email-address
     */
    case RegexMatchUseRegexMatchToCheckEmailAddress = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "category": {
                    "$cond": {
                        "if": {
                            "$regexMatch": {
                                "input": "$comment",
                                "regex": {
                                    "$regularExpression": {
                                        "pattern": "[a-z0-9_.+-]+@mongodb.com",
                                        "options": "i"
                                    }
                                }
                            }
                        },
                        "then": "Employee",
                        "else": "External"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Replace Using a String
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceAll/#replace-using-a-string
     */
    case ReplaceAllReplaceUsingAString = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$replaceAll": {
                        "input": "$item",
                        "find": "blue paint",
                        "replacement": "red paint"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Replace Using Regex
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceAll/#replace-using-regex
     */
    case ReplaceAllReplaceUsingRegex = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$replaceAll": {
                        "input": "$item",
                        "find": {
                            "$regularExpression": {
                                "pattern": "\\bblue paint\\b",
                                "options": ""
                            }
                        },
                        "replacement": "red paint"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceOne/#example
     */
    case ReplaceOneExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$replaceOne": {
                        "input": "$item",
                        "find": "blue paint",
                        "replacement": "red paint"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Replace Using Regex
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceOne/#replace-using-regex
     */
    case ReplaceOneReplaceUsingRegex = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$replaceOne": {
                        "input": "$item",
                        "find": {
                            "$regularExpression": {
                                "pattern": "\\bblue paint\\b",
                                "options": ""
                            }
                        },
                        "replacement": "red paint"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/reverseArray/#example
     */
    case ReverseArrayExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/round/#example
     */
    case RoundExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "roundedValue": {
                    "$round": [
                        "$value",
                        {
                            "$numberInt": "1"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rtrim/#example
     */
    case RtrimExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "description": {
                    "$rtrim": {
                        "input": "$description"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/second/#example
     */
    case SecondExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "seconds": {
                    "$second": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Canonical Extended JSON Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/serializeEJSON/#canonical-extended-json-example
     */
    case SerializeEJSONCanonicalExtendedJSONExample = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "title": "Inception"
            }
        },
        {
            "$project": {
                "ejson": {
                    "$serializeEJSON": {
                        "input": "$$ROOT"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Relaxed Extended JSON Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/serializeEJSON/#relaxed-extended-json-example
     */
    case SerializeEJSONRelaxedExtendedJSONExample = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "title": "Inception"
            }
        },
        {
            "$project": {
                "ejson": {
                    "$serializeEJSON": {
                        "input": "$$ROOT",
                        "relaxed": true
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Convert to JSON String
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/serializeEJSON/#convert-to-json-string
     */
    case SerializeEJSONConvertToJSONString = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "title": "The Godfather"
            }
        },
        {
            "$project": {
                "title": {
                    "$numberInt": "1"
                },
                "jsonString": {
                    "$toString": {
                        "$serializeEJSON": {
                            "input": "$$ROOT"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Serialize Specific Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/serializeEJSON/#serialize-specific-fields
     */
    case SerializeEJSONSerializeSpecificFields = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "year": {
                    "$gte": {
                        "$numberInt": "2010"
                    }
                }
            }
        },
        {
            "$project": {
                "title": {
                    "$numberInt": "1"
                },
                "metadataEJSON": {
                    "$serializeEJSON": {
                        "input": {
                            "releaseDate": "$released",
                            "runtime": "$runtime",
                            "imdbRating": "$imdb.rating"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use onError for Error Handling
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/serializeEJSON/#use-onerror-for-error-handling
     */
    case SerializeEJSONUseOnErrorForErrorHandling = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "title": {
                    "$numberInt": "1"
                },
                "ejson": {
                    "$serializeEJSON": {
                        "input": "$customField",
                        "onError": {
                            "error": "Serialization failed"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setDifference/#example
     */
    case SetDifferenceExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setEquals/#example
     */
    case SetEqualsExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Add Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#add-fields-that-contain-periods--.-
     */
    case SetFieldAddFieldsThatContainPeriods = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Add Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#add-fields-that-start-with-a-dollar-sign----
     */
    case SetFieldAddFieldsThatStartWithADollarSign = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Update Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#update-fields-that-contain-periods--.-
     */
    case SetFieldUpdateFieldsThatContainPeriods = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Update Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#update-fields-that-start-with-a-dollar-sign----
     */
    case SetFieldUpdateFieldsThatStartWithADollarSign = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Remove Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#remove-fields-that-contain-periods--.-
     */
    case SetFieldRemoveFieldsThatContainPeriods = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Remove Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setField/#remove-fields-that-start-with-a-dollar-sign----
     */
    case SetFieldRemoveFieldsThatStartWithADollarSign = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Elements Array Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIntersection/#elements-array-example
     */
    case SetIntersectionElementsArrayExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Retrieve Documents for Roles Granted to the Current User
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIntersection/#retrieve-documents-for-roles-granted-to-the-current-user
     */
    case SetIntersectionRetrieveDocumentsForRolesGrantedToTheCurrentUser = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setIsSubset/#example
     */
    case SetIsSubsetExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setUnion/#example
     */
    case SetUnionExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sigmoid/#examples
     */
    case SigmoidExample = <<<'EXTENDED_JSON'
    [
        {
            "$set": {
                "scaled": {
                    "$sigmoid": "$score"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/similarityCosine/#example
     */
    case SimilarityCosineExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "raw": {
                    "$similarityCosine": {
                        "vectors": [
                            "$a",
                            "$b"
                        ]
                    }
                },
                "normalized": {
                    "$similarityCosine": {
                        "vectors": [
                            "$a",
                            "$b"
                        ],
                        "score": true
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/similarityDotProduct/#example
     */
    case SimilarityDotProductExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "raw": {
                    "$similarityDotProduct": {
                        "vectors": [
                            "$a",
                            "$b"
                        ]
                    }
                },
                "normalized": {
                    "$similarityDotProduct": {
                        "vectors": [
                            "$a",
                            "$b"
                        ],
                        "score": true
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/similarityEuclidean/#example
     */
    case SimilarityEuclideanExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "raw": {
                    "$similarityEuclidean": {
                        "vectors": [
                            "$a",
                            "$b"
                        ]
                    }
                },
                "normalized": {
                    "$similarityEuclidean": {
                        "vectors": [
                            "$a",
                            "$b"
                        ],
                        "score": true
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sin/#example
     */
    case SinExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "side_b": {
                    "$multiply": [
                        {
                            "$sin": {
                                "$degreesToRadians": "$angle_a"
                            }
                        },
                        "$hypotenuse"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sinh/#example
     */
    case SinhExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "sinh_output": {
                    "$sinh": {
                        "$degreesToRadians": "$angle"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/size/#example
     */
    case SizeExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/slice/#example
     */
    case SliceExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Sort on a Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-on-a-field
     */
    case SortArraySortOnAField = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Sort on a Subfield
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-on-a-subfield
     */
    case SortArraySortOnASubfield = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Sort on Multiple Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-on-multiple-fields
     */
    case SortArraySortOnMultipleFields = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Sort an Array of Integers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-an-array-of-integers
     */
    case SortArraySortAnArrayOfIntegers = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Sort on Mixed Type Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortArray/#sort-on-mixed-type-fields
     */
    case SortArraySortOnMixedTypeFields = <<<'EXTENDED_JSON'
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
                                "$numberDecimal": "10.23"
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/split/#example
     */
    case SplitExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "city_state": {
                    "$split": [
                        "$city",
                        ", "
                    ]
                },
                "qty": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$unwind": {
                "path": "$city_state"
            }
        },
        {
            "$match": {
                "city_state": {
                    "$regularExpression": {
                        "pattern": "[A-Z]{2}",
                        "options": ""
                    }
                }
            }
        },
        {
            "$group": {
                "_id": {
                    "state": "$city_state"
                },
                "total_qty": {
                    "$sum": "$qty"
                }
            }
        },
        {
            "$sort": {
                "total_qty": {
                    "$numberInt": "-1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sqrt/#example
     */
    case SqrtExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "distance": {
                    "$sqrt": {
                        "$add": [
                            {
                                "$pow": [
                                    {
                                        "$subtract": [
                                            "$p2.y",
                                            "$p1.y"
                                        ]
                                    },
                                    {
                                        "$numberInt": "2"
                                    }
                                ]
                            },
                            {
                                "$pow": [
                                    {
                                        "$subtract": [
                                            "$p2.x",
                                            "$p1.x"
                                        ]
                                    },
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
    EXTENDED_JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/#use-in--project-stage
     */
    case StdDevPopUseInProjectStage = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Single-Byte and Multibyte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenBytes/#single-byte-and-multibyte-character-set
     */
    case StrLenBytesSingleByteAndMultibyteCharacterSet = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "length": {
                    "$strLenBytes": "$name"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Single-Byte and Multibyte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenBytes/#single-byte-and-multibyte-character-set
     */
    case StrLenCPSingleByteAndMultibyteCharacterSet = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "length": {
                    "$strLenCP": "$name"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strcasecmp/#example
     */
    case StrcasecmpExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "comparisonResult": {
                    "$strcasecmp": [
                        "$quarter",
                        "13q4"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substr/#example
     */
    case SubstrExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "yearSubstring": {
                    "$substr": [
                        "$quarter",
                        {
                            "$numberInt": "0"
                        },
                        {
                            "$numberInt": "2"
                        }
                    ]
                },
                "quarterSubtring": {
                    "$substr": [
                        "$quarter",
                        {
                            "$numberInt": "2"
                        },
                        {
                            "$numberInt": "-1"
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Single-Byte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrBytes/#single-byte-character-set
     */
    case SubstrBytesSingleByteCharacterSet = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "yearSubstring": {
                    "$substrBytes": [
                        "$quarter",
                        {
                            "$numberInt": "0"
                        },
                        {
                            "$numberInt": "2"
                        }
                    ]
                },
                "quarterSubtring": {
                    "$substrBytes": [
                        "$quarter",
                        {
                            "$numberInt": "2"
                        },
                        {
                            "$subtract": [
                                {
                                    "$strLenBytes": "$quarter"
                                },
                                {
                                    "$numberInt": "2"
                                }
                            ]
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Single-Byte and Multibyte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrBytes/#single-byte-and-multibyte-character-set
     */
    case SubstrBytesSingleByteAndMultibyteCharacterSet = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "menuCode": {
                    "$substrBytes": [
                        "$name",
                        {
                            "$numberInt": "0"
                        },
                        {
                            "$numberInt": "3"
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Single-Byte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrCP/#single-byte-character-set
     */
    case SubstrCPSingleByteCharacterSet = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "yearSubstring": {
                    "$substrCP": [
                        "$quarter",
                        {
                            "$numberInt": "0"
                        },
                        {
                            "$numberInt": "2"
                        }
                    ]
                },
                "quarterSubtring": {
                    "$substrCP": [
                        "$quarter",
                        {
                            "$numberInt": "2"
                        },
                        {
                            "$subtract": [
                                {
                                    "$strLenCP": "$quarter"
                                },
                                {
                                    "$numberInt": "2"
                                }
                            ]
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Single-Byte and Multibyte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrCP/#single-byte-and-multibyte-character-set
     */
    case SubstrCPSingleByteAndMultibyteCharacterSet = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "menuCode": {
                    "$substrCP": [
                        "$name",
                        {
                            "$numberInt": "0"
                        },
                        {
                            "$numberInt": "3"
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Subtract Numbers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/#subtract-numbers
     */
    case SubtractSubtractNumbers = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Subtract Two Dates
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/#subtract-two-dates
     */
    case SubtractSubtractTwoDates = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Subtract Milliseconds from a Date
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtract/#subtract-milliseconds-from-a-date
     */
    case SubtractSubtractMillisecondsFromADate = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/subtype/
     */
    case SubtypeExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "result": {
                    "$subtype": "$myBinDataField"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $project Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/#use-in--project-stage
     */
    case SumUseInProjectStage = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/switch/#example
     */
    case SwitchExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tan/#example
     */
    case TanExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "side_b": {
                    "$multiply": [
                        {
                            "$tan": {
                                "$degreesToRadians": "$angle_a"
                            }
                        },
                        "$side_a"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tanh/#example
     */
    case TanhExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "tanh_output": {
                    "$tanh": {
                        "$degreesToRadians": "$angle"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Convert String to Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toArray/#convert-string-to-array
     */
    case ToArrayConvertStringToArray = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "numbers": {
                    "$toArray": "[1, 2, 3]"
                },
                "documents": {
                    "$toArray": "[{\"a\": 1}, {\"b\": 2}]"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Convert binData to Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toArray/#convert-bindata-to-array
     */
    case ToArrayConvertBinDataToArray = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "original": "$v",
                "asArray": {
                    "$toArray": "$v"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toBool/#example
     */
    case ToBoolExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "convertedShippedFlag": {
                    "$switch": {
                        "branches": [
                            {
                                "case": {
                                    "$eq": [
                                        "$shipped",
                                        "false"
                                    ]
                                },
                                "then": false
                            },
                            {
                                "case": {
                                    "$eq": [
                                        "$shipped",
                                        ""
                                    ]
                                },
                                "then": false
                            }
                        ],
                        "default": {
                            "$toBool": "$shipped"
                        }
                    }
                }
            }
        },
        {
            "$match": {
                "convertedShippedFlag": false
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDate/#example
     */
    case ToDateExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "convertedDate": {
                    "$toDate": "$order_date"
                }
            }
        },
        {
            "$sort": {
                "convertedDate": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDecimal/#example
     */
    case ToDecimalExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "convertedPrice": {
                    "$toDecimal": "$price"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDouble/#example
     */
    case ToDoubleExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "degrees": {
                    "$toDouble": {
                        "$substrBytes": [
                            "$temp",
                            {
                                "$numberInt": "0"
                            },
                            {
                                "$numberInt": "4"
                            }
                        ]
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toHashedIndexKey/#example
     */
    case ToHashedIndexKeyExample = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toInt/#example
     */
    case ToIntExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "convertedQty": {
                    "$toInt": "$qty"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLong/#example
     */
    case ToLongExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "convertedQty": {
                    "$toLong": "$qty"
                }
            }
        },
        {
            "$sort": {
                "convertedQty": {
                    "$numberInt": "-1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLower/#example
     */
    case ToLowerExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$toLower": "$item"
                },
                "description": {
                    "$toLower": "$description"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Convert String to Object
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toObject/#convert-string-to-object
     */
    case ToObjectConvertStringToObject = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "parsedConfig": {
                    "$toObject": "$config"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toObjectId/#example
     */
    case ToObjectIdExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "convertedId": {
                    "$toObjectId": "$_id"
                }
            }
        },
        {
            "$sort": {
                "convertedId": {
                    "$numberInt": "-1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toString/#example
     */
    case ToStringExample = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "convertedZipCode": {
                    "$toString": "$zipcode"
                }
            }
        },
        {
            "$sort": {
                "convertedZipCode": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toUpper/#example
     */
    case ToUpperExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$toUpper": "$item"
                },
                "description": {
                    "$toUpper": "$description"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/top-array-operator/#example
     */
    case TopExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "topScore": {
                    "$top": {
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        },
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "input": "$results"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN-array-operator/#example
     */
    case TopNExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "topScores": {
                    "$topN": {
                        "n": {
                            "$numberInt": "3"
                        },
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        },
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "input": "$results"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trim/#example
     */
    case TrimExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "item": {
                    "$numberInt": "1"
                },
                "description": {
                    "$trim": {
                        "input": "$description"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trunc/#example
     */
    case TruncExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "truncatedValue": {
                    "$trunc": [
                        "$value",
                        {
                            "$numberInt": "1"
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Obtain the Incrementing Ordinal from a Timestamp Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsIncrement/#obtain-the-incrementing-ordinal-from-a-timestamp-field
     */
    case TsIncrementObtainTheIncrementingOrdinalFromATimestampField = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "saleTimestamp": {
                    "$numberInt": "1"
                },
                "saleIncrement": {
                    "$tsIncrement": "$saleTimestamp"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use $tsSecond in a Change Stream Cursor to Monitor Collection Changes
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/#use--tssecond-in-a-change-stream-cursor-to-monitor-collection-changes
     */
    case TsIncrementUseTsSecondInAChangeStreamCursorToMonitorCollectionChanges = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$expr": {
                    "$eq": [
                        {
                            "$mod": [
                                {
                                    "$tsIncrement": "$clusterTime"
                                },
                                {
                                    "$numberInt": "2"
                                }
                            ]
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
     * Obtain the Number of Seconds from a Timestamp Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/#obtain-the-number-of-seconds-from-a-timestamp-field
     */
    case TsSecondObtainTheNumberOfSecondsFromATimestampField = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "saleTimestamp": {
                    "$numberInt": "1"
                },
                "saleSeconds": {
                    "$tsSecond": "$saleTimestamp"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use $tsSecond in a Change Stream Cursor to Monitor Collection Changes
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/#use--tssecond-in-a-change-stream-cursor-to-monitor-collection-changes
     */
    case TsSecondUseTsSecondInAChangeStreamCursorToMonitorCollectionChanges = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "clusterTimeSeconds": {
                    "$tsSecond": "$clusterTime"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/type/#example
     */
    case TypeExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "a": {
                    "$type": "$a"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Remove Fields that Contain Periods
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/#remove-fields-that-contain-periods--.-
     */
    case UnsetFieldRemoveFieldsThatContainPeriods = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Remove Fields that Start with a Dollar Sign
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/#remove-fields-that-start-with-a-dollar-sign----
     */
    case UnsetFieldRemoveFieldsThatStartWithADollarSign = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Remove A Subfield
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unsetField/#remove-a-subfield
     */
    case UnsetFieldRemoveASubfield = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/week/#example
     */
    case WeekExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "week": {
                    "$week": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/year/#example
     */
    case YearExample = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "year": {
                    "$year": {
                        "date": "$date"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Matrix Transposition
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/zip/#matrix-transposition
     */
    case ZipMatrixTransposition = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;

    /**
     * Filtering and Preserving Indexes
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/zip/#filtering-and-preserving-indexes
     */
    case ZipFilteringAndPreservingIndexes = <<<'EXTENDED_JSON'
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
    EXTENDED_JSON;
}
