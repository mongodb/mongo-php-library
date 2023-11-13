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
                            "$code": "function () { return { count: 0, sum: 0 } }"
                        },
                        "accumulate": {
                            "$code": "function (state, numCopies) { return { count: state.count + 1, sum: state.sum + numCopies } }"
                        },
                        "accumulateArgs": [
                            "$copies"
                        ],
                        "merge": {
                            "$code": "function (state1, state2) { return { count: state1.count + state2.count, sum: state1.sum + state2.sum } }"
                        },
                        "finalize": {
                            "$code": "function (state) { return (state.sum \/ state.count) }"
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
                            "$code": "function (city, userProfileCity) { return { max: city === userProfileCity ? 3 : 1, restaurants: [] } }"
                        },
                        "initArgs": [
                            "$city",
                            "Bettles"
                        ],
                        "accumulate": {
                            "$code": "function (state, restaurantName) { if (state.restaurants.length < state.max) { state.restaurants.push(restaurantName); } return state; }"
                        },
                        "accumulateArgs": [
                            "$name"
                        ],
                        "merge": {
                            "$code": "function (state1, state2) { return { max: state1.max, restaurants: state1.restaurants.concat(state2.restaurants).slice(0, state1.max) } }"
                        },
                        "finalize": {
                            "$code": "function (state) { return state.restaurants }"
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
}
