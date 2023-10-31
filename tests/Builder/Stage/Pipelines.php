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
                    "$sum": "$homework"
                },
                "totalQuiz": {
                    "$sum": "$quiz"
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
}
