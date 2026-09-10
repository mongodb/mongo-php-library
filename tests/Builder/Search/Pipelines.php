<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Search;

enum Pipelines: string
{
    /**
     * Basic
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/#basic-example
     */
    case AutocompleteBasic = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "autocomplete": {
                    "query": "off",
                    "path": "title"
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fuzzy
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/#fuzzy-example
     */
    case AutocompleteFuzzy = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "autocomplete": {
                    "query": "pre",
                    "path": "title",
                    "fuzzy": {
                        "maxEdits": {
                            "$numberInt": "1"
                        },
                        "prefixLength": {
                            "$numberInt": "1"
                        },
                        "maxExpansions": {
                            "$numberInt": "256"
                        }
                    }
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Token Order any
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/#simple-any-example
     */
    case AutocompleteTokenOrderAny = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "autocomplete": {
                    "query": "men with",
                    "path": "title",
                    "tokenOrder": "any"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "4"
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
     * Token Order sequential
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/#simple-sequential-example
     */
    case AutocompleteTokenOrderSequential = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "autocomplete": {
                    "query": "men with",
                    "path": "title",
                    "tokenOrder": "sequential"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "4"
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
     * Highlighting
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/#highlighting-example
     */
    case AutocompleteHighlighting = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "autocomplete": {
                    "query": "ger",
                    "path": "title"
                },
                "highlight": {
                    "path": "title"
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
                "score": {
                    "$meta": "searchScore"
                },
                "_id": {
                    "$numberInt": "0"
                },
                "title": {
                    "$numberInt": "1"
                },
                "highlights": {
                    "$meta": "searchHighlights"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Across Multiple Fields
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/autocomplete/#search-across-multiple-fields
     */
    case AutocompleteAcrossMultipleFields = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "should": [
                        {
                            "autocomplete": {
                                "query": "inter",
                                "path": "title"
                            }
                        },
                        {
                            "autocomplete": {
                                "query": "inter",
                                "path": "plot"
                            }
                        }
                    ],
                    "minimumShouldMatch": {
                        "$numberInt": "1"
                    }
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * must and mustNot
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/compound/#must-and-mustnot-example
     */
    case CompoundMustAndMustNot = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "must": [
                        {
                            "text": {
                                "query": "varieties",
                                "path": "description"
                            }
                        }
                    ],
                    "mustNot": [
                        {
                            "text": {
                                "query": "apples",
                                "path": "description"
                            }
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * must and should
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/compound/#must-and-should-example
     */
    case CompoundMustAndShould = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "must": [
                        {
                            "text": {
                                "query": "varieties",
                                "path": "description"
                            }
                        }
                    ],
                    "should": [
                        {
                            "text": {
                                "query": "Fuji",
                                "path": "description"
                            }
                        }
                    ]
                }
            }
        },
        {
            "$project": {
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * minimumShouldMatch
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/compound/#minimumshouldmatch-example
     */
    case CompoundMinimumShouldMatch = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "must": [
                        {
                            "text": {
                                "query": "varieties",
                                "path": "description"
                            }
                        }
                    ],
                    "should": [
                        {
                            "text": {
                                "query": "Fuji",
                                "path": "description"
                            }
                        },
                        {
                            "text": {
                                "query": "Golden Delicious",
                                "path": "description"
                            }
                        }
                    ],
                    "minimumShouldMatch": {
                        "$numberInt": "1"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Filter
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/compound/#filter-examples
     */
    case CompoundFilter = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "must": [
                        {
                            "text": {
                                "query": "varieties",
                                "path": "description"
                            }
                        }
                    ],
                    "should": [
                        {
                            "text": {
                                "query": "banana",
                                "path": "description"
                            }
                        }
                    ],
                    "filter": [
                        {
                            "text": {
                                "query": "granny",
                                "path": "description"
                            }
                        }
                    ]
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Nested
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/compound/#nested-example
     */
    case CompoundNested = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "should": [
                        {
                            "text": {
                                "query": "apple",
                                "path": "type"
                            }
                        },
                        {
                            "compound": {
                                "must": [
                                    {
                                        "text": {
                                            "query": "organic",
                                            "path": "category"
                                        }
                                    },
                                    {
                                        "equals": {
                                            "value": true,
                                            "path": "in_stock"
                                        }
                                    }
                                ]
                            }
                        }
                    ],
                    "minimumShouldMatch": {
                        "$numberInt": "1"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Basic
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/embedded-document/#index-definition
     */
    case EmbeddedDocumentBasic = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "embeddedDocument": {
                    "path": "items",
                    "operator": {
                        "compound": {
                            "must": [
                                {
                                    "text": {
                                        "path": "items.tags",
                                        "query": "school"
                                    }
                                }
                            ],
                            "should": [
                                {
                                    "text": {
                                        "path": "items.name",
                                        "query": "backpack"
                                    }
                                }
                            ]
                        }
                    },
                    "score": {
                        "embedded": {
                            "aggregate": "mean"
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
                "items.name": {
                    "$numberInt": "1"
                },
                "items.tags": {
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
     * Facet
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/embedded-document/#facet-query
     */
    case EmbeddedDocumentFacet = <<<'EXTENDED_JSON'
    [
        {
            "$searchMeta": {
                "facet": {
                    "operator": {
                        "embeddedDocument": {
                            "path": "items",
                            "operator": {
                                "compound": {
                                    "must": [
                                        {
                                            "text": {
                                                "path": "items.tags",
                                                "query": "school"
                                            }
                                        }
                                    ],
                                    "should": [
                                        {
                                            "text": {
                                                "path": "items.name",
                                                "query": "backpack"
                                            }
                                        }
                                    ]
                                }
                            }
                        }
                    },
                    "facets": {
                        "purchaseMethodFacet": {
                            "type": "string",
                            "path": "purchaseMethod"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Query and Sort
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/embedded-document/#query-and-sort
     */
    case EmbeddedDocumentQueryAndSort = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "embeddedDocument": {
                    "path": "items",
                    "operator": {
                        "text": {
                            "path": "items.name",
                            "query": "laptop"
                        }
                    }
                },
                "sort": {
                    "items.tags": {
                        "$numberInt": "1"
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
                "items.name": {
                    "$numberInt": "1"
                },
                "items.tags": {
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
     * Query for Matching Embedded Documents Only
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/embedded-document/#query-for-matching-embedded-documents-only
     */
    case EmbeddedDocumentQueryForMatchingEmbeddedDocumentsOnly = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "embeddedDocument": {
                    "path": "items",
                    "operator": {
                        "compound": {
                            "must": [
                                {
                                    "range": {
                                        "path": "items.quantity",
                                        "gt": {
                                            "$numberInt": "2"
                                        }
                                    }
                                },
                                {
                                    "exists": {
                                        "path": "items.price"
                                    }
                                },
                                {
                                    "text": {
                                        "path": "items.tags",
                                        "query": "school"
                                    }
                                }
                            ]
                        }
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "2"
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "storeLocation": {
                    "$numberInt": "1"
                },
                "items": {
                    "$filter": {
                        "input": "$items",
                        "cond": {
                            "$and": [
                                {
                                    "$ifNull": [
                                        "$$this.price",
                                        "false"
                                    ]
                                },
                                {
                                    "$gt": [
                                        "$$this.quantity",
                                        {
                                            "$numberInt": "2"
                                        }
                                    ]
                                },
                                {
                                    "$in": [
                                        "office",
                                        "$$this.tags"
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
     * Boolean
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/#boolean-examples
     */
    case EqualsBoolean = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "equals": {
                    "path": "verified_user",
                    "value": true
                }
            }
        },
        {
            "$project": {
                "name": {
                    "$numberInt": "1"
                },
                "_id": {
                    "$numberInt": "0"
                },
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * ObjectId
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/#objectid-example
     */
    case EqualsObjectId = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "equals": {
                    "path": "teammates",
                    "value": {
                        "$oid": "5a9427648b0beebeb69589a1"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Date
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/#date-example
     */
    case EqualsDate = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "equals": {
                    "path": "account_created",
                    "value": {
                        "$date": {
                            "$numberLong": "1651640468000"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Number
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/#number-example
     */
    case EqualsNumber = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "equals": {
                    "path": "employee_number",
                    "value": {
                        "$numberInt": "259"
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * String
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/#string-example
     */
    case EqualsString = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "equals": {
                    "path": "name",
                    "value": "jim hall"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * UUID
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/#uuid-example
     */
    case EqualsUUID = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "equals": {
                    "path": "uuid",
                    "value": {
                        "$binary": {
                            "base64": "+sMiYLURTGmEhaK+W33ang==",
                            "subType": "04"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Null
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/equals/#null-example
     */
    case EqualsNull = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "equals": {
                    "path": "job_title",
                    "value": null
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Basic
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/exists/#basic-example
     */
    case ExistsBasic = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "exists": {
                    "path": "type"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Embedded
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/exists/#embedded-example
     */
    case ExistsEmbedded = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "exists": {
                    "path": "quantities.lemons"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Compound
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/exists/#compound-example
     */
    case ExistsCompound = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "must": [
                        {
                            "exists": {
                                "path": "type"
                            }
                        },
                        {
                            "text": {
                                "query": "apple",
                                "path": "type"
                            }
                        }
                    ],
                    "should": {
                        "text": {
                            "query": "fuji",
                            "path": "description"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Facet
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/facet/#examples
     */
    case FacetFacet = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "facet": {
                    "operator": {
                        "near": {
                            "path": "released",
                            "origin": {
                                "$date": {
                                    "$numberLong": "930787200000"
                                }
                            },
                            "pivot": {
                                "$numberLong": "7776000000"
                            }
                        }
                    },
                    "facets": {
                        "genresFacet": {
                            "type": "string",
                            "path": "genres"
                        }
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "2"
            }
        },
        {
            "$facet": {
                "docs": [
                    {
                        "$project": {
                            "title": {
                                "$numberInt": "1"
                            },
                            "released": {
                                "$numberInt": "1"
                            }
                        }
                    }
                ],
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
        },
        {
            "$set": {
                "meta": {
                    "$arrayElemAt": [
                        "$meta",
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
     * Multi-Select Faceting Example
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/facet/#multi-select-faceting-example
     */
    case FacetMultiSelectFacetingExample = <<<'EXTENDED_JSON'
    [
        {
            "$searchMeta": {
                "facet": {
                    "operator": {
                        "compound": {
                            "must": [
                                {
                                    "text": {
                                        "path": "description",
                                        "query": "new york city"
                                    }
                                }
                            ],
                            "filter": [
                                {
                                    "equals": {
                                        "path": "cancellation_policy",
                                        "value": "moderate",
                                        "doesNotAffect": "cancellationFacet"
                                    }
                                }
                            ]
                        }
                    },
                    "facets": {
                        "accommodatesFacet": {
                            "path": "accommodates",
                            "type": "number",
                            "boundaries": [
                                {
                                    "$numberInt": "1"
                                },
                                {
                                    "$numberInt": "2"
                                },
                                {
                                    "$numberInt": "4"
                                },
                                {
                                    "$numberInt": "8"
                                }
                            ]
                        },
                        "cancellationFacet": {
                            "path": "cancellation_policy",
                            "type": "string"
                        },
                        "roomTypeFacet": {
                            "path": "room_type",
                            "type": "string"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Inter-Facet Filter Exclusion Example
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/facet/#inter-facet-filter-exclusion-example
     */
    case FacetInterFacetFilterExclusionExample = <<<'EXTENDED_JSON'
    [
        {
            "$searchMeta": {
                "facet": {
                    "operator": {
                        "compound": {
                            "must": [
                                {
                                    "text": {
                                        "path": "description",
                                        "query": "new york city"
                                    }
                                }
                            ],
                            "filter": [
                                {
                                    "equals": {
                                        "path": "cancellation_policy",
                                        "value": "moderate",
                                        "doesNotAffect": "accommodatesFacet"
                                    }
                                }
                            ]
                        }
                    },
                    "facets": {
                        "accommodatesFacet": {
                            "path": "accommodates",
                            "type": "number",
                            "boundaries": [
                                {
                                    "$numberInt": "1"
                                },
                                {
                                    "$numberInt": "2"
                                },
                                {
                                    "$numberInt": "4"
                                },
                                {
                                    "$numberInt": "8"
                                }
                            ]
                        },
                        "cancellationFacet": {
                            "path": "cancellation_policy",
                            "type": "string"
                        },
                        "roomTypeFacet": {
                            "path": "room_type",
                            "type": "string"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Disjoint
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/geoShape/#disjoint-example
     */
    case GeoShapeDisjoint = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "geoShape": {
                    "relation": "disjoint",
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [
                                    {
                                        "$numberDouble": "-161.32324199999999337"
                                    },
                                    {
                                        "$numberDouble": "22.51255700000000104"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-152.44628900000000726"
                                    },
                                    {
                                        "$numberDouble": "22.065277999999999281"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-156.09375"
                                    },
                                    {
                                        "$numberDouble": "17.811455999999999733"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-161.32324199999999337"
                                    },
                                    {
                                        "$numberDouble": "22.51255700000000104"
                                    }
                                ]
                            ]
                        ]
                    },
                    "path": "address.location"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Intersect
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/geoShape/#intersects-example
     */
    case GeoShapeIntersect = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "geoShape": {
                    "relation": "intersects",
                    "geometry": {
                        "type": "MultiPolygon",
                        "coordinates": [
                            [
                                [
                                    [
                                        {
                                            "$numberDouble": "2.1694200000000001261"
                                        },
                                        {
                                            "$numberDouble": "41.400820000000003063"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1796299999999999564"
                                        },
                                        {
                                            "$numberDouble": "41.400869999999997617"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1814599999999999547"
                                        },
                                        {
                                            "$numberDouble": "41.397159999999999513"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1553300000000001901"
                                        },
                                        {
                                            "$numberDouble": "41.406860000000001776"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1459600000000000897"
                                        },
                                        {
                                            "$numberDouble": "41.384749999999996817"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1751900000000001789"
                                        },
                                        {
                                            "$numberDouble": "41.410350000000001103"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1694200000000001261"
                                        },
                                        {
                                            "$numberDouble": "41.400820000000003063"
                                        }
                                    ]
                                ]
                            ],
                            [
                                [
                                    [
                                        {
                                            "$numberDouble": "2.1636500000000000732"
                                        },
                                        {
                                            "$numberDouble": "41.3941599999999994"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1696300000000001695"
                                        },
                                        {
                                            "$numberDouble": "41.397260000000002833"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1539500000000000313"
                                        },
                                        {
                                            "$numberDouble": "41.380049999999997112"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1793499999999998984"
                                        },
                                        {
                                            "$numberDouble": "41.430379999999999541"
                                        }
                                    ],
                                    [
                                        {
                                            "$numberDouble": "2.1636500000000000732"
                                        },
                                        {
                                            "$numberDouble": "41.3941599999999994"
                                        }
                                    ]
                                ]
                            ]
                        ]
                    },
                    "path": "address.location"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Within
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/geoShape/#within-example
     */
    case GeoShapeWithin = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "geoShape": {
                    "relation": "within",
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [
                                    {
                                        "$numberDouble": "-74.3994140625"
                                    },
                                    {
                                        "$numberDouble": "40.530501775700003009"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-74.729003906299993787"
                                    },
                                    {
                                        "$numberDouble": "40.580584664100001646"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-74.772949218799993787"
                                    },
                                    {
                                        "$numberDouble": "40.946713665099998991"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-74.069824218799993787"
                                    },
                                    {
                                        "$numberDouble": "41.129021347500000161"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-73.65234375"
                                    },
                                    {
                                        "$numberDouble": "40.996484014399996454"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-72.6416015625"
                                    },
                                    {
                                        "$numberDouble": "40.946713665099998991"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-72.355957031299993787"
                                    },
                                    {
                                        "$numberDouble": "40.797177415199996631"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-74.3994140625"
                                    },
                                    {
                                        "$numberDouble": "40.530501775700003009"
                                    }
                                ]
                            ]
                        ]
                    },
                    "path": "address.location"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * box
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/geoWithin/#box-example
     */
    case GeoWithinBox = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "geoWithin": {
                    "path": "address.location",
                    "box": {
                        "bottomLeft": {
                            "type": "Point",
                            "coordinates": [
                                {
                                    "$numberDouble": "112.46699999999999875"
                                },
                                {
                                    "$numberDouble": "-55.049999999999997158"
                                }
                            ]
                        },
                        "topRight": {
                            "type": "Point",
                            "coordinates": [
                                {
                                    "$numberInt": "168"
                                },
                                {
                                    "$numberDouble": "-9.1329999999999991189"
                                }
                            ]
                        }
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * circle
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/geoWithin/#circle-example
     */
    case GeoWithinCircle = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "geoWithin": {
                    "circle": {
                        "center": {
                            "type": "Point",
                            "coordinates": [
                                {
                                    "$numberDouble": "-73.540000000000006253"
                                },
                                {
                                    "$numberDouble": "45.539999999999999147"
                                }
                            ]
                        },
                        "radius": {
                            "$numberInt": "1600"
                        }
                    },
                    "path": "address.location"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * geometry
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/geoWithin/#geometry-examples
     */
    case GeoWithinGeometry = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "geoWithin": {
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [
                                    {
                                        "$numberDouble": "-161.32324199999999337"
                                    },
                                    {
                                        "$numberDouble": "22.51255700000000104"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-152.44628900000000726"
                                    },
                                    {
                                        "$numberDouble": "22.065277999999999281"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-156.09375"
                                    },
                                    {
                                        "$numberDouble": "17.811455999999999733"
                                    }
                                ],
                                [
                                    {
                                        "$numberDouble": "-161.32324199999999337"
                                    },
                                    {
                                        "$numberDouble": "22.51255700000000104"
                                    }
                                ]
                            ]
                        ]
                    },
                    "path": "address.location"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Example
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/operators-collectors/hasAncestor/#sample-query
     */
    case HasAncestorExample = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "returnStoredSource": true,
                "returnScope": {
                    "path": "funding_rounds.investments"
                },
                "hasAncestor": {
                    "ancestorPath": "funding_rounds",
                    "operator": {
                        "equals": {
                            "path": "funding_rounds.funded_year",
                            "value": {
                                "$numberInt": "2005"
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Simple Query
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/operators-collectors/hasRoot/#simple-query
     */
    case HasRootSimpleQuery = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "returnStoredSource": true,
                "returnScope": {
                    "path": "products"
                },
                "hasRoot": {
                    "operator": {
                        "range": {
                            "path": "founded_year",
                            "gte": {
                                "$numberInt": "2005"
                            },
                            "lte": {
                                "$numberInt": "2010"
                            }
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Multi-Level Query
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/operators-collectors/hasRoot/#multi-level-query
     */
    case HasRootMultiLevelQuery = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "returnStoredSource": true,
                "returnScope": {
                    "path": "funding_rounds"
                },
                "hasRoot": {
                    "operator": {
                        "text": {
                            "path": "name",
                            "query": "Facebook"
                        }
                    }
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Compound Query
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/operators-collectors/hasRoot/#compound-query
     */
    case HasRootCompoundQuery = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "should": [
                        {
                            "embeddedDocument": {
                                "path": "funding_rounds.investments",
                                "operator": {
                                    "wildcard": {
                                        "path": "funding_rounds.investments.financial_org.name",
                                        "query": "*Ventures*",
                                        "allowAnalyzedField": true
                                    }
                                }
                            }
                        },
                        {
                            "hasRoot": {
                                "operator": {
                                    "wildcard": {
                                        "path": "description",
                                        "query": "*network*",
                                        "allowAnalyzedField": true
                                    }
                                }
                            }
                        }
                    ]
                },
                "returnScope": {
                    "path": "funding_rounds"
                },
                "returnStoredSource": true
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
     * Single Value Field Match
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/in/#examples
     */
    case InSingleValueFieldMatch = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "in": {
                    "path": "birthdate",
                    "value": [
                        {
                            "$date": {
                                "$numberLong": "226117231000"
                            }
                        },
                        {
                            "$date": {
                                "$numberLong": "226022400000"
                            }
                        },
                        {
                            "$date": {
                                "$numberLong": "231803855000"
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
                "name": {
                    "$numberInt": "1"
                },
                "birthdate": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Array Value Field Match
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/in/#examples
     */
    case InArrayValueFieldMatch = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "in": {
                    "path": "accounts",
                    "value": [
                        {
                            "$numberInt": "371138"
                        },
                        {
                            "$numberInt": "371139"
                        },
                        {
                            "$numberInt": "371140"
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
                "accounts": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Compound Query Match
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/in/#examples
     */
    case InCompoundQueryMatch = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "must": [
                        {
                            "in": {
                                "path": "name",
                                "value": [
                                    "james sanchez",
                                    "jennifer lawrence"
                                ]
                            }
                        }
                    ],
                    "should": [
                        {
                            "in": {
                                "path": "_id",
                                "value": [
                                    {
                                        "$oid": "5ca4bbcea2dd94ee58162a72"
                                    },
                                    {
                                        "$oid": "5ca4bbcea2dd94ee58162a91"
                                    }
                                ]
                            }
                        }
                    ]
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
                    "$numberInt": "1"
                },
                "name": {
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
     * Single Document with Multiple Fields
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/morelikethis/#example-1--single-document-with-multiple-fields
     */
    case MoreLikeThisSingleDocumentWithMultipleFields = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "moreLikeThis": {
                    "like": {
                        "title": "The Godfather",
                        "genres": "action"
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
                },
                "genres": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Input Document Excluded in Results
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/morelikethis/#example-2--input-document-excluded-in-results
     */
    case MoreLikeThisInputDocumentExcludedInResults = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "must": [
                        {
                            "moreLikeThis": {
                                "like": {
                                    "_id": {
                                        "$oid": "573a1396f29313caabce4a9a"
                                    },
                                    "genres": [
                                        "Crime",
                                        "Drama"
                                    ],
                                    "title": "The Godfather"
                                }
                            }
                        }
                    ],
                    "mustNot": [
                        {
                            "equals": {
                                "path": "_id",
                                "value": {
                                    "$oid": "573a1396f29313caabce4a9a"
                                }
                            }
                        }
                    ]
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
                    "$numberInt": "1"
                },
                "title": {
                    "$numberInt": "1"
                },
                "released": {
                    "$numberInt": "1"
                },
                "genres": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Multiple Analyzers
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/morelikethis/#example-3--multiple-analyzers
     */
    case MoreLikeThisMultipleAnalyzers = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "should": [
                        {
                            "moreLikeThis": {
                                "like": {
                                    "_id": {
                                        "$oid": "573a1396f29313caabce4a9a"
                                    },
                                    "genres": [
                                        "Crime",
                                        "Drama"
                                    ],
                                    "title": "The Godfather"
                                }
                            }
                        }
                    ],
                    "mustNot": [
                        {
                            "equals": {
                                "path": "_id",
                                "value": {
                                    "$oid": "573a1394f29313caabcde9ef"
                                }
                            }
                        }
                    ]
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
                "title": {
                    "$numberInt": "1"
                },
                "genres": {
                    "$numberInt": "1"
                },
                "_id": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Number
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/near/#number-example
     */
    case NearNumber = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "index": "runtimes",
                "near": {
                    "path": "runtime",
                    "origin": {
                        "$numberInt": "279"
                    },
                    "pivot": {
                        "$numberInt": "2"
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "7"
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
                "runtime": {
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
     * Date
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/near/#date-example
     */
    case NearDate = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "index": "releaseddate",
                "near": {
                    "path": "released",
                    "origin": {
                        "$date": {
                            "$numberLong": "-1713657600000"
                        }
                    },
                    "pivot": {
                        "$numberLong": "7776000000"
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
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
                },
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * GeoJSON Point
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/near/#geojson-point-examples
     */
    case NearGeoJSONPoint = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "near": {
                    "origin": {
                        "type": "Point",
                        "coordinates": [
                            {
                                "$numberDouble": "-8.6130800000000000693"
                            },
                            {
                                "$numberDouble": "41.141300000000001091"
                            }
                        ]
                    },
                    "pivot": {
                        "$numberInt": "1000"
                    },
                    "path": "address.location"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Compound
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/near/#compound-example
     */
    case NearCompound = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "compound": {
                    "must": {
                        "text": {
                            "query": "Apartment",
                            "path": "property_type"
                        }
                    },
                    "should": {
                        "near": {
                            "origin": {
                                "type": "Point",
                                "coordinates": [
                                    {
                                        "$numberDouble": "114.15027000000000612"
                                    },
                                    {
                                        "$numberDouble": "22.281580000000001718"
                                    }
                                ]
                            },
                            "pivot": {
                                "$numberInt": "1000"
                            },
                            "path": "address.location"
                        }
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "3"
            }
        },
        {
            "$project": {
                "_id": {
                    "$numberInt": "0"
                },
                "property_type": {
                    "$numberInt": "1"
                },
                "address": {
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
     * Single Phrase
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/phrase/#single-phrase-example
     */
    case PhraseSinglePhrase = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "phrase": {
                    "path": "title",
                    "query": "new york"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Multiple Phrase
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/phrase/#multiple-phrases-example
     */
    case PhraseMultiplePhrase = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "phrase": {
                    "path": "title",
                    "query": [
                        "the man",
                        "the moon"
                    ]
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Phrase Slop
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/phrase/#slop-example
     */
    case PhrasePhraseSlop = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "phrase": {
                    "path": "title",
                    "query": "men women",
                    "slop": {
                        "$numberInt": "5"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Phrase Synonyms
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/phrase/#synonyms-example
     */
    case PhrasePhraseSynonyms = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "phrase": {
                    "path": "plot",
                    "query": "automobile race",
                    "slop": {
                        "$numberInt": "5"
                    },
                    "synonyms": "my_synonyms"
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
                "plot": {
                    "$numberInt": "1"
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
     * Boolean Operator Queries
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/queryString/#boolean-operator-queries
     */
    case QueryStringBooleanOperatorQueries = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "queryString": {
                    "defaultPath": "title",
                    "query": "Rocky AND (IV OR 4 OR Four)"
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Number gte lte
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/range/#number-example
     */
    case RangeNumberGteLte = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "range": {
                    "path": "runtime",
                    "gte": {
                        "$numberInt": "2"
                    },
                    "lte": {
                        "$numberInt": "3"
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
                "runtime": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Number lte
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/range/#number-example
     */
    case RangeNumberLte = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "range": {
                    "path": "runtime",
                    "lte": {
                        "$numberInt": "2"
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
                "runtime": {
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
     * Date
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/range/#date-example
     */
    case RangeDate = <<<'EXTENDED_JSON'
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
     * ObjectId
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/range/#objectid-example
     */
    case RangeObjectId = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "range": {
                    "path": "_id",
                    "gte": {
                        "$oid": "573a1396f29313caabce4a9a"
                    },
                    "lte": {
                        "$oid": "573a1396f29313caabce4ae7"
                    }
                }
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
                "released": {
                    "$numberInt": "1"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * String
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/range/#string-example
     */
    case RangeString = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "range": {
                    "path": "title",
                    "gt": "city",
                    "lt": "country"
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
     * Regex
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/regex/#examples
     */
    case RegexRegex = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "regex": {
                    "path": "title",
                    "query": "[0-9]{2} (.){4}s"
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Basic
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/#basic-example
     */
    case TextBasic = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "title",
                    "query": "surfer"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fuzzy Default
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/#fuzzy-examples
     */
    case TextFuzzyDefault = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "title",
                    "query": "naw yark",
                    "fuzzy": {}
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fuzzy maxExpansions
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/#fuzzy-examples
     */
    case TextFuzzyMaxExpansions = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "title",
                    "query": "naw yark",
                    "fuzzy": {
                        "maxEdits": {
                            "$numberInt": "1"
                        },
                        "maxExpansions": {
                            "$numberInt": "100"
                        }
                    }
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Fuzzy prefixLength
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/#fuzzy-examples
     */
    case TextFuzzyPrefixLength = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "title",
                    "query": "naw yark",
                    "fuzzy": {
                        "maxEdits": {
                            "$numberInt": "1"
                        },
                        "prefixLength": {
                            "$numberInt": "2"
                        }
                    }
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "8"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Match any Using equivalent Mapping
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/#match-any-using-equivalent-mapping
     */
    case TextMatchAnyUsingEquivalentMapping = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "plot",
                    "query": "attire",
                    "synonyms": "my_synonyms",
                    "matchCriteria": "any"
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
                "plot": {
                    "$numberInt": "1"
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
     * Match any Using explicit Mapping
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/#match-any-using-explicit-mapping
     */
    case TextMatchAnyUsingExplicitMapping = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "plot",
                    "query": "boat race",
                    "synonyms": "my_synonyms",
                    "matchCriteria": "any"
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
                "plot": {
                    "$numberInt": "1"
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
     * Match all Using Synonyms
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/#match-all-using-synonyms
     */
    case TextMatchAllUsingSynonyms = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": "plot",
                    "query": "automobile race",
                    "matchCriteria": "all",
                    "synonyms": "my_synonyms"
                }
            }
        },
        {
            "$limit": {
                "$numberInt": "20"
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
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Wildcard Path
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/text/
     */
    case TextWildcardPath = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "text": {
                    "path": {
                        "wildcard": "*"
                    },
                    "query": "surfer"
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
                "score": {
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * ANN Basic
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/vector-search/#basic-example
     */
    case VectorSearchANNBasic = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "vectorSearch": {
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
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * ANN Filter
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/vector-search/#filter-example
     */
    case VectorSearchANNFilter = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "vectorSearch": {
                    "path": "plot_embedding",
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
                    "filter": {
                        "range": {
                            "path": "year",
                            "lt": {
                                "$numberInt": "1975"
                            }
                        }
                    },
                    "numCandidates": {
                        "$numberInt": "150"
                    },
                    "limit": {
                        "$numberInt": "10"
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
                "plot": {
                    "$numberInt": "1"
                },
                "year": {
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
     * ENN
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/vector-search/#enn-example
     */
    case VectorSearchENN = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "vectorSearch": {
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
                    "$meta": "searchScore"
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Wildcard Path
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/wildcard/#index-definition
     */
    case WildcardWildcardPath = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "wildcard": {
                    "query": "Wom?n *",
                    "path": {
                        "wildcard": "*"
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
                }
            }
        }
    ]
    EXTENDED_JSON;

    /**
     * Escape Character Example
     *
     * @see https://www.mongodb.com/docs/atlas/atlas-search/wildcard/#escape-character-example
     */
    case WildcardEscapeCharacterExample = <<<'EXTENDED_JSON'
    [
        {
            "$search": {
                "wildcard": {
                    "query": "*\\?",
                    "path": "title"
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
}
