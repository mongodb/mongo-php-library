UPGRADE FROM 1.x to 2.0
========================

 * Classes in the namespace `MongoDB\Operation\` are `final`.
 * All methods in interfaces and classes now define a return type.
 * The `MongoDB\ChangeStream::CURSOR_NOT_FOUND` constant is now private.
 * The `MongoDB\Operation\Watch::FULL_DOCUMENT_DEFAULT` constant has been
   removed.
 * The `getNamespace` and `isGeoHaystack` methods have been removed from the
   `MongoDB\Model\IndexInfo` class.
 * The `maxScan`, `modifiers`, `oplogReplay`, and `snapshot` options for `find`
   and `findOne` operations have been removed.
 * The `MongoDB\Collection::mapReduce` method has been removed. Use
   [aggregation pipeline](https://www.mongodb.com/docs/manual/reference/map-reduce-to-aggregation-pipeline/)
   instead.
 * The following classes and interfaces have been removed without replacement:
   * `MongoDB\MapReduceResult`
   * `MongoDB\Model\CollectionInfoCommandIterator`
   * `MongoDB\Model\CollectionInfoIterator`
   * `MongoDB\Model\DatabaseInfoIterator`
   * `MongoDB\Model\DatabaseInfoLegacyIterator`
   * `MongoDB\Model\IndexInfoIterator`
   * `MongoDB\Model\IndexInfoIteratorIterator`
   * `MongoDB\Operation\Executable`
 * The `flags` and `autoIndexId` options for
   `MongoDB\Database::createCollection()` have been removed. Additionally, the
   `USE_POWER_OF_2_SIZES` and `NO_PADDING` constants in
   `MongoDB\Operation\CreateCollection` have been removed.

Operations with no result
-------------------------

The following operations no longer return the raw command result. The return
type changed to `void`. In case of an error, an exception is thrown.

 * `MongoDB\Client`: `dropDatabase`
 * `MongoDB\Collection`: `drop`, `dropIndex`, `dropIndexes`, `dropSearchIndex`, `rename`
 * `MongoDB\Database`: `createCollection`, `drop`, `dropCollection`, `renameCollection`
 * `MongoDB\Database::createEncryptedCollection()` returns the list of encrypted fields

If you still need to access the raw command result, you can use a
[`CommandSubscriber`](https://www.php.net/manual/en/class.mongodb-driver-monitoring-commandsubscriber.php).

GridFS
------

 * The `md5` is no longer calculated when a file is uploaded to GridFS.
   Applications that require a file digest should implement it outside GridFS
   and store in metadata.

   ```php
   $hash = hash_file('sha256', $filename);
   $bucket->openUploadStream($fileId, ['metadata' => ['hash' => $hash]]);
   ```

 * The fields `contentType` and `aliases` are no longer stored in the `files`
   collection. Applications that require this information should store it in
   metadata.

   **Before:**
   ```php
   $bucket->openUploadStream($fileId, ['contentType' => 'image/png']);
   ```

   **After:**
   ```php
   $bucket->openUploadStream($fileId, ['metadata' => ['contentType' => 'image/png']]);
   ```

UnsupportedException method removals
------------------------------------

The following methods have been removed from the
`MongoDB\Exception\UnsupportedException` class:
 * `allowDiskUseNotSupported`
 * `arrayFiltersNotSupported`
 * `collationNotSupported`
 * `explainNotSupported`
 * `readConcernNotSupported`
 * `writeConcernNotSupported`

The remaining methods have been marked as internal and may be removed in a
future minor version. Only the class itself is covered by the BC promise.
