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
