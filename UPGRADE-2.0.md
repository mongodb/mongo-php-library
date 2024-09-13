UPGRADE FROM 1.x to 2.0
========================

GridFS
------

 * The `md5` is no longer calculated when a file is uploaded to GridFS.
   Applications that require a file digest should implement it outside GridFS
   and store in metadata.

   ```php
   $hash = hash_file('sha256', $filename);
   $bucket->openDownloadStream($fileId, ['metadata' => ['hash' => $hash]]);
   ```

 * The fields `contentType` and `aliases` are no longer stored in the `files`
   collection. Applications that require this information should store it in
   metadata.

   **Before:**
   ```php
   $bucket->openDownloadStream($fileId, ['contentType' => 'image/png']);
   ```

   **After:**
   ```php
   $bucket->openDownloadStream($fileId, ['metadata' => ['contentType' => 'image/png']]);
   ```
