<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Stage;

enum Pipelines: string
{
    /**
     * Using Two $addFields Stages
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/#using-two--addfields-stages
     */
    case AddFieldsUsingTwoAddFieldsStages = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "totalHomework": {
                    "$sum": [
                        "$homework"
                    ]
                },
                "totalQuiz": {
                    "$sum": [
                        "$quiz"
                    ]
                }
            }
        },
        {
            "$addFields": {
                "totalScore": {
                    "$add": [
                        "$totalHomework",
                        "$totalQuiz",
                        "$extraCredit"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Adding Fields to an Embedded Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/#adding-fields-to-an-embedded-document
     */
    case AddFieldsAddingFieldsToAnEmbeddedDocument = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "specs.fuel_type": "unleaded"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Overwriting an existing field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/#overwriting-an-existing-field
     */
    case AddFieldsOverwritingAnExistingField = <<<'EXTENDED_JSON'
    [
        {
            "$addFields": {
                "cats": {
                    "$numberInt": "20"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Add Element to an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/#add-element-to-an-array
     */
    case AddFieldsAddElementToAnArray = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "_id": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$addFields": {
                "homework": {
                    "$concatArrays": [
                        "$homework",
                        [
                            {
                                "$numberInt": "7"
                            }
                        ]
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Bucket by Year and Filter by Bucket Results
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucket/#bucket-by-year-and-filter-by-bucket-results
     */
    case BucketBucketByYearAndFilterByBucketResults = <<<'EXTENDED_JSON'
    [
        {
            "$bucket": {
                "groupBy": "$year_born",
                "boundaries": [
                    {
                        "$numberInt": "1840"
                    },
                    {
                        "$numberInt": "1850"
                    },
                    {
                        "$numberInt": "1860"
                    },
                    {
                        "$numberInt": "1870"
                    },
                    {
                        "$numberInt": "1880"
                    }
                ],
                "default": "Other",
                "output": {
                    "count": {
                        "$sum": {
                            "$numberInt": "1"
                        }
                    },
                    "artists": {
                        "$push": {
                            "name": {
                                "$concat": [
                                    "$first_name",
                                    " ",
                                    "$last_name"
                                ]
                            },
                            "year_born": "$year_born"
                        }
                    }
                }
            }
        },
        {
            "$match": {
                "count": {
                    "$gt": {
                        "$numberInt": "3"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use $bucket with $facet to Bucket by Multiple Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucket/#use--bucket-with--facet-to-bucket-by-multiple-fields
     */
    case BucketUseBucketWithFacetToBucketByMultipleFields = <<<'EXTENDED_JSON'
    [
        {
            "$facet": {
                "price": [
                    {
                        "$bucket": {
                            "groupBy": "$price",
                            "boundaries": [
                                {
                                    "$numberInt": "0"
                                },
                                {
                                    "$numberInt": "200"
                                },
                                {
                                    "$numberInt": "400"
                                }
                            ],
                            "default": "Other",
                            "output": {
                                "count": {
                                    "$sum": {
                                        "$numberInt": "1"
                                    }
                                },
                                "artwork": {
                                    "$push": {
                                        "title": "$title",
                                        "price": "$price"
                                    }
                                },
                                "averagePrice": {
                                    "$avg": "$price"
                                }
                            }
                        }
                    }
                ],
                "year": [
                    {
                        "$bucket": {
                            "groupBy": "$year",
                            "boundaries": [
                                {
                                    "$numberInt": "1890"
                                },
                                {
                                    "$numberInt": "1910"
                                },
                                {
                                    "$numberInt": "1920"
                                },
                                {
                                    "$numberInt": "1940"
                                }
                            ],
                            "default": "Unknown",
                            "output": {
                                "count": {
                                    "$sum": {
                                        "$numberInt": "1"
                                    }
                                },
                                "artwork": {
                                    "$push": {
                                        "title": "$title",
                                        "year": "$year"
                                    }
                                }
                            }
                        }
                    }
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Single Facet Aggregation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucketAuto/#single-facet-aggregation
     */
    case BucketAutoSingleFacetAggregation = <<<'EXTENDED_JSON'
    [
        {
            "$bucketAuto": {
                "groupBy": "$price",
                "buckets": {
                    "$numberInt": "4"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStream/#examples
     */
    case ChangeStreamExample = <<<'EXTENDED_JSON'
    [
        {
            "$changeStream": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStreamSplitLargeEvent/#example
     */
    case ChangeStreamSplitLargeEventExample = <<<'EXTENDED_JSON'
    [
        {
            "$changeStreamSplitLargeEvent": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * latencyStats Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/#latencystats-document
     */
    case CollStatsLatencyStatsDocument = <<<'EXTENDED_JSON'
    [
        {
            "$collStats": {
                "latencyStats": {
                    "histograms": true
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * storageStats Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/#storagestats-document
     */
    case CollStatsStorageStatsDocument = <<<'EXTENDED_JSON'
    [
        {
            "$collStats": {
                "storageStats": {}
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * count Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/#count-field
     */
    case CollStatsCountField = <<<'EXTENDED_JSON'
    [
        {
            "$collStats": {
                "count": {}
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * queryExecStats Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/#queryexecstats-document
     */
    case CollStatsQueryExecStatsDocument = <<<'EXTENDED_JSON'
    [
        {
            "$collStats": {
                "queryExecStats": {}
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count/#example
     */
    case CountExample = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "score": {
                    "$gt": {
                        "$numberInt": "80"
                    }
                }
            }
        },
        {
            "$count": "passing_scores"
        }
    ]
    EXTENDED_JSON;

    /**
     * Inactive Sessions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/currentOp/#inactive-sessions
     */
    case CurrentOpInactiveSessions = <<<'EXTENDED_JSON'
    [
        {
            "$currentOp": {
                "allUsers": true,
                "idleSessions": true
            }
        },
        {
            "$match": {
                "active": false,
                "transaction": {
                    "$exists": true
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Sampled Queries
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/currentOp/#sampled-queries
     */
    case CurrentOpSampledQueries = <<<'EXTENDED_JSON'
    [
        {
            "$currentOp": {
                "allUsers": true,
                "localOps": true
            }
        },
        {
            "$match": {
                "desc": "query analyzer"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Densify Time Series Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/densify/#densify-time-series-data
     */
    case DensifyDensifyTimeSeriesData = <<<'EXTENDED_JSON'
    [
        {
            "$densify": {
                "field": "timestamp",
                "range": {
                    "step": {
                        "$numberInt": "1"
                    },
                    "unit": "hour",
                    "bounds": [
                        {
                            "$date": {
                                "$numberLong": "1621296000000"
                            }
                        },
                        {
                            "$date": {
                                "$numberLong": "1621324800000"
                            }
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Densifiction with Partitions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/densify/#densifiction-with-partitions
     */
    case DensifyDensifictionWithPartitions = <<<'EXTENDED_JSON'
    [
        {
            "$densify": {
                "field": "altitude",
                "partitionByFields": [
                    "variety"
                ],
                "range": {
                    "bounds": "full",
                    "step": {
                        "$numberInt": "200"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Test a Pipeline Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documents/#test-a-pipeline-stage
     */
    case DocumentsTestAPipelineStage = <<<'EXTENDED_JSON'
    [
        {
            "$documents": [
                {
                    "x": {
                        "$numberInt": "10"
                    }
                },
                {
                    "x": {
                        "$numberInt": "2"
                    }
                },
                {
                    "x": {
                        "$numberInt": "5"
                    }
                }
            ]
        },
        {
            "$bucketAuto": {
                "groupBy": "$x",
                "buckets": {
                    "$numberInt": "4"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use a $documents Stage in a $lookup Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documents/#use-a--documents-stage-in-a--lookup-stage
     */
    case DocumentsUseADocumentsStageInALookupStage = <<<'EXTENDED_JSON'
    [
        {
            "$match": {}
        },
        {
            "$lookup": {
                "localField": "zip",
                "foreignField": "zip_id",
                "as": "city_state",
                "pipeline": [
                    {
                        "$documents": [
                            {
                                "zip_id": {
                                    "$numberInt": "94301"
                                },
                                "name": "Palo Alto, CA"
                            },
                            {
                                "zip_id": {
                                    "$numberInt": "10019"
                                },
                                "name": "New York, NY"
                            }
                        ]
                    }
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/facet/#example
     */
    case FacetExample = <<<'EXTENDED_JSON'
    [
        {
            "$facet": {
                "categorizedByTags": [
                    {
                        "$unwind": {
                            "path": "$tags"
                        }
                    },
                    {
                        "$sortByCount": "$tags"
                    }
                ],
                "categorizedByPrice": [
                    {
                        "$match": {
                            "price": {
                                "$exists": true
                            }
                        }
                    },
                    {
                        "$bucket": {
                            "groupBy": "$price",
                            "boundaries": [
                                {
                                    "$numberInt": "0"
                                },
                                {
                                    "$numberInt": "150"
                                },
                                {
                                    "$numberInt": "200"
                                },
                                {
                                    "$numberInt": "300"
                                },
                                {
                                    "$numberInt": "400"
                                }
                            ],
                            "default": "Other",
                            "output": {
                                "count": {
                                    "$sum": {
                                        "$numberInt": "1"
                                    }
                                },
                                "titles": {
                                    "$push": "$title"
                                }
                            }
                        }
                    }
                ],
                "categorizedByYears(Auto)": [
                    {
                        "$bucketAuto": {
                            "groupBy": "$year",
                            "buckets": {
                                "$numberInt": "4"
                            }
                        }
                    }
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fill Missing Field Values with a Constant Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#fill-missing-field-values-with-a-constant-value
     */
    case FillFillMissingFieldValuesWithAConstantValue = <<<'EXTENDED_JSON'
    [
        {
            "$fill": {
                "output": {
                    "bootsSold": {
                        "value": {
                            "$numberInt": "0"
                        }
                    },
                    "sandalsSold": {
                        "value": {
                            "$numberInt": "0"
                        }
                    },
                    "sneakersSold": {
                        "value": {
                            "$numberInt": "0"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fill Missing Field Values with Linear Interpolation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#fill-missing-field-values-with-linear-interpolation
     */
    case FillFillMissingFieldValuesWithLinearInterpolation = <<<'EXTENDED_JSON'
    [
        {
            "$fill": {
                "sortBy": {
                    "time": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "price": {
                        "method": "linear"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fill Missing Field Values Based on the Last Observed Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#fill-missing-field-values-based-on-the-last-observed-value
     */
    case FillFillMissingFieldValuesBasedOnTheLastObservedValue = <<<'EXTENDED_JSON'
    [
        {
            "$fill": {
                "sortBy": {
                    "date": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "score": {
                        "method": "locf"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fill Data for Distinct Partitions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#fill-data-for-distinct-partitions
     */
    case FillFillDataForDistinctPartitions = <<<'EXTENDED_JSON'
    [
        {
            "$fill": {
                "sortBy": {
                    "date": {
                        "$numberInt": "1"
                    }
                },
                "partitionBy": {
                    "restaurant": "$restaurant"
                },
                "output": {
                    "score": {
                        "method": "locf"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Indicate if a Field was Populated Using $fill
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#indicate-if-a-field-was-populated-using--fill
     */
    case FillIndicateIfAFieldWasPopulatedUsingFill = <<<'EXTENDED_JSON'
    [
        {
            "$set": {
                "valueExisted": {
                    "$ifNull": [
                        {
                            "$toBool": {
                                "$toString": "$score"
                            }
                        },
                        false
                    ]
                }
            }
        },
        {
            "$fill": {
                "sortBy": {
                    "date": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "score": {
                        "method": "locf"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Maximum Distance
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#maximum-distance
     */
    case GeoNearMaximumDistance = <<<'EXTENDED_JSON'
    [
        {
            "$geoNear": {
                "near": {
                    "type": "Point",
                    "coordinates": [
                        {
                            "$numberDouble": "-73.992789999999999395"
                        },
                        {
                            "$numberDouble": "40.719295999999999935"
                        }
                    ]
                },
                "distanceField": "dist.calculated",
                "maxDistance": {
                    "$numberInt": "2"
                },
                "query": {
                    "category": "Parks"
                },
                "includeLocs": "dist.location",
                "spherical": true
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Minimum Distance
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#minimum-distance
     */
    case GeoNearMinimumDistance = <<<'EXTENDED_JSON'
    [
        {
            "$geoNear": {
                "near": {
                    "type": "Point",
                    "coordinates": [
                        {
                            "$numberDouble": "-73.992789999999999395"
                        },
                        {
                            "$numberDouble": "40.719295999999999935"
                        }
                    ]
                },
                "distanceField": "dist.calculated",
                "minDistance": {
                    "$numberInt": "2"
                },
                "query": {
                    "category": "Parks"
                },
                "includeLocs": "dist.location",
                "spherical": true
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * with the let option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#-geonear-with-the-let-option
     */
    case GeoNearWithTheLetOption = <<<'EXTENDED_JSON'
    [
        {
            "$geoNear": {
                "near": "$$pt",
                "distanceField": "distance",
                "maxDistance": {
                    "$numberInt": "2"
                },
                "query": {
                    "category": "Parks"
                },
                "includeLocs": "dist.location",
                "spherical": true
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * with Bound let Option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#-geonear-with-bound-let-option
     */
    case GeoNearWithBoundLetOption = <<<'EXTENDED_JSON'
    [
        {
            "$lookup": {
                "from": "places",
                "let": {
                    "pt": "$location"
                },
                "pipeline": [
                    {
                        "$geoNear": {
                            "near": "$$pt",
                            "distanceField": "distance"
                        }
                    }
                ],
                "as": "joinedField"
            }
        },
        {
            "$match": {
                "name": "Sara D. Roosevelt Park"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Specify Which Geospatial Index to Use
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#specify-which-geospatial-index-to-use
     */
    case GeoNearSpecifyWhichGeospatialIndexToUse = <<<'EXTENDED_JSON'
    [
        {
            "$geoNear": {
                "near": {
                    "type": "Point",
                    "coordinates": [
                        {
                            "$numberDouble": "-73.981419999999999959"
                        },
                        {
                            "$numberDouble": "40.717820000000003233"
                        }
                    ]
                },
                "key": "location",
                "distanceField": "dist.calculated",
                "query": {
                    "category": "Parks"
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
     * Within a Single Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/#within-a-single-collection
     */
    case GraphLookupWithinASingleCollection = <<<'EXTENDED_JSON'
    [
        {
            "$graphLookup": {
                "from": "employees",
                "startWith": "$reportsTo",
                "connectFromField": "reportsTo",
                "connectToField": "name",
                "as": "reportingHierarchy"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Across Multiple Collections
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/#across-multiple-collections
     */
    case GraphLookupAcrossMultipleCollections = <<<'EXTENDED_JSON'
    [
        {
            "$graphLookup": {
                "from": "airports",
                "startWith": "$nearestAirport",
                "connectFromField": "connects",
                "connectToField": "airport",
                "maxDepth": {
                    "$numberInt": "2"
                },
                "depthField": "numConnections",
                "as": "destinations"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * With a Query Filter
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/#with-a-query-filter
     */
    case GraphLookupWithAQueryFilter = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "name": "Tanya Jordan"
            }
        },
        {
            "$graphLookup": {
                "from": "people",
                "startWith": "$friends",
                "connectFromField": "friends",
                "connectToField": "name",
                "as": "golfers",
                "restrictSearchWithMatch": {
                    "hobbies": "golf"
                }
            }
        },
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "friends": {
                    "$numberInt": "1"
                },
                "connections who play golf": "$golfers.name"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Count the Number of Documents in a Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#count-the-number-of-documents-in-a-collection
     */
    case GroupCountTheNumberOfDocumentsInACollection = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": null,
                "count": {
                    "$count": {}
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Retrieve Distinct Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#retrieve-distinct-values
     */
    case GroupRetrieveDistinctValues = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$item"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Group by Item Having
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#group-by-item-having
     */
    case GroupGroupByItemHaving = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$item",
                "totalSaleAmount": {
                    "$sum": {
                        "$multiply": [
                            "$price",
                            "$quantity"
                        ]
                    }
                }
            }
        },
        {
            "$match": {
                "totalSaleAmount": {
                    "$gte": {
                        "$numberInt": "100"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Calculate Count Sum and Average
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#calculate-count--sum--and-average
     */
    case GroupCalculateCountSumAndAverage = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "date": {
                    "$gte": {
                        "$date": {
                            "$numberLong": "1388534400000"
                        }
                    },
                    "$lt": {
                        "$date": {
                            "$numberLong": "1420070400000"
                        }
                    }
                }
            }
        },
        {
            "$group": {
                "_id": {
                    "$dateToString": {
                        "format": "%Y-%m-%d",
                        "date": "$date"
                    }
                },
                "totalSaleAmount": {
                    "$sum": {
                        "$multiply": [
                            "$price",
                            "$quantity"
                        ]
                    }
                },
                "averageQuantity": {
                    "$avg": "$quantity"
                },
                "count": {
                    "$sum": {
                        "$numberInt": "1"
                    }
                }
            }
        },
        {
            "$sort": {
                "totalSaleAmount": {
                    "$numberInt": "-1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Group by null
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#group-by-null
     */
    case GroupGroupByNull = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": null,
                "totalSaleAmount": {
                    "$sum": {
                        "$multiply": [
                            "$price",
                            "$quantity"
                        ]
                    }
                },
                "averageQuantity": {
                    "$avg": "$quantity"
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
     * Pivot Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#pivot-data
     */
    case GroupPivotData = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$author",
                "books": {
                    "$push": "$title"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Group Documents by author
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#group-documents-by-author
     */
    case GroupGroupDocumentsByAuthor = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$author",
                "books": {
                    "$push": "$$ROOT"
                }
            }
        },
        {
            "$addFields": {
                "totalCopies": {
                    "$sum": [
                        "$books.copies"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexStats/#example
     */
    case IndexStatsExample = <<<'EXTENDED_JSON'
    [
        {
            "$indexStats": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/limit/#example
     */
    case LimitExample = <<<'EXTENDED_JSON'
    [
        {
            "$limit": {
                "$numberInt": "5"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * List All Local Sessions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listLocalSessions/#list-all-local-sessions
     */
    case ListLocalSessionsListAllLocalSessions = <<<'EXTENDED_JSON'
    [
        {
            "$listLocalSessions": {
                "allUsers": true
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * List All Local Sessions for the Specified Users
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listLocalSessions/#list-all-local-sessions-for-the-specified-users
     */
    case ListLocalSessionsListAllLocalSessionsForTheSpecifiedUsers = <<<'EXTENDED_JSON'
    [
        {
            "$listLocalSessions": {
                "users": [
                    {
                        "user": "myAppReader",
                        "db": "test"
                    }
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * List All Local Sessions for the Current User
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listLocalSessions/#list-all-local-sessions-for-the-current-user
     */
    case ListLocalSessionsListAllLocalSessionsForTheCurrentUser = <<<'EXTENDED_JSON'
    [
        {
            "$listLocalSessions": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * List Sampled Queries for All Collections
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSampledQueries/#list-sampled-queries-for-all-collections
     */
    case ListSampledQueriesListSampledQueriesForAllCollections = <<<'EXTENDED_JSON'
    [
        {
            "$listSampledQueries": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * List Sampled Queries for A Specific Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSampledQueries/#list-sampled-queries-for-a-specific-collection
     */
    case ListSampledQueriesListSampledQueriesForASpecificCollection = <<<'EXTENDED_JSON'
    [
        {
            "$listSampledQueries": {
                "namespace": "social.post"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Return All Search Indexes
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/#return-all-search-indexes
     */
    case ListSearchIndexesReturnAllSearchIndexes = <<<'EXTENDED_JSON'
    [
        {
            "$listSearchIndexes": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * Return a Single Search Index by Name
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/#return-a-single-search-index-by-name
     */
    case ListSearchIndexesReturnASingleSearchIndexByName = <<<'EXTENDED_JSON'
    [
        {
            "$listSearchIndexes": {
                "name": "synonym-mappings"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Return a Single Search Index by id
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/#return-a-single-search-index-by-id
     */
    case ListSearchIndexesReturnASingleSearchIndexById = <<<'EXTENDED_JSON'
    [
        {
            "$listSearchIndexes": {
                "id": "6524096020da840844a4c4a7"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * List All Sessions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSessions/#list-all-sessions
     */
    case ListSessionsListAllSessions = <<<'EXTENDED_JSON'
    [
        {
            "$listSessions": {
                "allUsers": true
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * List All Sessions for the Specified Users
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSessions/#list-all-sessions-for-the-specified-users
     */
    case ListSessionsListAllSessionsForTheSpecifiedUsers = <<<'EXTENDED_JSON'
    [
        {
            "$listSessions": {
                "users": [
                    {
                        "user": "myAppReader",
                        "db": "test"
                    }
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * List All Sessions for the Current User
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSessions/#list-all-sessions-for-the-current-user
     */
    case ListSessionsListAllSessionsForTheCurrentUser = <<<'EXTENDED_JSON'
    [
        {
            "$listSessions": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * Perform a Single Equality Join with $lookup
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#perform-a-single-equality-join-with--lookup
     */
    case LookupPerformASingleEqualityJoinWithLookup = <<<'EXTENDED_JSON'
    [
        {
            "$lookup": {
                "from": "inventory",
                "localField": "item",
                "foreignField": "sku",
                "as": "inventory_docs"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use $lookup with an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#use--lookup-with-an-array
     */
    case LookupUseLookupWithAnArray = <<<'EXTENDED_JSON'
    [
        {
            "$lookup": {
                "from": "members",
                "localField": "enrollmentlist",
                "foreignField": "name",
                "as": "enrollee_info"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use $lookup with $mergeObjects
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#use--lookup-with--mergeobjects
     */
    case LookupUseLookupWithMergeObjects = <<<'EXTENDED_JSON'
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
     * Perform Multiple Joins and a Correlated Subquery with $lookup
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#perform-multiple-joins-and-a-correlated-subquery-with--lookup
     */
    case LookupPerformMultipleJoinsAndACorrelatedSubqueryWithLookup = <<<'EXTENDED_JSON'
    [
        {
            "$lookup": {
                "from": "warehouses",
                "let": {
                    "order_item": "$item",
                    "order_qty": "$ordered"
                },
                "pipeline": [
                    {
                        "$match": {
                            "$expr": {
                                "$and": [
                                    {
                                        "$eq": [
                                            "$stock_item",
                                            "$$order_item"
                                        ]
                                    },
                                    {
                                        "$gte": [
                                            "$instock",
                                            "$$order_qty"
                                        ]
                                    }
                                ]
                            }
                        }
                    },
                    {
                        "$project": {
                            "stock_item": {
                                "$numberInt": "0"
                            },
                            "_id": {
                                "$numberInt": "0"
                            }
                        }
                    }
                ],
                "as": "stockdata"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Perform an Uncorrelated Subquery with $lookup
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#perform-an-uncorrelated-subquery-with--lookup
     */
    case LookupPerformAnUncorrelatedSubqueryWithLookup = <<<'EXTENDED_JSON'
    [
        {
            "$lookup": {
                "from": "holidays",
                "pipeline": [
                    {
                        "$match": {
                            "year": {
                                "$numberInt": "2018"
                            }
                        }
                    },
                    {
                        "$project": {
                            "_id": {
                                "$numberInt": "0"
                            },
                            "date": {
                                "name": "$name",
                                "date": "$date"
                            }
                        }
                    },
                    {
                        "$replaceRoot": {
                            "newRoot": "$date"
                        }
                    }
                ],
                "as": "holidays"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Perform a Concise Correlated Subquery with $lookup
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#perform-a-concise-correlated-subquery-with--lookup
     */
    case LookupPerformAConciseCorrelatedSubqueryWithLookup = <<<'EXTENDED_JSON'
    [
        {
            "$lookup": {
                "from": "restaurants",
                "localField": "restaurant_name",
                "foreignField": "name",
                "let": {
                    "orders_drink": "$drink"
                },
                "pipeline": [
                    {
                        "$match": {
                            "$expr": {
                                "$in": [
                                    "$$orders_drink",
                                    "$beverages"
                                ]
                            }
                        }
                    }
                ],
                "as": "matches"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Equality Match
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/#equality-match
     */
    case MatchEqualityMatch = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "author": "dave"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Perform a Count
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/#perform-a-count
     */
    case MatchPerformACount = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$or": [
                    {
                        "score": {
                            "$gt": {
                                "$numberInt": "70"
                            },
                            "$lt": {
                                "$numberInt": "90"
                            }
                        }
                    },
                    {
                        "views": {
                            "$gte": {
                                "$numberInt": "1000"
                            }
                        }
                    }
                ]
            }
        },
        {
            "$group": {
                "_id": null,
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
     * On-Demand Materialized View Initial Creation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#on-demand-materialized-view--initial-creation
     */
    case MergeOnDemandMaterializedViewInitialCreation = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": {
                    "fiscal_year": "$fiscal_year",
                    "dept": "$dept"
                },
                "salaries": {
                    "$sum": "$salary"
                }
            }
        },
        {
            "$merge": {
                "into": {
                    "db": "reporting",
                    "coll": "budgets"
                },
                "on": "_id",
                "whenMatched": "replace",
                "whenNotMatched": "insert"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * On-Demand Materialized View Update Replace Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#on-demand-materialized-view--update-replace-data
     */
    case MergeOnDemandMaterializedViewUpdateReplaceData = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "fiscal_year": {
                    "$gte": {
                        "$numberInt": "2019"
                    }
                }
            }
        },
        {
            "$group": {
                "_id": {
                    "fiscal_year": "$fiscal_year",
                    "dept": "$dept"
                },
                "salaries": {
                    "$sum": "$salary"
                }
            }
        },
        {
            "$merge": {
                "into": {
                    "db": "reporting",
                    "coll": "budgets"
                },
                "on": "_id",
                "whenMatched": "replace",
                "whenNotMatched": "insert"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Only Insert New Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#only-insert-new-data
     */
    case MergeOnlyInsertNewData = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "fiscal_year": {
                    "$numberInt": "2019"
                }
            }
        },
        {
            "$group": {
                "_id": {
                    "fiscal_year": "$fiscal_year",
                    "dept": "$dept"
                },
                "employees": {
                    "$push": "$employee"
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "dept": "$_id.dept",
                "fiscal_year": "$_id.fiscal_year",
                "employees": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$merge": {
                "into": {
                    "db": "reporting",
                    "coll": "orgArchive"
                },
                "on": [
                    "dept",
                    "fiscal_year"
                ],
                "whenMatched": "fail"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Merge Results from Multiple Collections
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#merge-results-from-multiple-collections
     */
    case MergeMergeResultsFromMultipleCollections = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$quarter",
                "purchased": {
                    "$sum": "$qty"
                }
            }
        },
        {
            "$merge": {
                "into": "quarterlyreport",
                "on": "_id",
                "whenMatched": "merge",
                "whenNotMatched": "insert"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use the Pipeline to Customize the Merge
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#use-the-pipeline-to-customize-the-merge
     */
    case MergeUseThePipelineToCustomizeTheMerge = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "date": {
                    "$gte": {
                        "$date": {
                            "$numberLong": "1557187200000"
                        }
                    },
                    "$lt": {
                        "$date": {
                            "$numberLong": "1557273600000"
                        }
                    }
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$dateToString": {
                        "format": "%Y-%m",
                        "date": "$date"
                    }
                },
                "thumbsup": {
                    "$numberInt": "1"
                },
                "thumbsdown": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$merge": {
                "into": "monthlytotals",
                "on": "_id",
                "whenMatched": [
                    {
                        "$addFields": {
                            "thumbsup": {
                                "$add": [
                                    "$thumbsup",
                                    "$$new.thumbsup"
                                ]
                            },
                            "thumbsdown": {
                                "$add": [
                                    "$thumbsdown",
                                    "$$new.thumbsdown"
                                ]
                            }
                        }
                    }
                ],
                "whenNotMatched": "insert"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use Variables to Customize the Merge
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#use-variables-to-customize-the-merge
     */
    case MergeUseVariablesToCustomizeTheMerge = <<<'EXTENDED_JSON'
    [
        {
            "$merge": {
                "into": "cakeSales",
                "let": {
                    "year": "2020"
                },
                "whenMatched": [
                    {
                        "$addFields": {
                            "salesYear": "$$year"
                        }
                    }
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Output to Same Database
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/#output-to-same-database
     */
    case OutOutputToSameDatabase = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$author",
                "books": {
                    "$push": "$title"
                }
            }
        },
        {
            "$out": {
                "coll": "authors"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Output to a Different Database
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/#output-to-a-different-database
     */
    case OutOutputToADifferentDatabase = <<<'EXTENDED_JSON'
    [
        {
            "$group": {
                "_id": "$author",
                "books": {
                    "$push": "$title"
                }
            }
        },
        {
            "$out": {
                "db": "reporting",
                "coll": "authors"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Output to a Time Series Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/#output-to-a-time-series-collection
     */
    case OutOutputToATimeSeriesCollection = <<<'EXTENDED_JSON'
    [
        {
            "$out": {
                "db": "reporting",
                "coll": "sensorData",
                "timeseries": {
                    "timeField": "timestamp",
                    "metaField": "sensorId",
                    "granularity": "hours"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Return Information for All Entries in the Query Cache
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/planCacheStats/#return-information-for-all-entries-in-the-query-cache
     */
    case PlanCacheStatsReturnInformationForAllEntriesInTheQueryCache = <<<'EXTENDED_JSON'
    [
        {
            "$planCacheStats": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * Find Cache Entry Details for a Query Hash
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/planCacheStats/#find-cache-entry-details-for-a-query-hash
     */
    case PlanCacheStatsFindCacheEntryDetailsForAQueryHash = <<<'EXTENDED_JSON'
    [
        {
            "$planCacheStats": {}
        },
        {
            "$match": {
                "planCacheKey": "B1435201"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Include Specific Fields in Output Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#include-specific-fields-in-output-documents
     */
    case ProjectIncludeSpecificFieldsInOutputDocuments = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "title": {
                    "$numberInt": "1"
                },
                "author": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Suppress id Field in the Output Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#suppress-_id-field-in-the-output-documents
     */
    case ProjectSuppressIdFieldInTheOutputDocuments = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                },
                "author": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Exclude Fields from Output Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#exclude-fields-from-output-documents
     */
    case ProjectExcludeFieldsFromOutputDocuments = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "lastModified": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Exclude Fields from Embedded Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#exclude-fields-from-embedded-documents
     */
    case ProjectExcludeFieldsFromEmbeddedDocuments = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "author.first": {
                    "$numberInt": "0"
                },
                "lastModified": {
                    "$numberInt": "0"
                }
            }
        },
        {
            "$project": {
                "author": {
                    "first": {
                        "$numberInt": "0"
                    }
                },
                "lastModified": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Conditionally Exclude Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#conditionally-exclude-fields
     */
    case ProjectConditionallyExcludeFields = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "title": {
                    "$numberInt": "1"
                },
                "author.first": {
                    "$numberInt": "1"
                },
                "author.last": {
                    "$numberInt": "1"
                },
                "author.middle": {
                    "$cond": {
                        "if": {
                            "$eq": [
                                "",
                                "$author.middle"
                            ]
                        },
                        "then": "$$REMOVE",
                        "else": "$author.middle"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Include Specific Fields from Embedded Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#include-specific-fields-from-embedded-documents
     */
    case ProjectIncludeSpecificFieldsFromEmbeddedDocuments = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "stop.title": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$project": {
                "stop": {
                    "title": {
                        "$numberInt": "1"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Include Computed Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#include-computed-fields
     */
    case ProjectIncludeComputedFields = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "title": {
                    "$numberInt": "1"
                },
                "isbn": {
                    "prefix": {
                        "$substr": [
                            "$isbn",
                            {
                                "$numberInt": "0"
                            },
                            {
                                "$numberInt": "3"
                            }
                        ]
                    },
                    "group": {
                        "$substr": [
                            "$isbn",
                            {
                                "$numberInt": "3"
                            },
                            {
                                "$numberInt": "2"
                            }
                        ]
                    },
                    "publisher": {
                        "$substr": [
                            "$isbn",
                            {
                                "$numberInt": "5"
                            },
                            {
                                "$numberInt": "4"
                            }
                        ]
                    },
                    "title": {
                        "$substr": [
                            "$isbn",
                            {
                                "$numberInt": "9"
                            },
                            {
                                "$numberInt": "3"
                            }
                        ]
                    },
                    "checkDigit": {
                        "$substr": [
                            "$isbn",
                            {
                                "$numberInt": "12"
                            },
                            {
                                "$numberInt": "1"
                            }
                        ]
                    }
                },
                "lastName": "$author.last",
                "copiesSold": "$copies"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Project New Array Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#project-new-array-fields
     */
    case ProjectProjectNewArrayFields = <<<'EXTENDED_JSON'
    [
        {
            "$project": {
                "myArray": [
                    "$x",
                    "$y"
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/rankFusion/#examples
     */
    case RankFusionExample = <<<'EXTENDED_JSON'
    [
        {
            "$rankFusion": {
                "input": {
                    "pipelines": {
                        "searchPlot": [
                            {
                                "$search": {
                                    "index": "default",
                                    "text": {
                                        "query": "space",
                                        "path": "plot"
                                    }
                                }
                            }
                        ],
                        "searchGenre": [
                            {
                                "$search": {
                                    "index": "default",
                                    "text": {
                                        "query": "adventure",
                                        "path": "genres"
                                    }
                                }
                            }
                        ]
                    }
                },
                "combination": {
                    "weights": {
                        "searchPlot": {
                            "$numberDouble": "0.5999999999999999778"
                        },
                        "searchGenre": {
                            "$numberDouble": "0.4000000000000000222"
                        }
                    }
                },
                "scoreDetails": true
            }
        },
        {
            "$addFields": {
                "scoreDetails": {
                    "$meta": "searchScoreDetails"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Evaluate Access at Every Document Level
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/#evaluate-access-at-every-document-level
     */
    case RedactEvaluateAccessAtEveryDocumentLevel = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "year": {
                    "$numberInt": "2014"
                }
            }
        },
        {
            "$redact": {
                "$cond": {
                    "if": {
                        "$gt": [
                            {
                                "$size": {
                                    "$setIntersection": [
                                        "$tags",
                                        [
                                            "STLW",
                                            "G"
                                        ]
                                    ]
                                }
                            },
                            {
                                "$numberInt": "0"
                            }
                        ]
                    },
                    "then": "$$DESCEND",
                    "else": "$$PRUNE"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Exclude All Fields at a Given Level
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/#exclude-all-fields-at-a-given-level
     */
    case RedactExcludeAllFieldsAtAGivenLevel = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "status": "A"
            }
        },
        {
            "$redact": {
                "$cond": {
                    "if": {
                        "$eq": [
                            "$level",
                            {
                                "$numberInt": "5"
                            }
                        ]
                    },
                    "then": "$$PRUNE",
                    "else": "$$DESCEND"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * with an Embedded Document Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/#-replaceroot-with-an-embedded-document-field
     */
    case ReplaceRootWithAnEmbeddedDocumentField = <<<'EXTENDED_JSON'
    [
        {
            "$replaceRoot": {
                "newRoot": {
                    "$mergeObjects": [
                        {
                            "dogs": {
                                "$numberInt": "0"
                            },
                            "cats": {
                                "$numberInt": "0"
                            },
                            "birds": {
                                "$numberInt": "0"
                            },
                            "fish": {
                                "$numberInt": "0"
                            }
                        },
                        "$pets"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * with a Document Nested in an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/#-replaceroot-with-a-document-nested-in-an-array
     */
    case ReplaceRootWithADocumentNestedInAnArray = <<<'EXTENDED_JSON'
    [
        {
            "$unwind": {
                "path": "$grades"
            }
        },
        {
            "$match": {
                "grades.grade": {
                    "$gte": {
                        "$numberInt": "90"
                    }
                }
            }
        },
        {
            "$replaceRoot": {
                "newRoot": "$grades"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * with a newly created document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/#-replaceroot-with-a-newly-created-document
     */
    case ReplaceRootWithANewlyCreatedDocument = <<<'EXTENDED_JSON'
    [
        {
            "$replaceRoot": {
                "newRoot": {
                    "full_name": {
                        "$concat": [
                            "$first_name",
                            " ",
                            "$last_name"
                        ]
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * with a New Document Created from $$ROOT and a Default Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/#-replaceroot-with-a-new-document-created-from---root-and-a-default-document
     */
    case ReplaceRootWithANewDocumentCreatedFromROOTAndADefaultDocument = <<<'EXTENDED_JSON'
    [
        {
            "$replaceRoot": {
                "newRoot": {
                    "$mergeObjects": [
                        {
                            "_id": "",
                            "name": "",
                            "email": "",
                            "cell": "",
                            "home": ""
                        },
                        "$$ROOT"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * an Embedded Document Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/#-replacewith-an-embedded-document-field
     */
    case ReplaceWithAnEmbeddedDocumentField = <<<'EXTENDED_JSON'
    [
        {
            "$replaceWith": {
                "$mergeObjects": [
                    {
                        "dogs": {
                            "$numberInt": "0"
                        },
                        "cats": {
                            "$numberInt": "0"
                        },
                        "birds": {
                            "$numberInt": "0"
                        },
                        "fish": {
                            "$numberInt": "0"
                        }
                    },
                    "$pets"
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * a Document Nested in an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/#-replacewith-a-document-nested-in-an-array
     */
    case ReplaceWithADocumentNestedInAnArray = <<<'EXTENDED_JSON'
    [
        {
            "$unwind": {
                "path": "$grades"
            }
        },
        {
            "$match": {
                "grades.grade": {
                    "$gte": {
                        "$numberInt": "90"
                    }
                }
            }
        },
        {
            "$replaceWith": "$grades"
        }
    ]
    EXTENDED_JSON;

    /**
     * a Newly Created Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/#-replacewith-a-newly-created-document
     */
    case ReplaceWithANewlyCreatedDocument = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "status": "C"
            }
        },
        {
            "$replaceWith": {
                "_id": "$_id",
                "item": "$item",
                "amount": {
                    "$multiply": [
                        "$price",
                        "$quantity"
                    ]
                },
                "status": "Complete",
                "asofDate": "$$NOW"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * a New Document Created from $$ROOT and a Default Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/#-replacewith-a-new-document-created-from---root-and-a-default-document
     */
    case ReplaceWithANewDocumentCreatedFromROOTAndADefaultDocument = <<<'EXTENDED_JSON'
    [
        {
            "$replaceWith": {
                "$mergeObjects": [
                    {
                        "_id": "",
                        "name": "",
                        "email": "",
                        "cell": "",
                        "home": ""
                    },
                    "$$ROOT"
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/vector-search/query/aggregation-stages/rerank/#examples
     */
    case RerankExample = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "plot": {
                    "$exists": true,
                    "$type": [
                        "string"
                    ]
                }
            }
        },
        {
            "$sort": {
                "released": {
                    "$numberInt": "-1"
                }
            }
        },
        {
            "$rerank": {
                "model": "rerank-2.5",
                "query": {
                    "text": "a group of heroes band together to stop a powerful enemy and save the world"
                },
                "numDocsToRerank": {
                    "$numberInt": "100"
                },
                "path": [
                    "title",
                    "plot"
                ]
            }
        },
        {
            "$addFields": {
                "rerankScore": {
                    "$meta": "score"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "10"
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                },
                "plot": {
                    "$numberInt": "1"
                },
                "rerankScore": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sample/#example
     */
    case SampleExample = <<<'EXTENDED_JSON'
    [
        {
            "$sample": {
                "size": {
                    "$numberInt": "3"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/score/
     */
    case ScoreExample = <<<'EXTENDED_JSON'
    [
        {
            "$score": {
                "score": {
                    "$meta": "vectorSearchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/scoreFusion/#examples
     */
    case ScoreFusionExample = <<<'EXTENDED_JSON'
    [
        {
            "$scoreFusion": {
                "input": {
                    "pipelines": {
                        "searchOne": [
                            {
                                "$vectorSearch": {
                                    "index": "vector_index",
                                    "path": "plot_embedding",
                                    "queryVector": [
                                        {
                                            "$numberDouble": "-0.0016261311999999999121"
                                        },
                                        {
                                            "$numberDouble": "-0.028070756999999998266"
                                        },
                                        {
                                            "$numberDouble": "-0.011342932000000000015"
                                        }
                                    ],
                                    "numCandidates": {
                                        "$numberInt": "150"
                                    },
                                    "limit": {
                                        "$numberInt": "10"
                                    }
                                }
                            }
                        ],
                        "searchTwo": [
                            {
                                "$search": {
                                    "index": "<INDEX_NAME>",
                                    "text": {
                                        "query": "<QUERY_TERM>",
                                        "path": "<FIELD_NAME>"
                                    }
                                }
                            }
                        ]
                    },
                    "normalization": "sigmoid"
                },
                "combination": {
                    "method": "expression",
                    "expression": {
                        "$sum": [
                            {
                                "$multiply": [
                                    "$$searchOne",
                                    {
                                        "$numberInt": "10"
                                    }
                                ]
                            },
                            "$$searchTwo"
                        ]
                    }
                },
                "scoreDetails": true
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "1"
                },
                "title": {
                    "$numberInt": "1"
                },
                "plot": {
                    "$numberInt": "1"
                },
                "scoreDetails": {
                    "$meta": "scoreDetails"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "20"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/query-syntax/#aggregation-variable
     */
    case SearchExample = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "near": {
                    "path": "released",
                    "origin": {
                        "$date": {
                            "$numberLong": "1314835200000"
                        }
                    },
                    "pivot": {
                        "$numberLong": "7776000000"
                    }
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                },
                "released": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "5"
            }
        },
        {
            "$facet": {
                "docs": [],
                "meta": [
                    {
                        "$replaceWith": "$$SEARCH_META"
                    },
                    {
                        "$limit": {
                            "$numberInt": "1"
                        }
                    }
                ]
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Date Search and Sort
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/sort/#date-search-and-sort
     */
    case SearchDateSearchAndSort = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "range": {
                    "path": "released",
                    "gt": {
                        "$date": {
                            "$numberLong": "1262304000000"
                        }
                    },
                    "lt": {
                        "$date": {
                            "$numberLong": "1420070400000"
                        }
                    }
                },
                "sort": {
                    "released": {
                        "$numberInt": "-1"
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "5"
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                },
                "released": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Number Search and Sort
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/sort/#number-search-and-sort
     */
    case SearchNumberSearchAndSort = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "range": {
                    "path": "awards.wins",
                    "gt": {
                        "$numberInt": "3"
                    }
                },
                "sort": {
                    "awards.wins": {
                        "$numberInt": "-1"
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "5"
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                },
                "awards.wins": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Sort by score
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/sort/#sort-by-score
     */
    case SearchSortByScore = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "title",
                    "query": "story"
                },
                "sort": {
                    "score": {
                        "$meta": "searchScore",
                        "order": {
                            "$numberInt": "1"
                        }
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "5"
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                },
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Paginate results after a token
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/paginate-results/#search-after-the-reference-point
     */
    case SearchPaginateResultsAfterAToken = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "title",
                    "query": "war"
                },
                "sort": {
                    "score": {
                        "$meta": "searchScore"
                    },
                    "released": {
                        "$numberInt": "1"
                    }
                },
                "searchAfter": "CMtJGgYQuq+ngwgaCSkAjBYH7AAAAA=="
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Paginate results before a token
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/paginate-results/#search-before-the-reference-point
     */
    case SearchPaginateResultsBeforeAToken = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "title",
                    "query": "war"
                },
                "sort": {
                    "score": {
                        "$meta": "searchScore"
                    },
                    "released": {
                        "$numberInt": "1"
                    }
                },
                "searchBefore": "CJ6kARoGELqvp4MIGgkpACDA3U8BAAA="
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Count results
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/counting/#count-results
     */
    case SearchCountResults = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "near": {
                    "path": "released",
                    "origin": {
                        "$date": {
                            "$numberLong": "1314835200000"
                        }
                    },
                    "pivot": {
                        "$numberLong": "7776000000"
                    }
                },
                "count": {
                    "type": "total"
                }
            }
        },
        {
            "$project": {
                "meta": "$$SEARCH_META",
                "title": {
                    "$numberInt": "1"
                },
                "released": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "2"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Track Search terms
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/tracking/#examples
     */
    case SearchTrackSearchTerms = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "query": "summer",
                    "path": "title"
                },
                "tracking": {
                    "searchTerms": "summer"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "5"
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Return Stored Source Fields
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/return-stored-source/#examples
     */
    case SearchReturnStoredSourceFields = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "query": "baseball",
                    "path": "title"
                },
                "returnStoredSource": true
            }
        },
        {
            "$match": {
                "$or": [
                    {
                        "imdb.rating": {
                            "$gt": {
                                "$numberDouble": "8.1999999999999992895"
                            }
                        }
                    },
                    {
                        "imdb.votes": {
                            "$gte": {
                                "$numberInt": "4500"
                            }
                        }
                    }
                ]
            }
        },
        {
            "$lookup": {
                "from": "movies",
                "localField": "_id",
                "foreignField": "_id",
                "as": "document"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/query-syntax/#example
     */
    case SearchMetaExample = <<<'EXTENDED_JSON'
    [
        {
            "$searchMeta": {
                "range": {
                    "path": "year",
                    "gte": {
                        "$numberInt": "1998"
                    },
                    "lt": {
                        "$numberInt": "1999"
                    }
                },
                "count": {
                    "type": "total"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Year Facet
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/facet/#example-1
     */
    case SearchMetaYearFacet = <<<'EXTENDED_JSON'
    [
        {
            "$searchMeta": {
                "facet": {
                    "operator": {
                        "range": {
                            "path": "year",
                            "gte": {
                                "$numberInt": "1980"
                            },
                            "lte": {
                                "$numberInt": "2000"
                            }
                        }
                    },
                    "facets": {
                        "yearFacet": {
                            "type": "number",
                            "path": "year",
                            "boundaries": [
                                {
                                    "$numberInt": "1980"
                                },
                                {
                                    "$numberInt": "1990"
                                },
                                {
                                    "$numberInt": "2000"
                                }
                            ],
                            "default": "other"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Date Facet
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/facet/#example-2
     */
    case SearchMetaDateFacet = <<<'EXTENDED_JSON'
    [
        {
            "$searchMeta": {
                "facet": {
                    "operator": {
                        "range": {
                            "path": "released",
                            "gte": {
                                "$date": {
                                    "$numberLong": "946684800000"
                                }
                            },
                            "lte": {
                                "$date": {
                                    "$numberLong": "1422662400000"
                                }
                            }
                        }
                    },
                    "facets": {
                        "yearFacet": {
                            "type": "date",
                            "path": "released",
                            "boundaries": [
                                {
                                    "$date": {
                                        "$numberLong": "946684800000"
                                    }
                                },
                                {
                                    "$date": {
                                        "$numberLong": "1104537600000"
                                    }
                                },
                                {
                                    "$date": {
                                        "$numberLong": "1262304000000"
                                    }
                                },
                                {
                                    "$date": {
                                        "$numberLong": "1420070400000"
                                    }
                                }
                            ],
                            "default": "other"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Metadata Results
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/facet/#examples
     */
    case SearchMetaMetadataResults = <<<'EXTENDED_JSON'
    [
        {
            "$searchMeta": {
                "facet": {
                    "operator": {
                        "range": {
                            "path": "released",
                            "gte": {
                                "$date": {
                                    "$numberLong": "946684800000"
                                }
                            },
                            "lte": {
                                "$date": {
                                    "$numberLong": "1422662400000"
                                }
                            }
                        }
                    },
                    "facets": {
                        "directorsFacet": {
                            "type": "string",
                            "path": "directors",
                            "numBuckets": {
                                "$numberInt": "7"
                            }
                        },
                        "yearFacet": {
                            "type": "number",
                            "path": "year",
                            "boundaries": [
                                {
                                    "$numberInt": "2000"
                                },
                                {
                                    "$numberInt": "2005"
                                },
                                {
                                    "$numberInt": "2010"
                                },
                                {
                                    "$numberInt": "2015"
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
     * Autocomplete Bucket Results through Facet Queries
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/#bucket-results-through-facet-queries
     */
    case SearchMetaAutocompleteBucketResultsThroughFacetQueries = <<<'EXTENDED_JSON'
    [
        {
            "$searchMeta": {
                "facet": {
                    "operator": {
                        "autocomplete": {
                            "path": "title",
                            "query": "Gravity"
                        }
                    },
                    "facets": {
                        "titleFacet": {
                            "type": "string",
                            "path": "title"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Using Two $set Stages
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#using-two--set-stages
     */
    case SetUsingTwoSetStages = <<<'EXTENDED_JSON'
    [
        {
            "$set": {
                "totalHomework": {
                    "$sum": [
                        "$homework"
                    ]
                },
                "totalQuiz": {
                    "$sum": [
                        "$quiz"
                    ]
                }
            }
        },
        {
            "$set": {
                "totalScore": {
                    "$add": [
                        "$totalHomework",
                        "$totalQuiz",
                        "$extraCredit"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Adding Fields to an Embedded Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#adding-fields-to-an-embedded-document
     */
    case SetAddingFieldsToAnEmbeddedDocument = <<<'EXTENDED_JSON'
    [
        {
            "$set": {
                "specs.fuel_type": "unleaded"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Overwriting an existing field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#overwriting-an-existing-field
     */
    case SetOverwritingAnExistingField = <<<'EXTENDED_JSON'
    [
        {
            "$set": {
                "cats": {
                    "$numberInt": "20"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Add Element to an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#add-element-to-an-array
     */
    case SetAddElementToAnArray = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "_id": {
                    "$numberInt": "1"
                }
            }
        },
        {
            "$set": {
                "homework": {
                    "$concatArrays": [
                        "$homework",
                        [
                            {
                                "$numberInt": "7"
                            }
                        ]
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Creating a New Field with Existing Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#creating-a-new-field-with-existing-fields
     */
    case SetCreatingANewFieldWithExistingFields = <<<'EXTENDED_JSON'
    [
        {
            "$set": {
                "quizAverage": {
                    "$avg": [
                        "$quiz"
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use Documents Window to Obtain Cumulative Quantity for Each State
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-cumulative-quantity-for-each-state
     */
    case SetWindowFieldsUseDocumentsWindowToObtainCumulativeQuantityForEachState = <<<'EXTENDED_JSON'
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
                    "cumulativeQuantityForState": {
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
     * Use Documents Window to Obtain Cumulative Quantity for Each Year
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-cumulative-quantity-for-each-year
     */
    case SetWindowFieldsUseDocumentsWindowToObtainCumulativeQuantityForEachYear = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": {
                    "$year": {
                        "date": "$orderDate"
                    }
                },
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "cumulativeQuantityForYear": {
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
     * Use Documents Window to Obtain Moving Average Quantity for Each Year
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-moving-average-quantity-for-each-year
     */
    case SetWindowFieldsUseDocumentsWindowToObtainMovingAverageQuantityForEachYear = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": {
                    "$year": {
                        "date": "$orderDate"
                    }
                },
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "averageQuantity": {
                        "$avg": "$quantity",
                        "window": {
                            "documents": [
                                {
                                    "$numberInt": "-1"
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

    /**
     * Use Documents Window to Obtain Cumulative and Maximum Quantity for Each Year
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-cumulative-and-maximum-quantity-for-each-year
     */
    case SetWindowFieldsUseDocumentsWindowToObtainCumulativeAndMaximumQuantityForEachYear = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": {
                    "$year": {
                        "date": "$orderDate"
                    }
                },
                "sortBy": {
                    "orderDate": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "cumulativeQuantityForYear": {
                        "$sum": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
                                "current"
                            ]
                        }
                    },
                    "maximumQuantityForYear": {
                        "$max": "$quantity",
                        "window": {
                            "documents": [
                                "unbounded",
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
     * Range Window Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#range-window-example
     */
    case SetWindowFieldsRangeWindowExample = <<<'EXTENDED_JSON'
    [
        {
            "$setWindowFields": {
                "partitionBy": "$state",
                "sortBy": {
                    "price": {
                        "$numberInt": "1"
                    }
                },
                "output": {
                    "quantityFromSimilarOrders": {
                        "$sum": "$quantity",
                        "window": {
                            "range": [
                                {
                                    "$numberInt": "-10"
                                },
                                {
                                    "$numberInt": "10"
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
     * Use a Time Range Window with a Positive Upper Bound
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-a-time-range-window-with-a-positive-upper-bound
     */
    case SetWindowFieldsUseATimeRangeWindowWithAPositiveUpperBound = <<<'EXTENDED_JSON'
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
                    "recentOrders": {
                        "$push": "$orderDate",
                        "window": {
                            "range": [
                                "unbounded",
                                {
                                    "$numberInt": "10"
                                }
                            ],
                            "unit": "month"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Use a Time Range Window with a Negative Upper Bound
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-a-time-range-window-with-a-negative-upper-bound
     */
    case SetWindowFieldsUseATimeRangeWindowWithANegativeUpperBound = <<<'EXTENDED_JSON'
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
                    "recentOrders": {
                        "$push": "$orderDate",
                        "window": {
                            "range": [
                                "unbounded",
                                {
                                    "$numberInt": "-10"
                                }
                            ],
                            "unit": "month"
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
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shardedDataDistribution/#examples
     */
    case ShardedDataDistributionExample = <<<'EXTENDED_JSON'
    [
        {
            "$shardedDataDistribution": {}
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/skip/#example
     */
    case SkipExample = <<<'EXTENDED_JSON'
    [
        {
            "$skip": {
                "$numberInt": "5"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Ascending Descending Sort
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/#ascending-descending-sort
     */
    case SortAscendingDescendingSort = <<<'EXTENDED_JSON'
    [
        {
            "$sort": {
                "age": {
                    "$numberInt": "-1"
                },
                "posts": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Text Score Metadata Sort
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/#text-score-metadata-sort
     */
    case SortTextScoreMetadataSort = <<<'EXTENDED_JSON'
    [
        {
            "$match": {
                "$text": {
                    "$search": "operating"
                }
            }
        },
        {
            "$sort": {
                "score": {
                    "$meta": "textScore"
                },
                "posts": {
                    "$numberInt": "-1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortByCount/#example
     */
    case SortByCountExample = <<<'EXTENDED_JSON'
    [
        {
            "$unwind": {
                "path": "$tags"
            }
        },
        {
            "$sortByCount": "$tags"
        }
    ]
    EXTENDED_JSON;

    /**
     * Report 1 All Sales by Year and Stores and Items
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unionWith/#report-1--all-sales-by-year-and-stores-and-items
     */
    case UnionWithReport1AllSalesByYearAndStoresAndItems = <<<'EXTENDED_JSON'
    [
        {
            "$set": {
                "_id": "2017"
            }
        },
        {
            "$unionWith": {
                "coll": "sales_2018",
                "pipeline": [
                    {
                        "$set": {
                            "_id": "2018"
                        }
                    }
                ]
            }
        },
        {
            "$unionWith": {
                "coll": "sales_2019",
                "pipeline": [
                    {
                        "$set": {
                            "_id": "2019"
                        }
                    }
                ]
            }
        },
        {
            "$unionWith": {
                "coll": "sales_2020",
                "pipeline": [
                    {
                        "$set": {
                            "_id": "2020"
                        }
                    }
                ]
            }
        },
        {
            "$sort": {
                "_id": {
                    "$numberInt": "1"
                },
                "store": {
                    "$numberInt": "1"
                },
                "item": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Report 2 Aggregated Sales by Items
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unionWith/#report-2--aggregated-sales-by-items
     */
    case UnionWithReport2AggregatedSalesByItems = <<<'EXTENDED_JSON'
    [
        {
            "$unionWith": {
                "coll": "sales_2018"
            }
        },
        {
            "$unionWith": {
                "coll": "sales_2019"
            }
        },
        {
            "$unionWith": {
                "coll": "sales_2020"
            }
        },
        {
            "$group": {
                "_id": "$item",
                "total": {
                    "$sum": "$quantity"
                }
            }
        },
        {
            "$sort": {
                "total": {
                    "$numberInt": "-1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Remove a Single Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/#remove-a-single-field
     */
    case UnsetRemoveASingleField = <<<'EXTENDED_JSON'
    [
        {
            "$unset": [
                "copies"
            ]
        }
    ]
    EXTENDED_JSON;

    /**
     * Remove Top-Level Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/#remove-top-level-fields
     */
    case UnsetRemoveTopLevelFields = <<<'EXTENDED_JSON'
    [
        {
            "$unset": [
                "isbn",
                "copies"
            ]
        }
    ]
    EXTENDED_JSON;

    /**
     * Remove Embedded Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/#remove-embedded-fields
     */
    case UnsetRemoveEmbeddedFields = <<<'EXTENDED_JSON'
    [
        {
            "$unset": [
                "isbn",
                "author.first",
                "copies.warehouse"
            ]
        }
    ]
    EXTENDED_JSON;

    /**
     * Unwind Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#unwind-array
     */
    case UnwindUnwindArray = <<<'EXTENDED_JSON'
    [
        {
            "$unwind": {
                "path": "$sizes"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * preserveNullAndEmptyArrays
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#preservenullandemptyarrays
     */
    case UnwindPreserveNullAndEmptyArrays = <<<'EXTENDED_JSON'
    [
        {
            "$unwind": {
                "path": "$sizes",
                "preserveNullAndEmptyArrays": true
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * includeArrayIndex
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#includearrayindex
     */
    case UnwindIncludeArrayIndex = <<<'EXTENDED_JSON'
    [
        {
            "$unwind": {
                "path": "$sizes",
                "includeArrayIndex": "arrayIndex"
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Group by Unwound Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#group-by-unwound-values
     */
    case UnwindGroupByUnwoundValues = <<<'EXTENDED_JSON'
    [
        {
            "$unwind": {
                "path": "$sizes",
                "preserveNullAndEmptyArrays": true
            }
        },
        {
            "$group": {
                "_id": "$sizes",
                "averagePrice": {
                    "$avg": "$price"
                }
            }
        },
        {
            "$sort": {
                "averagePrice": {
                    "$numberInt": "-1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Unwind Embedded Arrays
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#unwind-embedded-arrays
     */
    case UnwindUnwindEmbeddedArrays = <<<'EXTENDED_JSON'
    [
        {
            "$unwind": {
                "path": "$items"
            }
        },
        {
            "$unwind": {
                "path": "$items.tags"
            }
        },
        {
            "$group": {
                "_id": "$items.tags",
                "totalSalesAmount": {
                    "$sum": {
                        "$multiply": [
                            "$items.price",
                            "$items.quantity"
                        ]
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * ANN Basic
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-stage/#examples
     */
    case VectorSearchANNBasic = <<<'EXTENDED_JSON'
    [
        {
            "$vectorSearch": {
                "index": "vector_index",
                "path": "plot_embedding",
                "queryVector": [
                    {
                        "$numberDouble": "-0.0016261311999999999121"
                    },
                    {
                        "$numberDouble": "-0.028070756999999998266"
                    },
                    {
                        "$numberDouble": "-0.011342932000000000015"
                    }
                ],
                "numCandidates": {
                    "$numberInt": "150"
                },
                "limit": {
                    "$numberInt": "10"
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "plot": {
                    "$numberInt": "1"
                },
                "title": {
                    "$numberInt": "1"
                },
                "score": {
                    "$meta": "vectorSearchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * ANN Filter
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-stage/#examples
     */
    case VectorSearchANNFilter = <<<'EXTENDED_JSON'
    [
        {
            "$vectorSearch": {
                "index": "vector_index",
                "path": "plot_embedding",
                "filter": {
                    "$and": [
                        {
                            "year": {
                                "$lt": {
                                    "$numberInt": "1975"
                                }
                            }
                        }
                    ]
                },
                "queryVector": [
                    {
                        "$numberDouble": "0.024210530000000000939"
                    },
                    {
                        "$numberDouble": "-0.022372592000000000173"
                    },
                    {
                        "$numberDouble": "-0.0062311370000000003075"
                    }
                ],
                "numCandidates": {
                    "$numberInt": "150"
                },
                "limit": {
                    "$numberInt": "10"
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                },
                "plot": {
                    "$numberInt": "1"
                },
                "year": {
                    "$numberInt": "1"
                },
                "score": {
                    "$meta": "vectorSearchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * ENN
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-stage/#examples
     */
    case VectorSearchENN = <<<'EXTENDED_JSON'
    [
        {
            "$vectorSearch": {
                "index": "vector_index",
                "path": "plot_embedding",
                "queryVector": [
                    {
                        "$numberDouble": "-0.0069540970000000002296"
                    },
                    {
                        "$numberDouble": "-0.009932498999999999148"
                    },
                    {
                        "$numberDouble": "-0.0013114739999999999731"
                    }
                ],
                "exact": true,
                "limit": {
                    "$numberInt": "10"
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "plot": {
                    "$numberInt": "1"
                },
                "title": {
                    "$numberInt": "1"
                },
                "score": {
                    "$meta": "vectorSearchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Stored Source
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-stage/#examples
     */
    case VectorSearchStoredSource = <<<'EXTENDED_JSON'
    [
        {
            "$vectorSearch": {
                "index": "vector_index",
                "path": "plot_embedding",
                "queryVector": [
                    {
                        "$numberDouble": "-0.039948012679815292358"
                    },
                    {
                        "$numberDouble": "-0.016522614285349845886"
                    },
                    {
                        "$numberDouble": "-0.0087753441184759140015"
                    }
                ],
                "filter": {
                    "$and": [
                        {
                            "year": {
                                "$gt": {
                                    "$numberInt": "1970"
                                }
                            }
                        },
                        {
                            "year": {
                                "$lt": {
                                    "$numberInt": "2020"
                                }
                            }
                        },
                        {
                            "genres": {
                                "$in": [
                                    "Action",
                                    "Drama",
                                    "Comedy"
                                ]
                            }
                        }
                    ]
                },
                "limit": {
                    "$numberInt": "10"
                },
                "numCandidates": {
                    "$numberInt": "1000"
                },
                "returnStoredSource": true
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "plot": {
                    "$numberInt": "1"
                },
                "title": {
                    "$numberInt": "1"
                },
                "genres": {
                    "$numberInt": "1"
                },
                "score": {
                    "$meta": "vectorSearchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Nested field
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-vector-search/vector-search-stage/#examples
     */
    case VectorSearchNestedField = <<<'EXTENDED_JSON'
    [
        {
            "$vectorSearch": {
                "index": "vector_index",
                "filter": {
                    "reviews.date": {
                        "$gte": {
                            "$date": {
                                "$numberLong": "946684800000"
                            }
                        }
                    }
                },
                "parentFilter": {
                    "address.country": {
                        "$in": [
                            "United States"
                        ]
                    },
                    "bedrooms": {
                        "$gte": {
                            "$numberInt": "2"
                        },
                        "$lte": {
                            "$numberInt": "3"
                        }
                    },
                    "property_type": {
                        "$in": [
                            "House",
                            "Apartment"
                        ]
                    }
                },
                "path": "reviews.comments_embedding",
                "queryVector": [
                    {
                        "$numberDouble": "0.01074588485062122345"
                    },
                    {
                        "$numberDouble": "-0.03567341342568397522"
                    },
                    {
                        "$numberDouble": "-0.092984795570373535156"
                    }
                ],
                "numCandidates": {
                    "$numberInt": "100"
                },
                "limit": {
                    "$numberInt": "5"
                },
                "nestedOptions": {
                    "scoreMode": "avg"
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
                "address": {
                    "$numberInt": "1"
                },
                "neighborhood_overview": {
                    "$numberInt": "1"
                },
                "bedrooms": {
                    "$numberInt": "1"
                },
                "property_type": {
                    "$numberInt": "1"
                },
                "reviews.comments": {
                    "$numberInt": "1"
                },
                "score": {
                    "$meta": "vectorSearchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Automated Embedding
     *
     * @see https://www.mongodb.com/docs/vector-search/crud-embeddings/automated-embedding/
     */
    case VectorSearchAutomatedEmbedding = <<<'EXTENDED_JSON'
    [
        {
            "$vectorSearch": {
                "index": "vector_index",
                "path": "plot_embedding",
                "query": "time travel",
                "numCandidates": {
                    "$numberInt": "150"
                },
                "limit": {
                    "$numberInt": "10"
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "plot": {
                    "$numberInt": "1"
                },
                "title": {
                    "$numberInt": "1"
                },
                "score": {
                    "$meta": "vectorSearchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Automated Embedding with model override
     *
     * @see https://www.mongodb.com/docs/vector-search/crud-embeddings/automated-embedding/
     */
    case VectorSearchAutomatedEmbeddingWithModelOverride = <<<'EXTENDED_JSON'
    [
        {
            "$vectorSearch": {
                "index": "vector_index",
                "path": "plot_embedding",
                "query": "time travel",
                "model": "voyage-3-lite",
                "numCandidates": {
                    "$numberInt": "150"
                },
                "limit": {
                    "$numberInt": "10"
                }
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "plot": {
                    "$numberInt": "1"
                },
                "title": {
                    "$numberInt": "1"
                },
                "score": {
                    "$meta": "vectorSearchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;
}
