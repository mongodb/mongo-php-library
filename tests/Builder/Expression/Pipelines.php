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
