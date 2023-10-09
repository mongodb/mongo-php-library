<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

namespace MongoDB\Builder\Stage;

use MongoDB\BSON\Document;
use MongoDB\BSON\Serializable;
use MongoDB\BSON\Timestamp;
use MongoDB\Builder\Encode;
use MongoDB\Builder\Optional;
use MongoDB\Builder\Type\StageInterface;
use stdClass;

/**
 * Returns a Change Stream cursor for the collection or database. This stage can only occur once in an aggregation pipeline and it must occur as the first stage.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/aggregation/changeStream/
 */
class ChangeStreamStage implements StageInterface
{
    public const NAME = '$changeStream';
    public const ENCODE = \MongoDB\Builder\Encode::Object;

    /** @param Optional|bool $allChangesForCluster A flag indicating whether the stream should report all changes that occur on the deployment, aside from those on internal databases or collections. */
    public Optional|bool $allChangesForCluster;

    /** @param Optional|non-empty-string $fullDocument Specifies whether change notifications include a copy of the full document when modified by update operations. */
    public Optional|string $fullDocument;

    /** @param Optional|non-empty-string $fullDocumentBeforeChange Valid values are "off", "whenAvailable", or "required". If set to "off", the "fullDocumentBeforeChange" field of the output document is always omitted. If set to "whenAvailable", the "fullDocumentBeforeChange" field will be populated with the pre-image of the document modified by the current change event if such a pre-image is available, and will be omitted otherwise. If set to "required", then the "fullDocumentBeforeChange" field is always populated and an exception is thrown if the pre-image is not              available. */
    public Optional|string $fullDocumentBeforeChange;

    /** @param Optional|int $resumeAfter Specifies a resume token as the logical starting point for the change stream. Cannot be used with startAfter or startAtOperationTime fields. */
    public Optional|int $resumeAfter;

    /**
     * @param Optional|bool $showExpandedEvents Specifies whether to include additional change events, such as such as DDL and index operations.
     * New in version 6.0.
     */
    public Optional|bool $showExpandedEvents;

    /** @param Document|Optional|Serializable|array|stdClass $startAfter Specifies a resume token as the logical starting point for the change stream. Cannot be used with resumeAfter or startAtOperationTime fields. */
    public Document|Serializable|Optional|stdClass|array $startAfter;

    /** @param Optional|Timestamp|int $startAtOperationTime Specifies a time as the logical starting point for the change stream. Cannot be used with resumeAfter or startAfter fields. */
    public Timestamp|Optional|int $startAtOperationTime;

    /**
     * @param Optional|bool $allChangesForCluster A flag indicating whether the stream should report all changes that occur on the deployment, aside from those on internal databases or collections.
     * @param Optional|non-empty-string $fullDocument Specifies whether change notifications include a copy of the full document when modified by update operations.
     * @param Optional|non-empty-string $fullDocumentBeforeChange Valid values are "off", "whenAvailable", or "required". If set to "off", the "fullDocumentBeforeChange" field of the output document is always omitted. If set to "whenAvailable", the "fullDocumentBeforeChange" field will be populated with the pre-image of the document modified by the current change event if such a pre-image is available, and will be omitted otherwise. If set to "required", then the "fullDocumentBeforeChange" field is always populated and an exception is thrown if the pre-image is not              available.
     * @param Optional|int $resumeAfter Specifies a resume token as the logical starting point for the change stream. Cannot be used with startAfter or startAtOperationTime fields.
     * @param Optional|bool $showExpandedEvents Specifies whether to include additional change events, such as such as DDL and index operations.
     * New in version 6.0.
     * @param Document|Optional|Serializable|array|stdClass $startAfter Specifies a resume token as the logical starting point for the change stream. Cannot be used with resumeAfter or startAtOperationTime fields.
     * @param Optional|Timestamp|int $startAtOperationTime Specifies a time as the logical starting point for the change stream. Cannot be used with resumeAfter or startAfter fields.
     */
    public function __construct(
        Optional|bool $allChangesForCluster = Optional::Undefined,
        Optional|string $fullDocument = Optional::Undefined,
        Optional|string $fullDocumentBeforeChange = Optional::Undefined,
        Optional|int $resumeAfter = Optional::Undefined,
        Optional|bool $showExpandedEvents = Optional::Undefined,
        Document|Serializable|Optional|stdClass|array $startAfter = Optional::Undefined,
        Timestamp|Optional|int $startAtOperationTime = Optional::Undefined,
    ) {
        $this->allChangesForCluster = $allChangesForCluster;
        $this->fullDocument = $fullDocument;
        $this->fullDocumentBeforeChange = $fullDocumentBeforeChange;
        $this->resumeAfter = $resumeAfter;
        $this->showExpandedEvents = $showExpandedEvents;
        $this->startAfter = $startAfter;
        $this->startAtOperationTime = $startAtOperationTime;
    }
}
