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
    case AddFieldsUsingTwoAddFieldsStages = <<<'JSON'
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
    JSON;

    /**
     * Adding Fields to an Embedded Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/#adding-fields-to-an-embedded-document
     */
    case AddFieldsAddingFieldsToAnEmbeddedDocument = <<<'JSON'
    [
        {
            "$addFields": {
                "specs.fuel_type": "unleaded"
            }
        }
    ]
    JSON;

    /**
     * Overwriting an existing field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/#overwriting-an-existing-field
     */
    case AddFieldsOverwritingAnExistingField = <<<'JSON'
    [
        {
            "$addFields": {
                "cats": {
                    "$numberInt": "20"
                }
            }
        }
    ]
    JSON;

    /**
     * Add Element to an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/addFields/#add-element-to-an-array
     */
    case AddFieldsAddElementToAnArray = <<<'JSON'
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
    JSON;

    /**
     * Bucket by Year and Filter by Bucket Results
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucket/#bucket-by-year-and-filter-by-bucket-results
     */
    case BucketBucketByYearAndFilterByBucketResults = <<<'JSON'
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
    JSON;

    /**
     * Use $bucket with $facet to Bucket by Multiple Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucket/#use--bucket-with--facet-to-bucket-by-multiple-fields
     */
    case BucketUseBucketWithFacetToBucketByMultipleFields = <<<'JSON'
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
    JSON;

    /**
     * Single Facet Aggregation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/bucketAuto/#single-facet-aggregation
     */
    case BucketAutoSingleFacetAggregation = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStream/#examples
     */
    case ChangeStreamExample = <<<'JSON'
    [
        {
            "$changeStream": {}
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStreamSplitLargeEvent/#example
     */
    case ChangeStreamSplitLargeEventExample = <<<'JSON'
    [
        {
            "$changeStreamSplitLargeEvent": {}
        }
    ]
    JSON;

    /**
     * latencyStats Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/#latencystats-document
     */
    case CollStatsLatencyStatsDocument = <<<'JSON'
    [
        {
            "$collStats": {
                "latencyStats": {
                    "histograms": true
                }
            }
        }
    ]
    JSON;

    /**
     * storageStats Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/#storagestats-document
     */
    case CollStatsStorageStatsDocument = <<<'JSON'
    [
        {
            "$collStats": {
                "storageStats": {}
            }
        }
    ]
    JSON;

    /**
     * count Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/#count-field
     */
    case CollStatsCountField = <<<'JSON'
    [
        {
            "$collStats": {
                "count": {}
            }
        }
    ]
    JSON;

    /**
     * queryExecStats Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/collStats/#queryexecstats-document
     */
    case CollStatsQueryExecStatsDocument = <<<'JSON'
    [
        {
            "$collStats": {
                "queryExecStats": {}
            }
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/count/#example
     */
    case CountExample = <<<'JSON'
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
    JSON;

    /**
     * Inactive Sessions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/currentOp/#inactive-sessions
     */
    case CurrentOpInactiveSessions = <<<'JSON'
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
    JSON;

    /**
     * Sampled Queries
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/currentOp/#sampled-queries
     */
    case CurrentOpSampledQueries = <<<'JSON'
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
    JSON;

    /**
     * Densify Time Series Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/densify/#densify-time-series-data
     */
    case DensifyDensifyTimeSeriesData = <<<'JSON'
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
    JSON;

    /**
     * Densifiction with Partitions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/densify/#densifiction-with-partitions
     */
    case DensifyDensifictionWithPartitions = <<<'JSON'
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
    JSON;

    /**
     * Test a Pipeline Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documents/#test-a-pipeline-stage
     */
    case DocumentsTestAPipelineStage = <<<'JSON'
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
    JSON;

    /**
     * Use a $documents Stage in a $lookup Stage
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/documents/#use-a--documents-stage-in-a--lookup-stage
     */
    case DocumentsUseADocumentsStageInALookupStage = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/facet/#example
     */
    case FacetExample = <<<'JSON'
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
    JSON;

    /**
     * Fill Missing Field Values with a Constant Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#fill-missing-field-values-with-a-constant-value
     */
    case FillFillMissingFieldValuesWithAConstantValue = <<<'JSON'
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
    JSON;

    /**
     * Fill Missing Field Values with Linear Interpolation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#fill-missing-field-values-with-linear-interpolation
     */
    case FillFillMissingFieldValuesWithLinearInterpolation = <<<'JSON'
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
    JSON;

    /**
     * Fill Missing Field Values Based on the Last Observed Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#fill-missing-field-values-based-on-the-last-observed-value
     */
    case FillFillMissingFieldValuesBasedOnTheLastObservedValue = <<<'JSON'
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
    JSON;

    /**
     * Fill Data for Distinct Partitions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#fill-data-for-distinct-partitions
     */
    case FillFillDataForDistinctPartitions = <<<'JSON'
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
    JSON;

    /**
     * Indicate if a Field was Populated Using $fill
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/fill/#indicate-if-a-field-was-populated-using--fill
     */
    case FillIndicateIfAFieldWasPopulatedUsingFill = <<<'JSON'
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
    JSON;

    /**
     * Maximum Distance
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#maximum-distance
     */
    case GeoNearMaximumDistance = <<<'JSON'
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
    JSON;

    /**
     * Minimum Distance
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#minimum-distance
     */
    case GeoNearMinimumDistance = <<<'JSON'
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
    JSON;

    /**
     * with the let option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#-geonear-with-the-let-option
     */
    case GeoNearWithTheLetOption = <<<'JSON'
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
    JSON;

    /**
     * with Bound let Option
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#-geonear-with-bound-let-option
     */
    case GeoNearWithBoundLetOption = <<<'JSON'
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
    JSON;

    /**
     * Specify Which Geospatial Index to Use
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/geoNear/#specify-which-geospatial-index-to-use
     */
    case GeoNearSpecifyWhichGeospatialIndexToUse = <<<'JSON'
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
    JSON;

    /**
     * Within a Single Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/#within-a-single-collection
     */
    case GraphLookupWithinASingleCollection = <<<'JSON'
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
    JSON;

    /**
     * Across Multiple Collections
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/#across-multiple-collections
     */
    case GraphLookupAcrossMultipleCollections = <<<'JSON'
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
    JSON;

    /**
     * With a Query Filter
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/graphLookup/#with-a-query-filter
     */
    case GraphLookupWithAQueryFilter = <<<'JSON'
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
    JSON;

    /**
     * Count the Number of Documents in a Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#count-the-number-of-documents-in-a-collection
     */
    case GroupCountTheNumberOfDocumentsInACollection = <<<'JSON'
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
    JSON;

    /**
     * Retrieve Distinct Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#retrieve-distinct-values
     */
    case GroupRetrieveDistinctValues = <<<'JSON'
    [
        {
            "$group": {
                "_id": "$item"
            }
        }
    ]
    JSON;

    /**
     * Group by Item Having
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#group-by-item-having
     */
    case GroupGroupByItemHaving = <<<'JSON'
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
    JSON;

    /**
     * Calculate Count Sum and Average
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#calculate-count--sum--and-average
     */
    case GroupCalculateCountSumAndAverage = <<<'JSON'
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
    JSON;

    /**
     * Group by null
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#group-by-null
     */
    case GroupGroupByNull = <<<'JSON'
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
    JSON;

    /**
     * Pivot Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#pivot-data
     */
    case GroupPivotData = <<<'JSON'
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
    JSON;

    /**
     * Group Documents by author
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/group/#group-documents-by-author
     */
    case GroupGroupDocumentsByAuthor = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/indexStats/#example
     */
    case IndexStatsExample = <<<'JSON'
    [
        {
            "$indexStats": {}
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/limit/#example
     */
    case LimitExample = <<<'JSON'
    [
        {
            "$limit": {
                "$numberInt": "5"
            }
        }
    ]
    JSON;

    /**
     * List All Local Sessions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listLocalSessions/#list-all-local-sessions
     */
    case ListLocalSessionsListAllLocalSessions = <<<'JSON'
    [
        {
            "$listLocalSessions": {
                "allUsers": true
            }
        }
    ]
    JSON;

    /**
     * List All Local Sessions for the Specified Users
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listLocalSessions/#list-all-local-sessions-for-the-specified-users
     */
    case ListLocalSessionsListAllLocalSessionsForTheSpecifiedUsers = <<<'JSON'
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
    JSON;

    /**
     * List All Local Sessions for the Current User
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listLocalSessions/#list-all-local-sessions-for-the-current-user
     */
    case ListLocalSessionsListAllLocalSessionsForTheCurrentUser = <<<'JSON'
    [
        {
            "$listLocalSessions": {}
        }
    ]
    JSON;

    /**
     * List Sampled Queries for All Collections
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSampledQueries/#list-sampled-queries-for-all-collections
     */
    case ListSampledQueriesListSampledQueriesForAllCollections = <<<'JSON'
    [
        {
            "$listSampledQueries": {}
        }
    ]
    JSON;

    /**
     * List Sampled Queries for A Specific Collection
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSampledQueries/#list-sampled-queries-for-a-specific-collection
     */
    case ListSampledQueriesListSampledQueriesForASpecificCollection = <<<'JSON'
    [
        {
            "$listSampledQueries": {
                "namespace": "social.post"
            }
        }
    ]
    JSON;

    /**
     * Return All Search Indexes
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/#return-all-search-indexes
     */
    case ListSearchIndexesReturnAllSearchIndexes = <<<'JSON'
    [
        {
            "$listSearchIndexes": {}
        }
    ]
    JSON;

    /**
     * Return a Single Search Index by Name
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/#return-a-single-search-index-by-name
     */
    case ListSearchIndexesReturnASingleSearchIndexByName = <<<'JSON'
    [
        {
            "$listSearchIndexes": {
                "name": "synonym-mappings"
            }
        }
    ]
    JSON;

    /**
     * Return a Single Search Index by id
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSearchIndexes/#return-a-single-search-index-by-id
     */
    case ListSearchIndexesReturnASingleSearchIndexById = <<<'JSON'
    [
        {
            "$listSearchIndexes": {
                "id": "6524096020da840844a4c4a7"
            }
        }
    ]
    JSON;

    /**
     * List All Sessions
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSessions/#list-all-sessions
     */
    case ListSessionsListAllSessions = <<<'JSON'
    [
        {
            "$listSessions": {
                "allUsers": true
            }
        }
    ]
    JSON;

    /**
     * List All Sessions for the Specified Users
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSessions/#list-all-sessions-for-the-specified-users
     */
    case ListSessionsListAllSessionsForTheSpecifiedUsers = <<<'JSON'
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
    JSON;

    /**
     * List All Sessions for the Current User
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/listSessions/#list-all-sessions-for-the-current-user
     */
    case ListSessionsListAllSessionsForTheCurrentUser = <<<'JSON'
    [
        {
            "$listSessions": {}
        }
    ]
    JSON;

    /**
     * Perform a Single Equality Join with $lookup
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#perform-a-single-equality-join-with--lookup
     */
    case LookupPerformASingleEqualityJoinWithLookup = <<<'JSON'
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
    JSON;

    /**
     * Use $lookup with an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#use--lookup-with-an-array
     */
    case LookupUseLookupWithAnArray = <<<'JSON'
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
    JSON;

    /**
     * Use $lookup with $mergeObjects
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#use--lookup-with--mergeobjects
     */
    case LookupUseLookupWithMergeObjects = <<<'JSON'
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
     * Perform Multiple Joins and a Correlated Subquery with $lookup
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#perform-multiple-joins-and-a-correlated-subquery-with--lookup
     */
    case LookupPerformMultipleJoinsAndACorrelatedSubqueryWithLookup = <<<'JSON'
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
    JSON;

    /**
     * Perform an Uncorrelated Subquery with $lookup
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#perform-an-uncorrelated-subquery-with--lookup
     */
    case LookupPerformAnUncorrelatedSubqueryWithLookup = <<<'JSON'
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
    JSON;

    /**
     * Perform a Concise Correlated Subquery with $lookup
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/lookup/#perform-a-concise-correlated-subquery-with--lookup
     */
    case LookupPerformAConciseCorrelatedSubqueryWithLookup = <<<'JSON'
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
    JSON;

    /**
     * Equality Match
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/#equality-match
     */
    case MatchEqualityMatch = <<<'JSON'
    [
        {
            "$match": {
                "author": "dave"
            }
        }
    ]
    JSON;

    /**
     * Perform a Count
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/match/#perform-a-count
     */
    case MatchPerformACount = <<<'JSON'
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
    JSON;

    /**
     * On-Demand Materialized View Initial Creation
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#on-demand-materialized-view--initial-creation
     */
    case MergeOnDemandMaterializedViewInitialCreation = <<<'JSON'
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
    JSON;

    /**
     * On-Demand Materialized View Update Replace Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#on-demand-materialized-view--update-replace-data
     */
    case MergeOnDemandMaterializedViewUpdateReplaceData = <<<'JSON'
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
    JSON;

    /**
     * Only Insert New Data
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#only-insert-new-data
     */
    case MergeOnlyInsertNewData = <<<'JSON'
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
    JSON;

    /**
     * Merge Results from Multiple Collections
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#merge-results-from-multiple-collections
     */
    case MergeMergeResultsFromMultipleCollections = <<<'JSON'
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
    JSON;

    /**
     * Use the Pipeline to Customize the Merge
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#use-the-pipeline-to-customize-the-merge
     */
    case MergeUseThePipelineToCustomizeTheMerge = <<<'JSON'
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
    JSON;

    /**
     * Use Variables to Customize the Merge
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/merge/#use-variables-to-customize-the-merge
     */
    case MergeUseVariablesToCustomizeTheMerge = <<<'JSON'
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
    JSON;

    /**
     * Output to Same Database
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/#output-to-same-database
     */
    case OutOutputToSameDatabase = <<<'JSON'
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
            "$out": "authors"
        }
    ]
    JSON;

    /**
     * Output to a Different Database
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/out/#output-to-a-different-database
     */
    case OutOutputToADifferentDatabase = <<<'JSON'
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
    JSON;

    /**
     * Return Information for All Entries in the Query Cache
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/planCacheStats/#return-information-for-all-entries-in-the-query-cache
     */
    case PlanCacheStatsReturnInformationForAllEntriesInTheQueryCache = <<<'JSON'
    [
        {
            "$planCacheStats": {}
        }
    ]
    JSON;

    /**
     * Find Cache Entry Details for a Query Hash
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/planCacheStats/#find-cache-entry-details-for-a-query-hash
     */
    case PlanCacheStatsFindCacheEntryDetailsForAQueryHash = <<<'JSON'
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
    JSON;

    /**
     * Include Specific Fields in Output Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#include-specific-fields-in-output-documents
     */
    case ProjectIncludeSpecificFieldsInOutputDocuments = <<<'JSON'
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
    JSON;

    /**
     * Suppress id Field in the Output Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#suppress-_id-field-in-the-output-documents
     */
    case ProjectSuppressIdFieldInTheOutputDocuments = <<<'JSON'
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
    JSON;

    /**
     * Exclude Fields from Output Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#exclude-fields-from-output-documents
     */
    case ProjectExcludeFieldsFromOutputDocuments = <<<'JSON'
    [
        {
            "$project": {
                "lastModified": {
                    "$numberInt": "0"
                }
            }
        }
    ]
    JSON;

    /**
     * Exclude Fields from Embedded Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#exclude-fields-from-embedded-documents
     */
    case ProjectExcludeFieldsFromEmbeddedDocuments = <<<'JSON'
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
    JSON;

    /**
     * Conditionally Exclude Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#conditionally-exclude-fields
     */
    case ProjectConditionallyExcludeFields = <<<'JSON'
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
    JSON;

    /**
     * Include Specific Fields from Embedded Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#include-specific-fields-from-embedded-documents
     */
    case ProjectIncludeSpecificFieldsFromEmbeddedDocuments = <<<'JSON'
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
    JSON;

    /**
     * Include Computed Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#include-computed-fields
     */
    case ProjectIncludeComputedFields = <<<'JSON'
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
    JSON;

    /**
     * Project New Array Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/project/#project-new-array-fields
     */
    case ProjectProjectNewArrayFields = <<<'JSON'
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
    JSON;

    /**
     * Evaluate Access at Every Document Level
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/#evaluate-access-at-every-document-level
     */
    case RedactEvaluateAccessAtEveryDocumentLevel = <<<'JSON'
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
    JSON;

    /**
     * Exclude All Fields at a Given Level
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/redact/#exclude-all-fields-at-a-given-level
     */
    case RedactExcludeAllFieldsAtAGivenLevel = <<<'JSON'
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
    JSON;

    /**
     * with an Embedded Document Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/#-replaceroot-with-an-embedded-document-field
     */
    case ReplaceRootWithAnEmbeddedDocumentField = <<<'JSON'
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
    JSON;

    /**
     * with a Document Nested in an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/#-replaceroot-with-a-document-nested-in-an-array
     */
    case ReplaceRootWithADocumentNestedInAnArray = <<<'JSON'
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
    JSON;

    /**
     * with a newly created document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/#-replaceroot-with-a-newly-created-document
     */
    case ReplaceRootWithANewlyCreatedDocument = <<<'JSON'
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
    JSON;

    /**
     * with a New Document Created from $$ROOT and a Default Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceRoot/#-replaceroot-with-a-new-document-created-from---root-and-a-default-document
     */
    case ReplaceRootWithANewDocumentCreatedFromROOTAndADefaultDocument = <<<'JSON'
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
    JSON;

    /**
     * an Embedded Document Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/#-replacewith-an-embedded-document-field
     */
    case ReplaceWithAnEmbeddedDocumentField = <<<'JSON'
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
    JSON;

    /**
     * a Document Nested in an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/#-replacewith-a-document-nested-in-an-array
     */
    case ReplaceWithADocumentNestedInAnArray = <<<'JSON'
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
    JSON;

    /**
     * a Newly Created Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/#-replacewith-a-newly-created-document
     */
    case ReplaceWithANewlyCreatedDocument = <<<'JSON'
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
    JSON;

    /**
     * a New Document Created from $$ROOT and a Default Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/replaceWith/#-replacewith-a-new-document-created-from---root-and-a-default-document
     */
    case ReplaceWithANewDocumentCreatedFromROOTAndADefaultDocument = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sample/#example
     */
    case SampleExample = <<<'JSON'
    [
        {
            "$sample": {
                "size": {
                    "$numberInt": "3"
                }
            }
        }
    ]
    JSON;

    /**
     * Using Two $set Stages
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#using-two--set-stages
     */
    case SetUsingTwoSetStages = <<<'JSON'
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
    JSON;

    /**
     * Adding Fields to an Embedded Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#adding-fields-to-an-embedded-document
     */
    case SetAddingFieldsToAnEmbeddedDocument = <<<'JSON'
    [
        {
            "$set": {
                "specs.fuel_type": "unleaded"
            }
        }
    ]
    JSON;

    /**
     * Overwriting an existing field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#overwriting-an-existing-field
     */
    case SetOverwritingAnExistingField = <<<'JSON'
    [
        {
            "$set": {
                "cats": {
                    "$numberInt": "20"
                }
            }
        }
    ]
    JSON;

    /**
     * Add Element to an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#add-element-to-an-array
     */
    case SetAddElementToAnArray = <<<'JSON'
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
    JSON;

    /**
     * Creating a New Field with Existing Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/set/#creating-a-new-field-with-existing-fields
     */
    case SetCreatingANewFieldWithExistingFields = <<<'JSON'
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
    JSON;

    /**
     * Use Documents Window to Obtain Cumulative Quantity for Each State
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-cumulative-quantity-for-each-state
     */
    case SetWindowFieldsUseDocumentsWindowToObtainCumulativeQuantityForEachState = <<<'JSON'
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
    JSON;

    /**
     * Use Documents Window to Obtain Cumulative Quantity for Each Year
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-cumulative-quantity-for-each-year
     */
    case SetWindowFieldsUseDocumentsWindowToObtainCumulativeQuantityForEachYear = <<<'JSON'
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
    JSON;

    /**
     * Use Documents Window to Obtain Moving Average Quantity for Each Year
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-moving-average-quantity-for-each-year
     */
    case SetWindowFieldsUseDocumentsWindowToObtainMovingAverageQuantityForEachYear = <<<'JSON'
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
    JSON;

    /**
     * Use Documents Window to Obtain Cumulative and Maximum Quantity for Each Year
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-documents-window-to-obtain-cumulative-and-maximum-quantity-for-each-year
     */
    case SetWindowFieldsUseDocumentsWindowToObtainCumulativeAndMaximumQuantityForEachYear = <<<'JSON'
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
    JSON;

    /**
     * Range Window Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#range-window-example
     */
    case SetWindowFieldsRangeWindowExample = <<<'JSON'
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
    JSON;

    /**
     * Use a Time Range Window with a Positive Upper Bound
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-a-time-range-window-with-a-positive-upper-bound
     */
    case SetWindowFieldsUseATimeRangeWindowWithAPositiveUpperBound = <<<'JSON'
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
    JSON;

    /**
     * Use a Time Range Window with a Negative Upper Bound
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/setWindowFields/#use-a-time-range-window-with-a-negative-upper-bound
     */
    case SetWindowFieldsUseATimeRangeWindowWithANegativeUpperBound = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/shardedDataDistribution/#examples
     */
    case ShardedDataDistributionExample = <<<'JSON'
    [
        {
            "$shardedDataDistribution": {}
        }
    ]
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/skip/#example
     */
    case SkipExample = <<<'JSON'
    [
        {
            "$skip": {
                "$numberInt": "5"
            }
        }
    ]
    JSON;

    /**
     * Ascending Descending Sort
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/#ascending-descending-sort
     */
    case SortAscendingDescendingSort = <<<'JSON'
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
    JSON;

    /**
     * Text Score Metadata Sort
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sort/#text-score-metadata-sort
     */
    case SortTextScoreMetadataSort = <<<'JSON'
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
    JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/sortByCount/#example
     */
    case SortByCountExample = <<<'JSON'
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
    JSON;

    /**
     * Report 1 All Sales by Year and Stores and Items
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unionWith/#report-1--all-sales-by-year-and-stores-and-items
     */
    case UnionWithReport1AllSalesByYearAndStoresAndItems = <<<'JSON'
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
    JSON;

    /**
     * Report 2 Aggregated Sales by Items
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unionWith/#report-2--aggregated-sales-by-items
     */
    case UnionWithReport2AggregatedSalesByItems = <<<'JSON'
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
    JSON;

    /**
     * Remove a Single Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/#remove-a-single-field
     */
    case UnsetRemoveASingleField = <<<'JSON'
    [
        {
            "$unset": [
                "copies"
            ]
        }
    ]
    JSON;

    /**
     * Remove Top-Level Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/#remove-top-level-fields
     */
    case UnsetRemoveTopLevelFields = <<<'JSON'
    [
        {
            "$unset": [
                "isbn",
                "copies"
            ]
        }
    ]
    JSON;

    /**
     * Remove Embedded Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unset/#remove-embedded-fields
     */
    case UnsetRemoveEmbeddedFields = <<<'JSON'
    [
        {
            "$unset": [
                "isbn",
                "author.first",
                "copies.warehouse"
            ]
        }
    ]
    JSON;

    /**
     * Unwind Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#unwind-array
     */
    case UnwindUnwindArray = <<<'JSON'
    [
        {
            "$unwind": {
                "path": "$sizes"
            }
        }
    ]
    JSON;

    /**
     * preserveNullAndEmptyArrays
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#preservenullandemptyarrays
     */
    case UnwindPreserveNullAndEmptyArrays = <<<'JSON'
    [
        {
            "$unwind": {
                "path": "$sizes",
                "preserveNullAndEmptyArrays": true
            }
        }
    ]
    JSON;

    /**
     * includeArrayIndex
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#includearrayindex
     */
    case UnwindIncludeArrayIndex = <<<'JSON'
    [
        {
            "$unwind": {
                "path": "$sizes",
                "includeArrayIndex": "arrayIndex"
            }
        }
    ]
    JSON;

    /**
     * Group by Unwound Values
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#group-by-unwound-values
     */
    case UnwindGroupByUnwoundValues = <<<'JSON'
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
    JSON;

    /**
     * Unwind Embedded Arrays
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/unwind/#unwind-embedded-arrays
     */
    case UnwindUnwindEmbeddedArrays = <<<'JSON'
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
    JSON;
}
