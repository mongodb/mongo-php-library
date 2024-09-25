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
    case AbsExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acos/#example
     */
    case AcosExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/acosh/#example
     */
    case AcoshExample = <<<'JSON'
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
    JSON;

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
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asin/#example
     */
    case AsinExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/asinh/#example
     */
    case AsinhExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atan/#example
     */
    case AtanExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atan2/#example
     */
    case Atan2Example = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/atanh/#example
     */
    case AtanhExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ceil/#example
     */
    case CeilExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concat/#examples
     */
    case ConcatExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/convert/#example
     */
    case ConvertExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cos/#example
     */
    case CosExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/cosh/#example
     */
    case CoshExample = <<<'JSON'
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
    JSON;

    /**
     * Add a Future Date
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/#add-a-future-date
     */
    case DateAddAddAFutureDate = <<<'JSON'
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
    JSON;

    /**
     * Filter on a Date Range
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/#filter-on-a-date-range
     */
    case DateAddFilterOnADateRange = <<<'JSON'
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
    JSON;

    /**
     * Adjust for Daylight Savings Time
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateAdd/#adjust-for-daylight-savings-time
     */
    case DateAddAdjustForDaylightSavingsTime = <<<'JSON'
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
    JSON;

    /**
     * Elapsed Time
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/#elapsed-time
     */
    case DateDiffElapsedTime = <<<'JSON'
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
    JSON;

    /**
     * Result Precision
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/#result-precision
     */
    case DateDiffResultPrecision = <<<'JSON'
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
    JSON;

    /**
     * Weeks Per Month
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateDiff/#weeks-per-month
     */
    case DateDiffWeeksPerMonth = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromParts/#example
     */
    case DateFromPartsExample = <<<'JSON'
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
    JSON;

    /**
     * Converting Dates
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/#converting-dates
     */
    case DateFromStringConvertingDates = <<<'JSON'
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
    JSON;

    /**
     * onError
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/#onerror
     */
    case DateFromStringOnError = <<<'JSON'
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
    JSON;

    /**
     * onNull
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateFromString/#onnull
     */
    case DateFromStringOnNull = <<<'JSON'
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
    JSON;

    /**
     * Subtract A Fixed Amount
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/#subtract-a-fixed-amount
     */
    case DateSubtractSubtractAFixedAmount = <<<'JSON'
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
    JSON;

    /**
     * Filter by Relative Dates
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/#filter-by-relative-dates
     */
    case DateSubtractFilterByRelativeDates = <<<'JSON'
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
    JSON;

    /**
     * Adjust for Daylight Savings Time
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateSubtract/#adjust-for-daylight-savings-time
     */
    case DateSubtractAdjustForDaylightSavingsTime = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToParts/#example
     */
    case DateToPartsExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateToString/#example
     */
    case DateToStringExample = <<<'JSON'
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
    JSON;

    /**
     * Truncate Order Dates in a $project Pipeline Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateTrunc/#truncate-order-dates-in-a--project-pipeline-stage
     */
    case DateTruncTruncateOrderDatesInAProjectPipelineStage = <<<'JSON'
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
    JSON;

    /**
     * Truncate Order Dates and Obtain Quantity Sum in a $group Pipeline Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dateTrunc/#truncate-order-dates-and-obtain-quantity-sum-in-a--group-pipeline-stage
     */
    case DateTruncTruncateOrderDatesAndObtainQuantitySumInAGroupPipelineStage = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfMonth/#example
     */
    case DayOfMonthExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfWeek/#example
     */
    case DayOfWeekExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/dayOfYear/#example
     */
    case DayOfYearExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/degreesToRadians/#example
     */
    case DegreesToRadiansExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/divide/#example
     */
    case DivideExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/exp/#example
     */
    case ExpExample = <<<'JSON'
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
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/floor/#example
     */
    case FloorExample = <<<'JSON'
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
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/hour/#example
     */
    case HourExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfBytes/#examples
     */
    case IndexOfBytesExample = <<<'JSON'
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
    JSON;

    /**
     * Examples
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexOfCP/#examples
     */
    case IndexOfCPExamples = <<<'JSON'
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

    /**
     * Use $isNumber to Check if a Field is Numeric
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isNumber/#use--isnumber-to-check-if-a-field-is-numeric
     */
    case IsNumberUseIsNumberToCheckIfAFieldIsNumeric = <<<'JSON'
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
    JSON;

    /**
     * Conditionally Modify Fields using $isNumber
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isNumber/#conditionally-modify-fields-using--isnumber
     */
    case IsNumberConditionallyModifyFieldsUsingIsNumber = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoDayOfWeek/#example
     */
    case IsoDayOfWeekExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoWeek/#example
     */
    case IsoWeekExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/isoWeekYear/#example
     */
    case IsoWeekYearExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/let/#example
     */
    case LetExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ln/#example
     */
    case LnExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log/#example
     */
    case LogExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/log10/#example
     */
    case Log10Example = <<<'JSON'
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
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/ltrim/#example
     */
    case LtrimExample = <<<'JSON'
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
     * textScore
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/#-meta---textscore-
     */
    case MetaTextScore = <<<'JSON'
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
    JSON;

    /**
     * indexKey
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/meta/#-meta---indexkey-
     */
    case MetaIndexKey = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/millisecond/#example
     */
    case MillisecondExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minute/#example
     */
    case MinuteExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mod/#example
     */
    case ModExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/month/#example
     */
    case MonthExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/multiply/#example
     */
    case MultiplyExample = <<<'JSON'
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
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/pow/#example
     */
    case PowExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/radiansToDegrees/#example
     */
    case RadiansToDegreesExample = <<<'JSON'
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
     * $regexFind and Its Options
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFind/#-regexfind-and-its-options
     */
    case RegexFindRegexFindAndItsOptions = <<<'JSON'
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
    JSON;

    /**
     * i Option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFind/#i-option
     */
    case RegexFindIOption = <<<'JSON'
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
    JSON;

    /**
     * $regexFindAll and Its Options
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/#-regexfindall-and-its-options
     */
    case RegexFindAllRegexFindAllAndItsOptions = <<<'JSON'
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
    JSON;

    /**
     * i Option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/#i-option
     */
    case RegexFindAllIOption = <<<'JSON'
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
    JSON;

    /**
     * Use $regexFindAll to Parse Email from String
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/#use--regexfindall-to-parse-email-from-string
     */
    case RegexFindAllUseRegexFindAllToParseEmailFromString = <<<'JSON'
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
    JSON;

    /**
     * Use Captured Groupings to Parse User Name
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexFindAll/#use-captured-groupings-to-parse-user-name
     */
    case RegexFindAllUseCapturedGroupingsToParseUserName = <<<'JSON'
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
    JSON;

    /**
     * $regexMatch and Its Options
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/#-regexmatch-and-its-options
     */
    case RegexMatchRegexMatchAndItsOptions = <<<'JSON'
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
    JSON;

    /**
     * i Option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/#i-option
     */
    case RegexMatchIOption = <<<'JSON'
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
    JSON;

    /**
     * Use $regexMatch to Check Email Address
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/regexMatch/#use--regexmatch-to-check-email-address
     */
    case RegexMatchUseRegexMatchToCheckEmailAddress = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceAll/#example
     */
    case ReplaceAllExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceOne/#example
     */
    case ReplaceOneExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/round/#example
     */
    case RoundExample = <<<'JSON'
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
    JSON;

    /** Round Average Rating */
    case RoundRoundAverageRating = <<<'JSON'
    [
        {
            "$project": {
                "roundedAverageRating": {
                    "$avg": [
                        {
                            "$round": [
                                {
                                    "$avg": [
                                        "$averageRating"
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
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rtrim/#example
     */
    case RtrimExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/second/#example
     */
    case SecondExample = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sin/#example
     */
    case SinExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sinh/#example
     */
    case SinhExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/split/#example
     */
    case SplitExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sqrt/#example
     */
    case SqrtExample = <<<'JSON'
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
     * Single-Byte and Multibyte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenBytes/#single-byte-and-multibyte-character-set
     */
    case StrLenBytesSingleByteAndMultibyteCharacterSet = <<<'JSON'
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
    JSON;

    /**
     * Single-Byte and Multibyte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strLenBytes/#single-byte-and-multibyte-character-set
     */
    case StrLenCPSingleByteAndMultibyteCharacterSet = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/strcasecmp/#example
     */
    case StrcasecmpExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substr/#example
     */
    case SubstrExample = <<<'JSON'
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
    JSON;

    /**
     * Single-Byte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrBytes/#single-byte-character-set
     */
    case SubstrBytesSingleByteCharacterSet = <<<'JSON'
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
    JSON;

    /**
     * Single-Byte and Multibyte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrBytes/#single-byte-and-multibyte-character-set
     */
    case SubstrBytesSingleByteAndMultibyteCharacterSet = <<<'JSON'
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
    JSON;

    /**
     * Single-Byte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrCP/#single-byte-character-set
     */
    case SubstrCPSingleByteCharacterSet = <<<'JSON'
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
    JSON;

    /**
     * Single-Byte and Multibyte Character Set
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/substrCP/#single-byte-and-multibyte-character-set
     */
    case SubstrCPSingleByteAndMultibyteCharacterSet = <<<'JSON'
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tan/#example
     */
    case TanExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tanh/#example
     */
    case TanhExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toBool/#example
     */
    case ToBoolExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDate/#example
     */
    case ToDateExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDecimal/#example
     */
    case ToDecimalExample = <<<'JSON'
    [
        {
            "$addFields": {
                "convertedPrice": {
                    "$toDecimal": "$price"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toDouble/#example
     */
    case ToDoubleExample = <<<'JSON'
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
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toInt/#example
     */
    case ToIntExample = <<<'JSON'
    [
        {
            "$addFields": {
                "convertedQty": {
                    "$toInt": "$qty"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLong/#example
     */
    case ToLongExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toLower/#example
     */
    case ToLowerExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toObjectId/#example
     */
    case ToObjectIdExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toString/#example
     */
    case ToStringExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/toUpper/#example
     */
    case ToUpperExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trim/#example
     */
    case TrimExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/trunc/#example
     */
    case TruncExample = <<<'JSON'
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
    JSON;

    /**
     * Obtain the Incrementing Ordinal from a Timestamp Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsIncrement/#obtain-the-incrementing-ordinal-from-a-timestamp-field
     */
    case TsIncrementObtainTheIncrementingOrdinalFromATimestampField = <<<'JSON'
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
    JSON;

    /**
     * Use $tsSecond in a Change Stream Cursor to Monitor Collection Changes
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/#use--tssecond-in-a-change-stream-cursor-to-monitor-collection-changes
     */
    case TsIncrementUseTsSecondInAChangeStreamCursorToMonitorCollectionChanges = <<<'JSON'
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
    JSON;

    /**
     * Obtain the Number of Seconds from a Timestamp Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/#obtain-the-number-of-seconds-from-a-timestamp-field
     */
    case TsSecondObtainTheNumberOfSecondsFromATimestampField = <<<'JSON'
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
    JSON;

    /**
     * Use $tsSecond in a Change Stream Cursor to Monitor Collection Changes
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/tsSecond/#use--tssecond-in-a-change-stream-cursor-to-monitor-collection-changes
     */
    case TsSecondUseTsSecondInAChangeStreamCursorToMonitorCollectionChanges = <<<'JSON'
    [
        {
            "$addFields": {
                "clusterTimeSeconds": {
                    "$tsSecond": "$clusterTime"
                }
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/type/#example
     */
    case TypeExample = <<<'JSON'
    [
        {
            "$project": {
                "a": {
                    "$type": "$a"
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
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/week/#example
     */
    case WeekExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/year/#example
     */
    case YearExample = <<<'JSON'
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
