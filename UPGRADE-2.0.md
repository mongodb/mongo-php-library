UPGRADE FROM 1.x to 2.0
========================

 * Classes in the namespace `MongoDB\Operation\` are `final`.
 * All methods in interfaces and classes now define a return type.
 * The `MongoDB\ChangeStream::CURSOR_NOT_FOUND` constant is now private.
 * The `MongoDB\Operation\Watch::FULL_DOCUMENT_DEFAULT` constant has been
   removed.

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
