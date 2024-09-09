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
    case AccumulatorUseAccumulatorToImplementTheAvgOperator = <<<'JSON'
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
    JSON;

    /**
     * Use initArgs to Vary the Initial State by Group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/accumulator/#use-initargs-to-vary-the-initial-state-by-group
     */
    case AccumulatorUseInitArgsToVaryTheInitialStateByGroup = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addToSet/#use-in--group-stage
     */
    case AddToSetUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addToSet/#use-in--setwindowfields-stage
     */
    case AddToSetUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/#use-in--group-stage
     */
    case AvgUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/avg/#use-in--setwindowfields-stage
     */
    case AvgUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Find the Bottom Score
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom/#find-the-bottom-score
     */
    case BottomFindTheBottomScore = <<<'JSON'
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
    JSON;

    /**
     * Finding the Bottom Score Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottom/#finding-the-bottom-score-across-multiple-games
     */
    case BottomFindingTheBottomScoreAcrossMultipleGames = <<<'JSON'
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
    JSON;

    /**
     * Find the Three Lowest Scores
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/#find-the-three-lowest-scores
     */
    case BottomNFindTheThreeLowestScores = <<<'JSON'
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
    JSON;

    /**
     * Finding the Three Lowest Score Documents Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/#finding-the-three-lowest-score-documents-across-multiple-games
     */
    case BottomNFindingTheThreeLowestScoreDocumentsAcrossMultipleGames = <<<'JSON'
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
    JSON;

    /**
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bottomN/#computing-n-based-on-the-group-key-for--group
     */
    case BottomNComputingNBasedOnTheGroupKeyForGroup = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count-accumulator/#use-in--group-stage
     */
    case CountUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count-accumulator/#use-in--setwindowfields-stage
     */
    case CountUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covariancePop/#example
     */
    case CovariancePopExample = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/covarianceSamp/#example
     */
    case CovarianceSampExample = <<<'JSON'
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
    JSON;

    /**
     * Dense Rank Partitions by an Integer Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/denseRank/#dense-rank-partitions-by-an-integer-field
     */
    case DenseRankDenseRankPartitionsByAnIntegerField = <<<'JSON'
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
    JSON;

    /**
     * Dense Rank Partitions by a Date Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/denseRank/#dense-rank-partitions-by-a-date-field
     */
    case DenseRankDenseRankPartitionsByADateField = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/derivative/#example
     */
    case DerivativeExample = <<<'JSON'
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
    JSON;

    /**
     * Document Number for Each State
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documentNumber/#document-number-for-each-state
     */
    case DocumentNumberDocumentNumberForEachState = <<<'JSON'
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
    JSON;

    /**
     * Exponential Moving Average Using N
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/expMovingAvg/#exponential-moving-average-using-n
     */
    case ExpMovingAvgExponentialMovingAverageUsingN = <<<'JSON'
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
    JSON;

    /**
     * Exponential Moving Average Using alpha
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/expMovingAvg/#exponential-moving-average-using-alpha
     */
    case ExpMovingAvgExponentialMovingAverageUsingAlpha = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/first/#use-in--group-stage
     */
    case FirstUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/first/#use-in--setwindowfields-stage
     */
    case FirstUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Null and Missing Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#null-and-missing-values
     */
    case FirstNNullAndMissingValues = <<<'JSON'
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
    JSON;

    /**
     * Find the First Three Player Scores for a Single Game
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#find-the-first-three-player-scores-for-a-single-game
     */
    case FirstNFindTheFirstThreePlayerScoresForASingleGame = <<<'JSON'
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
    JSON;

    /**
     * Finding the First Three Player Scores Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#finding-the-first-three-player-scores-across-multiple-games
     */
    case FirstNFindingTheFirstThreePlayerScoresAcrossMultipleGames = <<<'JSON'
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
    JSON;

    /**
     * Using $sort With $firstN
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#using--sort-with--firstn
     */
    case FirstNUsingSortWithFirstN = <<<'JSON'
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
    JSON;

    /**
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/firstN/#computing-n-based-on-the-group-key-for--group
     */
    case FirstNComputingNBasedOnTheGroupKeyForGroup = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/integral/#example
     */
    case IntegralExample = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/
     */
    case LastUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/last/#use-in--setwindowfields-stage
     */
    case LastUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Find the Last Three Player Scores for a Single Game
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#find-the-last-three-player-scores-for-a-single-game
     */
    case LastNFindTheLastThreePlayerScoresForASingleGame = <<<'JSON'
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
    JSON;

    /**
     * Finding the Last Three Player Scores Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#finding-the-last-three-player-scores-across-multiple-games
     */
    case LastNFindingTheLastThreePlayerScoresAcrossMultipleGames = <<<'JSON'
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
    JSON;

    /**
     * Using $sort With $lastN
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#using--sort-with--lastn
     */
    case LastNUsingSortWithLastN = <<<'JSON'
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
    JSON;

    /**
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lastN/#computing-n-based-on-the-group-key-for--group
     */
    case LastNComputingNBasedOnTheGroupKeyForGroup = <<<'JSON'
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
    JSON;

    /**
     * Fill Missing Values with Linear Interpolation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/linearFill/#fill-missing-values-with-linear-interpolation
     */
    case LinearFillFillMissingValuesWithLinearInterpolation = <<<'JSON'
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
    JSON;

    /**
     * Use Multiple Fill Methods in a Single Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/linearFill/#use-multiple-fill-methods-in-a-single-stage
     */
    case LinearFillUseMultipleFillMethodsInASingleStage = <<<'JSON'
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
    JSON;

    /**
     * Fill Missing Values with the Last Observed Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/locf/#fill-missing-values-with-the-last-observed-value
     */
    case LocfFillMissingValuesWithTheLastObservedValue = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/#use-in--group-stage
     */
    case MaxUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/max/#use-in--setwindowfields-stage
     */
    case MaxUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Find the Maximum Three Scores for a Single Game
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN/#find-the-maximum-three-scores-for-a-single-game
     */
    case MaxNFindTheMaximumThreeScoresForASingleGame = <<<'JSON'
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
    JSON;

    /**
     * Finding the Maximum Three Scores Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN/#finding-the-maximum-three-scores-across-multiple-games
     */
    case MaxNFindingTheMaximumThreeScoresAcrossMultipleGames = <<<'JSON'
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
    JSON;

    /**
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/maxN/#computing-n-based-on-the-group-key-for--group
     */
    case MaxNComputingNBasedOnTheGroupKeyForGroup = <<<'JSON'
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
    JSON;

    /**
     * Use $median as an Accumulator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/#use-operatorname-as-an-accumulator
     */
    case MedianUseMedianAsAnAccumulator = <<<'JSON'
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
    JSON;

    /**
     * Use $median in a $setWindowField Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/median/#use-operatorname-in-a--setwindowfield-stage
     */
    case MedianUseMedianInASetWindowFieldStage = <<<'JSON'
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
    JSON;

    /**
     * $mergeObjects as an Accumulator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/mergeObjects/#-mergeobjects-as-an-accumulator
     */
    case MergeObjectsMergeObjectsAsAnAccumulator = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/#use-in--group-stage
     */
    case MinUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/min/#use-in--setwindowfields-stage
     */
    case MinUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Find the Minimum Three Scores for a Single Game
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN/#find-the-minimum-three-scores-for-a-single-game
     */
    case MinNFindTheMinimumThreeScoresForASingleGame = <<<'JSON'
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
    JSON;

    /**
     * Finding the Minimum Three Documents Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN/#finding-the-minimum-three-documents-across-multiple-games
     */
    case MinNFindingTheMinimumThreeDocumentsAcrossMultipleGames = <<<'JSON'
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
    JSON;

    /**
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/minN/#computing-n-based-on-the-group-key-for--group
     */
    case MinNComputingNBasedOnTheGroupKeyForGroup = <<<'JSON'
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
    JSON;

    /**
     * Calculate a Single Value as an Accumulator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/#calculate-a-single-value-as-an-accumulator
     */
    case PercentileCalculateASingleValueAsAnAccumulator = <<<'JSON'
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
    JSON;

    /**
     * Calculate Multiple Values as an Accumulator
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/#calculate-multiple-values-as-an-accumulator
     */
    case PercentileCalculateMultipleValuesAsAnAccumulator = <<<'JSON'
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
    JSON;

    /**
     * Use $percentile in a $setWindowField Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/percentile/#use-operatorname-in-a--setwindowfield-stage
     */
    case PercentileUsePercentileInASetWindowFieldStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/push/#use-in--group-stage
     */
    case PushUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/push/#use-in--setwindowfields-stage
     */
    case PushUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Rank Partitions by an Integer Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rank/#rank-partitions-by-an-integer-field
     */
    case RankRankPartitionsByAnIntegerField = <<<'JSON'
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
    JSON;

    /**
     * Rank Partitions by a Date Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rank/#rank-partitions-by-a-date-field
     */
    case RankRankPartitionsByADateField = <<<'JSON'
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
    JSON;

    /**
     * Shift Using a Positive Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/#shift-using-a-positive-integer
     */
    case ShiftShiftUsingAPositiveInteger = <<<'JSON'
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
    JSON;

    /**
     * Shift Using a Negative Integer
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shift/#shift-using-a-negative-integer
     */
    case ShiftShiftUsingANegativeInteger = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/#use-in--group-stage
     */
    case StdDevPopUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevPop/#use-in--setwindowfields-stage
     */
    case StdDevPopUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/#use-in--group-stage
     */
    case StdDevSampUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/stdDevSamp/#use-in--setwindowfields-stage
     */
    case StdDevSampUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $group Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/#use-in--group-stage
     */
    case SumUseInGroupStage = <<<'JSON'
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
    JSON;

    /**
     * Use in $setWindowFields Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sum/#use-in--setwindowfields-stage
     */
    case SumUseInSetWindowFieldsStage = <<<'JSON'
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
    JSON;

    /**
     * Find the Top Score
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/top/#find-the-top-score
     */
    case TopFindTheTopScore = <<<'JSON'
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
    JSON;

    /**
     * Find the Top Score Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/top/#find-the-top-score-across-multiple-games
     */
    case TopFindTheTopScoreAcrossMultipleGames = <<<'JSON'
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
    JSON;

    /**
     * Find the Three Highest Scores
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/#find-the-three-highest-scores
     */
    case TopNFindTheThreeHighestScores = <<<'JSON'
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
    JSON;

    /**
     * Finding the Three Highest Score Documents Across Multiple Games
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/#finding-the-three-highest-score-documents-across-multiple-games
     */
    case TopNFindingTheThreeHighestScoreDocumentsAcrossMultipleGames = <<<'JSON'
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
    JSON;

    /**
     * Computing n Based on the Group Key for $group
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/topN/#computing-n-based-on-the-group-key-for--group
     */
    case TopNComputingNBasedOnTheGroupKeyForGroup = <<<'JSON'
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
    JSON;
}
