.. list-table::
   :header-rows: 1
   :widths: 20 20 80

   * - Name
     - Type
     - Description

   * - batchSize
     - integer
     - Specifies the batch size for the cursor, which will apply to both the
       initial ``aggregate`` command and any subsequent ``getMore`` commands.
       This determines the maximum number of change events to return in each
       response from the server.

       .. note::

          Irrespective of the ``batchSize`` option, the initial ``aggregate``
          command response for a change stream generally does not include any
          documents unless another option is used to configure its starting
          point (e.g. ``startAfter``).

   * - collation
     - array|object
     - .. include:: /includes/extracts/common-option-collation.rst

   * - comment
     - mixed
     - .. include:: /includes/extracts/common-option-comment.rst

       The comment can be any valid BSON type for server versions 4.4 and above.
       Earlier server versions only support string values.

       .. versionadded:: 1.13

   * - fullDocument
     - string
     - Determines how the ``fullDocument`` response field will be populated for
       update operations.

       By default, change streams only return the delta of fields (via an
       ``updateDescription`` field) for update operations and ``fullDocument``
       is omitted. Insert and replace operations always include the
       ``fullDocument`` field. Delete operations omit the field as the document
       no longer exists.

       Specify "updateLookup" to return the current majority-committed version
       of the updated document.

       MongoDB 6.0+ allows returning the post-image of the modified document if
       the collection has ``changeStreamPreAndPostImages`` enabled. Specify
       "whenAvailable" to return the post-image if available or a null value if
       not. Specify "required" to return the post-image if available or raise an
       error if not.

       The following values are supported:

       - ``MongoDB\Operation\Watch::FULL_DOCUMENT_UPDATE_LOOKUP``
       - ``MongoDB\Operation\Watch::FULL_DOCUMENT_WHEN_AVAILABLE``
       - ``MongoDB\Operation\Watch::FULL_DOCUMENT_REQUIRED``

       .. note::

          This is an option of the ``$changeStream`` pipeline stage.

   * - fullDocumentBeforeChange
     - string
     - Determines how the ``fullDocumentBeforeChange`` response field will be
       populated. By default, the field is omitted.

       MongoDB 6.0+ allows returning the pre-image of the modified document if
       the collection has ``changeStreamPreAndPostImages`` enabled. Specify
       "whenAvailable" to return the pre-image if available or a null value if
       not. Specify "required" to return the pre-image if available or raise an
       error if not.

       The following values are supported:

       - ``MongoDB\Operation\Watch::FULL_DOCUMENT_BEFORE_CHANGE_WHEN_AVAILABLE``
       - ``MongoDB\Operation\Watch::FULL_DOCUMENT_BEFORE_CHANGE_REQUIRED``

       .. note::

          This is an option of the ``$changeStream`` pipeline stage.

       .. versionadded: 1.13

   * - maxAwaitTimeMS
     - integer
     - Positive integer denoting the time limit in milliseconds for the server
       to block a getMore operation if no data is available.

   * - readConcern
     - :php:`MongoDB\\Driver\\ReadConcern <class.mongodb-driver-readconcern>`
     - .. include:: /includes/extracts/common-option-readConcern.rst

   * - readPreference
     - :php:`MongoDB\\Driver\\ReadPreference <class.mongodb-driver-readpreference>`
     - .. include:: /includes/extracts/common-option-readPreference.rst

       This is used for both the initial change stream aggregation and for
       server selection during an automatic resume.

   * - resumeAfter
     - array|object
     - Specifies the logical starting point for the new change stream. The
       ``_id`` field in documents returned by the change stream may be used
       here.

       Using this option in conjunction with ``startAfter`` and/or
       ``startAtOperationTime`` will result in a server error. The options are
       mutually exclusive.

       .. note::

          This is an option of the ``$changeStream`` pipeline stage.

   * - session
     - :php:`MongoDB\\Driver\\Session <class.mongodb-driver-session>`
     - .. include:: /includes/extracts/common-option-session.rst

   * - showExpandedEvents
     - boolean
     - If true, instructs the server to include additional DDL events in the
       change stream. The additional events that may be included are:

       - ``createIndexes``
       - ``dropIndexes``
       - ``modify``
       - ``create``
       - ``shardCollection``
       - ``reshardCollection`` (server 6.1+)
       - ``refineCollectionShardKey`` (server 6.1+)

       This is not supported for server versions prior to 6.0 and will result in
       an exception at execution time if used.

       .. note::

          This is an option of the ``$changeStream`` pipeline stage.

       .. versionadded:: 1.13

   * - startAfter
     - array|object
     - Specifies the logical starting point for the new change stream. The
       ``_id`` field in documents returned by the change stream may be used
       here. Unlike ``resumeAfter``, this option can be used with a resume token
       from an "invalidate" event.

       Using this option in conjunction with ``resumeAfter`` and/or
       ``startAtOperationTime`` will result in a server error. The options are
       mutually exclusive.

       This is not supported for server versions prior to 4.2 and will result in
       an exception at execution time if used.

       .. note::

          This is an option of the ``$changeStream`` pipeline stage.

       .. versionadded: 1.5

   * - startAtOperationTime
     - :php:`MongoDB\\BSON\\TimestampInterface <class.mongodb-bson-timestampinterface>`
     - If specified, the change stream will only provide changes that occurred
       at or after the specified timestamp. Command responses from a MongoDB
       4.0+ server include an ``operationTime`` that can be used here. By
       default, the ``operationTime`` returned by the initial ``aggregate``
       command will be used if available.

       Using this option in conjunction with ``resumeAfter`` and/or
       ``startAfter`` will result in a server error. The options are mutually
       exclusive.

       This is not supported for server versions prior to 4.0 and will result in
       an exception at execution time if used.

       .. note::

          This is an option of the ``$changeStream`` pipeline stage.

   * - typeMap
     - array
     - .. include:: /includes/extracts/common-option-typeMap.rst
