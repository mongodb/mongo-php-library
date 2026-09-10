<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

enum Pipelines: string
{
    /**
     * Add to Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/addToSet/#add-to-array
     */
    case AddToSetAddToArray = <<<'EXTENDED_JSON'
    {
        "update": {
            "$addToSet": {
                "tags": "accessories"
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Value Already Exists
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/addToSet/#value-already-exists
     */
    case AddToSetValueAlreadyExists = <<<'EXTENDED_JSON'
    {
        "update": {
            "$addToSet": {
                "tags": "camera"
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Use $each Modifier
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/addToSet/#each-modifier
     */
    case AddToSetUseEachModifier = <<<'EXTENDED_JSON'
    {
        "update": {
            "$addToSet": {
                "tags": {
                    "$each": [
                        "camera",
                        "electronics",
                        "accessories"
                    ]
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Bitwise AND
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/bit/#bitwise-and
     */
    case BitBitwiseAND = <<<'EXTENDED_JSON'
    {
        "update": {
            "$bit": {
                "expdata": {
                    "and": {
                        "$numberInt": "10"
                    }
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Bitwise OR
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/bit/#bitwise-or
     */
    case BitBitwiseOR = <<<'EXTENDED_JSON'
    {
        "update": {
            "$bit": {
                "expdata": {
                    "or": {
                        "$numberInt": "5"
                    }
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Bitwise XOR
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/bit/#bitwise-xor
     */
    case BitBitwiseXOR = <<<'EXTENDED_JSON'
    {
        "update": {
            "$bit": {
                "expdata": {
                    "xor": {
                        "$numberInt": "5"
                    }
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Set Current Date and Timestamp
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/currentDate/#example
     */
    case CurrentDateSetCurrentDateAndTimestamp = <<<'EXTENDED_JSON'
    {
        "update": {
            "$currentDate": {
                "lastModified": true,
                "cancellation.date": {
                    "$type": "timestamp"
                }
            },
            "$set": {
                "cancellation.reason": "user request",
                "status": "D"
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Increment Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/inc/#example
     */
    case IncIncrementFields = <<<'EXTENDED_JSON'
    {
        "update": {
            "$inc": {
                "quantity": {
                    "$numberInt": "-2"
                },
                "metrics.orders": {
                    "$numberInt": "1"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Use $max to Compare Numbers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/max/#use--max-to-compare-numbers
     */
    case MaxUseMaxToCompareNumbers = <<<'EXTENDED_JSON'
    {
        "update": {
            "$max": {
                "highScore": {
                    "$numberInt": "950"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Use $max to Compare Dates
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/max/#use--max-to-compare-dates
     */
    case MaxUseMaxToCompareDates = <<<'EXTENDED_JSON'
    {
        "update": {
            "$max": {
                "dateExpired": {
                    "$date": {
                        "$numberLong": "1380499200000"
                    }
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Use $min to Compare Numbers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/min/#use--min-to-compare-numbers
     */
    case MinUseMinToCompareNumbers = <<<'EXTENDED_JSON'
    {
        "update": {
            "$min": {
                "lowScore": {
                    "$numberInt": "150"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Use $min to Compare Dates
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/min/#use--min-to-compare-dates
     */
    case MinUseMinToCompareDates = <<<'EXTENDED_JSON'
    {
        "update": {
            "$min": {
                "dateEntered": {
                    "$date": {
                        "$numberLong": "1380067200000"
                    }
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Multiply the Value of a Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/mul/#multiply-the-value-of-a-field
     */
    case MulMultiplyTheValueOfAField = <<<'EXTENDED_JSON'
    {
        "update": {
            "$mul": {
                "price": {
                    "$numberDecimal": "1.25"
                },
                "quantity": {
                    "$numberInt": "2"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Remove the First Item of an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pop/#remove-the-first-item-of-an-array
     */
    case PopRemoveTheFirstItemOfAnArray = <<<'EXTENDED_JSON'
    {
        "update": {
            "$pop": {
                "scores": {
                    "$numberInt": "-1"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Remove the Last Item of an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pop/#remove-the-last-item-of-an-array
     */
    case PopRemoveTheLastItemOfAnArray = <<<'EXTENDED_JSON'
    {
        "update": {
            "$pop": {
                "scores": {
                    "$numberInt": "1"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Remove All Items That Equal a Specified Value
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pull/#remove-all-items-that-equal-a-specified-value
     */
    case PullRemoveAllItemsThatEqualASpecifiedValue = <<<'EXTENDED_JSON'
    {
        "update": {
            "$pull": {
                "fruits": {
                    "$in": [
                        "apples",
                        "oranges"
                    ]
                },
                "vegetables": "carrots"
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Remove All Items That Match a Specified $pull Condition
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pull/#remove-all-items-that-match-a-specified--pull-condition
     */
    case PullRemoveAllItemsThatMatchASpecifiedPullCondition = <<<'EXTENDED_JSON'
    {
        "update": {
            "$pull": {
                "votes": {
                    "$gte": {
                        "$numberInt": "6"
                    }
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Remove Items from an Array of Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pull/#remove-items-from-an-array-of-documents
     */
    case PullRemoveItemsFromAnArrayOfDocuments = <<<'EXTENDED_JSON'
    {
        "update": {
            "$pull": {
                "results": {
                    "score": {
                        "$numberInt": "8"
                    },
                    "item": "B"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Remove Documents from Nested Arrays
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pull/#remove-documents-from-nested-arrays
     */
    case PullRemoveDocumentsFromNestedArrays = <<<'EXTENDED_JSON'
    {
        "update": {
            "$pull": {
                "results": {
                    "answers": {
                        "$elemMatch": {
                            "q": {
                                "$numberInt": "2"
                            },
                            "a": {
                                "$gte": {
                                    "$numberInt": "8"
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Remove Multiple Values from an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/pullAll/#examples
     */
    case PullAllRemoveMultipleValuesFromAnArray = <<<'EXTENDED_JSON'
    {
        "update": {
            "$pullAll": {
                "scores": [
                    {
                        "$numberInt": "0"
                    },
                    {
                        "$numberInt": "5"
                    }
                ]
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Append a Value to an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/push/#append-a-value-to-an-array
     */
    case PushAppendAValueToAnArray = <<<'EXTENDED_JSON'
    {
        "update": {
            "$push": {
                "scores": {
                    "$numberInt": "89"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Append a Value to Arrays in Multiple Documents
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/push/#append-a-value-to-arrays-in-multiple-documents
     */
    case PushAppendAValueToArraysInMultipleDocuments = <<<'EXTENDED_JSON'
    {
        "update": {
            "$push": {
                "scores": {
                    "$numberInt": "95"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Append Multiple Values to an Array
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/push/#append-multiple-values-to-an-array
     */
    case PushAppendMultipleValuesToAnArray = <<<'EXTENDED_JSON'
    {
        "update": {
            "$push": {
                "scores": {
                    "$each": [
                        {
                            "$numberInt": "90"
                        },
                        {
                            "$numberInt": "92"
                        },
                        {
                            "$numberInt": "85"
                        }
                    ]
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Use $push with Multiple Modifiers
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/push/#use--push-operator-with-multiple-modifiers
     */
    case PushUsePushWithMultipleModifiers = <<<'EXTENDED_JSON'
    {
        "update": {
            "$push": {
                "quizzes": {
                    "$each": [
                        {
                            "wk": {
                                "$numberInt": "5"
                            },
                            "score": {
                                "$numberInt": "8"
                            }
                        },
                        {
                            "wk": {
                                "$numberInt": "6"
                            },
                            "score": {
                                "$numberInt": "7"
                            }
                        },
                        {
                            "wk": {
                                "$numberInt": "7"
                            },
                            "score": {
                                "$numberInt": "6"
                            }
                        }
                    ],
                    "$sort": {
                        "score": {
                            "$numberInt": "-1"
                        }
                    },
                    "$slice": {
                        "$numberInt": "3"
                    }
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Rename a Field
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/rename/#rename-a-field
     */
    case RenameRenameAField = <<<'EXTENDED_JSON'
    {
        "update": {
            "$rename": {
                "nmae": "name"
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Rename Multiple Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/rename/#definition
     */
    case RenameRenameMultipleFields = <<<'EXTENDED_JSON'
    {
        "update": {
            "$rename": {
                "nickname": "alias",
                "cell": "mobile"
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Rename a Field in an Embedded Document
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/rename/#rename-a-field-in-an-embedded-document
     */
    case RenameRenameAFieldInAnEmbeddedDocument = <<<'EXTENDED_JSON'
    {
        "update": {
            "$rename": {
                "name.first": "name.fname"
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Rename a Field That Does Not Exist
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/rename/#rename-a-field-that-does-not-exist
     */
    case RenameRenameAFieldThatDoesNotExist = <<<'EXTENDED_JSON'
    {
        "update": {
            "$rename": {
                "wife": "spouse"
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Set Top-Level Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/set/#set-top-level-fields
     */
    case SetSetTopLevelFields = <<<'EXTENDED_JSON'
    {
        "update": {
            "$set": {
                "quantity": {
                    "$numberInt": "500"
                },
                "details": {
                    "model": "2600",
                    "make": "Fashionaires"
                },
                "tags": [
                    "coats",
                    "outerwear",
                    "clothing"
                ]
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Upsert with $setOnInsert
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/setOnInsert/#example
     */
    case SetOnInsertUpsertWithSetOnInsert = <<<'EXTENDED_JSON'
    {
        "update": {
            "$set": {
                "item": "apple"
            },
            "$setOnInsert": {
                "defaultQty": {
                    "$numberInt": "100"
                }
            }
        }
    }
    EXTENDED_JSON;

    /**
     * Remove Fields
     *
     * @see https://www.mongodb.com/docs/manual/reference/operator/update/unset/#example
     */
    case UnsetRemoveFields = <<<'EXTENDED_JSON'
    {
        "update": {
            "$unset": {
                "quantity": "",
                "instock": ""
            }
        }
    }
    EXTENDED_JSON;
}
