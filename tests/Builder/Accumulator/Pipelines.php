<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Accumulator;

enum Pipelines: string
{
    /**
     * Use $accumulator to Implement the $avg Operator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/accumulator/#use--accumulator-to-implement-the--avg-operator
     */
    case AccumulatorUseAccumulatorToImplementTheAvgOperator = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$author",
                "avgCopies": {
                    "$accumulator": {
                        "init": {
                            "$code": "function() {\n    return { count: 0, sum: 0 }\n}"
                        },
                        "accumulate": {
                            "$code": "function(state, numCopies) {\n    return { count: state.count + 1, sum: state.sum + numCopies }\n}"
                        },
                        "accumulateArgs": [
                            "$copies"
                        ],
                        "merge": {
                            "$code": "function(state1, state2) {\n    return {\n        count: state1.count + state2.count,\n        sum: state1.sum + state2.sum\n    }\n}"
                        },
                        "finalize": {
                            "$code": "function(state) {\n    return (state.sum / state.count)\n}"
                        },
                        "lang": "js"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use initArgs to Vary the Initial State by Group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/accumulator/#use-initargs-to-vary-the-initial-state-by-group
     */
    case AccumulatorUseInitArgsToVaryTheInitialStateByGroup = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "city": "$city"
                },
                "restaurants": {
                    "$accumulator": {
                        "init": {
                            "$code": "function(city, userProfileCity) {\n    return { max: city === userProfileCity ? 3 : 1, restaurants: [] }\n}"
                        },
                        "initArgs": [
                            "$city",
                            "Bettles"
                        ],
                        "accumulate": {
                            "$code": "function(state, restaurantName) {\n    if (state.restaurants.length < state.max) {\n        state.restaurants.push(restaurantName);\n    }\n    return state;\n}"
                        },
                        "accumulateArgs": [
                            "$name"
                        ],
                        "merge": {
                            "$code": "function(state1, state2) {\n    return {\n        max: state1.max,\n        restaurants: state1.restaurants.concat(state2.restaurants).slice(0, state1.max)\n    }\n}"
                        },
                        "finalize": {
                            "$code": "function(state) {\n    return state.restaurants\n}"
                        },
                        "lang": "js"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addToSet/#use-in--group-stage
     */
    case AddToSetUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "day": {
                        "$dayOfYear": {
                            "date": "$date"
                        }
                    },
                    "year": {
                        "$year": {
                            "date": "$date"
                        }
                    }
                },
                "itemsSold": {
                    "$addToSet": "$item"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addToSet/#use-in--setwindowfields-stage
     */
    case AddToSetUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "cakeTypesForState": {
                        "$addToSet": "$type",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/#use-in--group-stage
     */
    case AvgUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$item",
                "avgAmount": {
                    "$avg": {
                        "$multiply": [
                            "$price",
                            "$quantity"
                        ]
                    }
                },
                "avgQuantity": {
                    "$avg": "$quantity"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/#use-in--setwindowfields-stage
     */
    case AvgUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "averageQuantityForState": {
                        "$avg": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Find the Bottom Score
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom/#find-the-bottom-score
     */
    case BottomFindTheBottomScore = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "gameId": "G1"
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$bottom": {
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "sortBy": {
                            "score": {
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
     * Finding the Bottom Score Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom/#finding-the-bottom-score-across-multiple-games
     */
    case BottomFindingTheBottomScoreAcrossMultipleGames = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$bottom": {
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "sortBy": {
                            "score": {
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
     * Find the Three Lowest Scores
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/#find-the-three-lowest-scores
     */
    case BottomNFindTheThreeLowestScores = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "gameId": "G1"
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$bottomN": {
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        },
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
     * Finding the Three Lowest Score Documents Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/#finding-the-three-lowest-score-documents-across-multiple-games
     */
    case BottomNFindingTheThreeLowestScoreDocumentsAcrossMultipleGames = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$bottomN": {
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        },
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
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/#computing-n-based-on-the-group-key-for--group
     */
    case BottomNComputingNBasedOnTheGroupKeyForGroup = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "gameId": "$gameId"
                },
                "gamescores": {
                    "$bottomN": {
                        "output": "$score",
                        "n": {
                            "$cond": {
                                "if": {
                                    "$eq": [
                                        "$gameId",
                                        "G2"
                                    ]
                                },
                                "then": {
                                    "$numberInt": "1"
                                },
                                "else": {
                                    "$numberInt": "3"
                                }
                            }
                        },
                        "sortBy": {
                            "score": {
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
     * Warehouse collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/concatArrays/#example
     */
    case ConcatArraysWarehouseCollection = <<<'EXTENDED_JSON'
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
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count-accumulator/#use-in--group-stage
     */
    case CountUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$state",
                "countNumberOfDocumentsForState": {
                    "$count": {}
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count-accumulator/#use-in--setwindowfields-stage
     */
    case CountUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "countNumberOfDocumentsForState": {
                        "$count": {},
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covariancePop/#example
     */
    case CovariancePopExample = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "covariancePopForState": {
                        "$covariancePop": [
                            {
                                "$year": {
                                    "date": "$orderDate"
                                }
                            },
                            "$quantity"
                        ],
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covarianceSamp/#example
     */
    case CovarianceSampExample = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "covarianceSampForState": {
                        "$covarianceSamp": [
                            {
                                "$year": {
                                    "date": "$orderDate"
                                }
                            },
                            "$quantity"
                        ],
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Dense Rank Partitions by an Integer Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/denseRank/#dense-rank-partitions-by-an-integer-field
     */
    case DenseRankDenseRankPartitionsByAnIntegerField = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "quantity": {
                        "$numberInt": "-1"
                    }
                },
                "output": {
                    "denseRankQuantityForState": {
                        "$denseRank": {}
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Dense Rank Partitions by a Date Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/denseRank/#dense-rank-partitions-by-a-date-field
     */
    case DenseRankDenseRankPartitionsByADateField = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "denseRankOrderDateForState": {
                        "$denseRank": {}
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/derivative/#example
     */
    case DerivativeExample = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$truckID",
                "sortBy": {
                    "timeStamp": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "truckAverageSpeed": {
                        "$derivative": {
                            "input": "$miles",
                            "unit": "hour"
                        },
                        "window": {
                            "range": [
                                {
                                    "$numberInt": "-30"
                                },
                                {
                                    "$numberInt": "0"
                                }
                            ],
                            "unit": "second"
                        }
                    }
                }
            }
        },
        {
            "$match": {
                "truckAverageSpeed": {
                    "$gt": {
                        "$numberInt": "50"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Document Number for Each State
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documentNumber/#document-number-for-each-state
     */
    case DocumentNumberDocumentNumberForEachState = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "quantity": {
                        "$numberInt": "-1"
                    }
                },
                "output": {
                    "documentNumberForState": {
                        "$documentNumber": {}
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Exponential Moving Average Using N
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/expMovingAvg/#exponential-moving-average-using-n
     */
    case ExpMovingAvgExponentialMovingAverageUsingN = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$stock",
                "sortBy": {
                    "date": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "expMovingAvgForStock": {
                        "$expMovingAvg": {
                            "input": "$price",
                            "N": {
                                "$numberInt": "2"
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Exponential Moving Average Using alpha
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/expMovingAvg/#exponential-moving-average-using-alpha
     */
    case ExpMovingAvgExponentialMovingAverageUsingAlpha = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$stock",
                "sortBy": {
                    "date": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "expMovingAvgForStock": {
                        "$expMovingAvg": {
                            "input": "$price",
                            "alpha": {
                                "$numberDouble": "0.75"
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/first/#use-in--group-stage
     */
    case FirstUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$sort": {
                "item": {
                    "$numberInt": "1"
                },
                "date": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$group": {
                "_id": "$item",
                "firstSale": {
                    "$first": "$date"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/first/#use-in--setwindowfields-stage
     */
    case FirstUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "firstOrderTypeForState": {
                        "$first": "$type",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Null and Missing Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#null-and-missing-values
     */
    case FirstNNullAndMissingValues = <<<'EXTENDED_JSON'
    [
        {
            "$documents": [
                {
                    "playerId": "PlayerA",
                    "gameId": "G1",
                    "score": {
                        "$numberInt": "1"
                    }
                },
                {
                    "playerId": "PlayerB",
                    "gameId": "G1",
                    "score": {
                        "$numberInt": "2"
                    }
                },
                {
                    "playerId": "PlayerC",
                    "gameId": "G1",
                    "score": {
                        "$numberInt": "3"
                    }
                },
                {
                    "playerId": "PlayerD",
                    "gameId": "G1"
                },
                {
                    "playerId": "PlayerE",
                    "gameId": "G1",
                    "score": null
                }
            ]
        },
        {
            "$group": {
                "_id": "$gameId",
                "firstFiveScores": {
                    "$firstN": {
                        "input": "$score",
                        "n": {
                            "$numberInt": "5"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Find the First Three Player Scores for a Single Game
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#find-the-first-three-player-scores-for-a-single-game
     */
    case FirstNFindTheFirstThreePlayerScoresForASingleGame = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "gameId": "G1"
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "firstThreeScores": {
                    "$firstN": {
                        "input": [
                            "$playerId",
                            "$score"
                        ],
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
     * Finding the First Three Player Scores Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#finding-the-first-three-player-scores-across-multiple-games
     */
    case FirstNFindingTheFirstThreePlayerScoresAcrossMultipleGames = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$firstN": {
                        "input": [
                            "$playerId",
                            "$score"
                        ],
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
     * Using $sort With $firstN
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#using--sort-with--firstn
     */
    case FirstNUsingSortWithFirstN = <<<'EXTENDED_JSON'
    [
        {
            "$sort": {
                "score": {
                    "$numberInt": "-1"
                }
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$firstN": {
                        "input": [
                            "$playerId",
                            "$score"
                        ],
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
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#computing-n-based-on-the-group-key-for--group
     */
    case FirstNComputingNBasedOnTheGroupKeyForGroup = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "gameId": "$gameId"
                },
                "gamescores": {
                    "$firstN": {
                        "input": "$score",
                        "n": {
                            "$cond": {
                                "if": {
                                    "$eq": [
                                        "$gameId",
                                        "G2"
                                    ]
                                },
                                "then": {
                                    "$numberInt": "1"
                                },
                                "else": {
                                    "$numberInt": "3"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/integral/#example
     */
    case IntegralExample = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$powerMeterID",
                "sortBy": {
                    "timeStamp": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "powerMeterKilowattHours": {
                        "$integral": {
                            "input": "$kilowatts",
                            "unit": "hour"
                        },
                        "window": {
                            "range": [
                                "unbounded",
                                "current"
                            ],
                            "unit": "hour"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/#use-in--group-stage
     */
    case LastUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$sort": {
                "item": {
                    "$numberInt": "1"
                },
                "date": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$group": {
                "_id": "$item",
                "lastSalesDate": {
                    "$last": "$date"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/#use-in--setwindowfields-stage
     */
    case LastUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "lastOrderTypeForState": {
                        "$last": "$type",
                        "window": {
                            "documents": [
                                "current",
                                "unbounded"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Find the Last Three Player Scores for a Single Game
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#find-the-last-three-player-scores-for-a-single-game
     */
    case LastNFindTheLastThreePlayerScoresForASingleGame = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "gameId": "G1"
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "lastThreeScores": {
                    "$lastN": {
                        "input": [
                            "$playerId",
                            "$score"
                        ],
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
     * Finding the Last Three Player Scores Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#finding-the-last-three-player-scores-across-multiple-games
     */
    case LastNFindingTheLastThreePlayerScoresAcrossMultipleGames = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$lastN": {
                        "input": [
                            "$playerId",
                            "$score"
                        ],
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
     * Using $sort With $lastN
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#using--sort-with--lastn
     */
    case LastNUsingSortWithLastN = <<<'EXTENDED_JSON'
    [
        {
            "$sort": {
                "score": {
                    "$numberInt": "-1"
                }
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$lastN": {
                        "input": [
                            "$playerId",
                            "$score"
                        ],
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
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#computing-n-based-on-the-group-key-for--group
     */
    case LastNComputingNBasedOnTheGroupKeyForGroup = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "gameId": "$gameId"
                },
                "gamescores": {
                    "$lastN": {
                        "input": "$score",
                        "n": {
                            "$cond": {
                                "if": {
                                    "$eq": [
                                        "$gameId",
                                        "G2"
                                    ]
                                },
                                "then": {
                                    "$numberInt": "1"
                                },
                                "else": {
                                    "$numberInt": "3"
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
     * Fill Missing Values with Linear Interpolation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/linearFill/#fill-missing-values-with-linear-interpolation
     */
    case LinearFillFillMissingValuesWithLinearInterpolation = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "sortBy": {
                    "time": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "price": {
                        "$linearFill": "$price"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use Multiple Fill Methods in a Single Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/linearFill/#use-multiple-fill-methods-in-a-single-stage
     */
    case LinearFillUseMultipleFillMethodsInASingleStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "sortBy": {
                    "time": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "linearFillPrice": {
                        "$linearFill": "$price"
                    },
                    "locfPrice": {
                        "$locf": "$price"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fill Missing Values with the Last Observed Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/locf/#fill-missing-values-with-the-last-observed-value
     */
    case LocfFillMissingValuesWithTheLastObservedValue = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "sortBy": {
                    "time": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "price": {
                        "$locf": "$price"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/#use-in--group-stage
     */
    case MaxUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$item",
                "maxTotalAmount": {
                    "$max": {
                        "$multiply": [
                            "$price",
                            "$quantity"
                        ]
                    }
                },
                "maxQuantity": {
                    "$max": "$quantity"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/#use-in--setwindowfields-stage
     */
    case MaxUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "maximumQuantityForState": {
                        "$max": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Find the Maximum Three Scores for a Single Game
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN/#find-the-maximum-three-scores-for-a-single-game
     */
    case MaxNFindTheMaximumThreeScoresForASingleGame = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "gameId": "G1"
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "maxThreeScores": {
                    "$maxN": {
                        "input": [
                            "$score",
                            "$playerId"
                        ],
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
     * Finding the Maximum Three Scores Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN/#finding-the-maximum-three-scores-across-multiple-games
     */
    case MaxNFindingTheMaximumThreeScoresAcrossMultipleGames = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$gameId",
                "maxScores": {
                    "$maxN": {
                        "input": [
                            "$score",
                            "$playerId"
                        ],
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
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN/#computing-n-based-on-the-group-key-for--group
     */
    case MaxNComputingNBasedOnTheGroupKeyForGroup = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "gameId": "$gameId"
                },
                "gamescores": {
                    "$maxN": {
                        "input": [
                            "$score",
                            "$playerId"
                        ],
                        "n": {
                            "$cond": {
                                "if": {
                                    "$eq": [
                                        "$gameId",
                                        "G2"
                                    ]
                                },
                                "then": {
                                    "$numberInt": "1"
                                },
                                "else": {
                                    "$numberInt": "3"
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
     * Use $median as an Accumulator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/#use-operatorname-as-an-accumulator
     */
    case MedianUseMedianAsAnAccumulator = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": null,
                "test01_median": {
                    "$median": {
                        "input": "$test01",
                        "method": "approximate"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use $median in a $setWindowField Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/#use-operatorname-in-a--setwindowfield-stage
     */
    case MedianUseMedianInASetWindowFieldStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "sortBy": {
                    "test01": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "test01_median": {
                        "$median": {
                            "input": "$test01",
                            "method": "approximate"
                        },
                        "window": {
                            "range": [
                                {
                                    "$numberInt": "-3"
                                },
                                {
                                    "$numberInt": "3"
                                }
                            ]
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
                "studentId": {
                    "$numberInt": "1"
                },
                "test01_median": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * $mergeObjects as an Accumulator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/#-mergeobjects-as-an-accumulator
     */
    case MergeObjectsMergeObjectsAsAnAccumulator = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$item",
                "mergedSales": {
                    "$mergeObjects": "$quantity"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/#use-in--group-stage
     */
    case MinUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$item",
                "minQuantity": {
                    "$min": "$quantity"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/#use-in--setwindowfields-stage
     */
    case MinUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "minimumQuantityForState": {
                        "$min": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Normalize values with custom range
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minMaxScaler/#examples
     */
    case MinMaxScalerNormalizeValuesWithCustomRange = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "sortBy": {
                    "a": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "scaled": {
                        "$minMaxScaler": {
                            "input": "$a"
                        }
                    },
                    "scaledTo100": {
                        "$minMaxScaler": {
                            "input": "$a",
                            "min": {
                                "$numberInt": "0"
                            },
                            "max": {
                                "$numberInt": "100"
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Find the Minimum Three Scores for a Single Game
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN/#find-the-minimum-three-scores-for-a-single-game
     */
    case MinNFindTheMinimumThreeScoresForASingleGame = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "gameId": "G1"
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "minScores": {
                    "$minN": {
                        "input": [
                            "$score",
                            "$playerId"
                        ],
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
     * Finding the Minimum Three Documents Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN/#finding-the-minimum-three-documents-across-multiple-games
     */
    case MinNFindingTheMinimumThreeDocumentsAcrossMultipleGames = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$gameId",
                "minScores": {
                    "$minN": {
                        "input": [
                            "$score",
                            "$playerId"
                        ],
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
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN/#computing-n-based-on-the-group-key-for--group
     */
    case MinNComputingNBasedOnTheGroupKeyForGroup = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "gameId": "$gameId"
                },
                "gamescores": {
                    "$minN": {
                        "input": [
                            "$score",
                            "$playerId"
                        ],
                        "n": {
                            "$cond": {
                                "if": {
                                    "$eq": [
                                        "$gameId",
                                        "G2"
                                    ]
                                },
                                "then": {
                                    "$numberInt": "1"
                                },
                                "else": {
                                    "$numberInt": "3"
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
     * Calculate a Single Value as an Accumulator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/#calculate-a-single-value-as-an-accumulator
     */
    case PercentileCalculateASingleValueAsAnAccumulator = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": null,
                "test01_percentiles": {
                    "$percentile": {
                        "input": "$test01",
                        "p": [
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
     * Calculate Multiple Values as an Accumulator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/#calculate-multiple-values-as-an-accumulator
     */
    case PercentileCalculateMultipleValuesAsAnAccumulator = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": null,
                "test01_percentiles": {
                    "$percentile": {
                        "input": "$test01",
                        "p": [
                            {
                                "$numberDouble": "0.5"
                            },
                            {
                                "$numberDouble": "0.75"
                            },
                            {
                                "$numberDouble": "0.9000000000000000222"
                            },
                            {
                                "$numberDouble": "0.94999999999999995559"
                            }
                        ],
                        "method": "approximate"
                    }
                },
                "test02_percentiles": {
                    "$percentile": {
                        "input": "$test02",
                        "p": [
                            {
                                "$numberDouble": "0.5"
                            },
                            {
                                "$numberDouble": "0.75"
                            },
                            {
                                "$numberDouble": "0.9000000000000000222"
                            },
                            {
                                "$numberDouble": "0.94999999999999995559"
                            }
                        ],
                        "method": "approximate"
                    }
                },
                "test03_percentiles": {
                    "$percentile": {
                        "input": "$test03",
                        "p": [
                            {
                                "$numberDouble": "0.5"
                            },
                            {
                                "$numberDouble": "0.75"
                            },
                            {
                                "$numberDouble": "0.9000000000000000222"
                            },
                            {
                                "$numberDouble": "0.94999999999999995559"
                            }
                        ],
                        "method": "approximate"
                    }
                },
                "test03_percent_alt": {
                    "$percentile": {
                        "input": "$test03",
                        "p": [
                            {
                                "$numberDouble": "0.9000000000000000222"
                            },
                            {
                                "$numberDouble": "0.5"
                            },
                            {
                                "$numberDouble": "0.75"
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
     * Use $percentile in a $setWindowField Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/#use-operatorname-in-a--setwindowfield-stage
     */
    case PercentileUsePercentileInASetWindowFieldStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "sortBy": {
                    "test01": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "test01_95percentile": {
                        "$percentile": {
                            "input": "$test01",
                            "p": [
                                {
                                    "$numberDouble": "0.94999999999999995559"
                                }
                            ],
                            "method": "approximate"
                        },
                        "window": {
                            "range": [
                                {
                                    "$numberInt": "-3"
                                },
                                {
                                    "$numberInt": "3"
                                }
                            ]
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
                "studentId": {
                    "$numberInt": "1"
                },
                "test01_95percentile": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/push/#use-in--group-stage
     */
    case PushUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$sort": {
                "date": {
                    "$numberInt": "1"
                },
                "item": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$group": {
                "_id": {
                    "day": {
                        "$dayOfYear": {
                            "date": "$date"
                        }
                    },
                    "year": {
                        "$year": {
                            "date": "$date"
                        }
                    }
                },
                "itemsSold": {
                    "$push": {
                        "item": "$item",
                        "quantity": "$quantity"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/push/#use-in--setwindowfields-stage
     */
    case PushUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "quantitiesForState": {
                        "$push": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Rank Partitions by an Integer Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rank/#rank-partitions-by-an-integer-field
     */
    case RankRankPartitionsByAnIntegerField = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "quantity": {
                        "$numberInt": "-1"
                    }
                },
                "output": {
                    "rankQuantityForState": {
                        "$rank": {}
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Rank Partitions by a Date Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rank/#rank-partitions-by-a-date-field
     */
    case RankRankPartitionsByADateField = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "rankOrderDateForState": {
                        "$rank": {}
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Flowers collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setUnion/#example
     */
    case SetUnionFlowersCollection = <<<'EXTENDED_JSON'
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
     * Shift Using a Positive Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/#shift-using-a-positive-integer
     */
    case ShiftShiftUsingAPositiveInteger = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "quantity": {
                        "$numberInt": "-1"
                    }
                },
                "output": {
                    "shiftQuantityForState": {
                        "$shift": {
                            "output": "$quantity",
                            "by": {
                                "$numberInt": "1"
                            },
                            "default": "Not available"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Shift Using a Negative Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/#shift-using-a-negative-integer
     */
    case ShiftShiftUsingANegativeInteger = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "quantity": {
                        "$numberInt": "-1"
                    }
                },
                "output": {
                    "shiftQuantityForState": {
                        "$shift": {
                            "output": "$quantity",
                            "by": {
                                "$numberInt": "-1"
                            },
                            "default": "Not available"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/#use-in--group-stage
     */
    case StdDevPopUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$quiz",
                "stdDev": {
                    "$stdDevPop": "$score"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/#use-in--setwindowfields-stage
     */
    case StdDevPopUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "stdDevPopQuantityForState": {
                        "$stdDevPop": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/#use-in--group-stage
     */
    case StdDevSampUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$sample": {
                "size": {
                    "$numberInt": "100"
                }
            }
        },
        {
            "$group": {
                "_id": null,
                "ageStdDev": {
                    "$stdDevSamp": "$age"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/#use-in--setwindowfields-stage
     */
    case StdDevSampUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "stdDevSampQuantityForState": {
                        "$stdDevSamp": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/#use-in--group-stage
     */
    case SumUseInGroupStage = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "day": {
                        "$dayOfYear": {
                            "date": "$date"
                        }
                    },
                    "year": {
                        "$year": {
                            "date": "$date"
                        }
                    }
                },
                "totalAmount": {
                    "$sum": {
                        "$multiply": [
                            "$price",
                            "$quantity"
                        ]
                    }
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
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/#use-in--setwindowfields-stage
     */
    case SumUseInSetWindowFieldsStage = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "sumQuantityForState": {
                        "$sum": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Find the Top Score
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/top/#find-the-top-score
     */
    case TopFindTheTopScore = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "gameId": "G1"
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$top": {
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "sortBy": {
                            "score": {
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
     * Find the Top Score Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/top/#find-the-top-score-across-multiple-games
     */
    case TopFindTheTopScoreAcrossMultipleGames = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$top": {
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "sortBy": {
                            "score": {
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
     * Find the Three Highest Scores
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/#find-the-three-highest-scores
     */
    case TopNFindTheThreeHighestScores = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "gameId": "G1"
            }
        },
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$topN": {
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        },
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
     * Finding the Three Highest Score Documents Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/#finding-the-three-highest-score-documents-across-multiple-games
     */
    case TopNFindingTheThreeHighestScoreDocumentsAcrossMultipleGames = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$gameId",
                "playerId": {
                    "$topN": {
                        "output": [
                            "$playerId",
                            "$score"
                        ],
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        },
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
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/#computing-n-based-on-the-group-key-for--group
     */
    case TopNComputingNBasedOnTheGroupKeyForGroup = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "gameId": "$gameId"
                },
                "gamescores": {
                    "$topN": {
                        "output": "$score",
                        "n": {
                            "$cond": {
                                "if": {
                                    "$eq": [
                                        "$gameId",
                                        "G2"
                                    ]
                                },
                                "then": {
                                    "$numberInt": "1"
                                },
                                "else": {
                                    "$numberInt": "3"
                                }
                            }
                        },
                        "sortBy": {
                            "score": {
                                "$numberInt": "-1"
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;
}
