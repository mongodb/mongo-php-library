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
                    "orderDate": 1
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
}
