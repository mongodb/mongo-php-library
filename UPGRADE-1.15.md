# UPGRADE FROM 1.x to 1.15

## Method signature changes

### Parameter types

Starting with 1.15, methods now declare types for their arguments. This will not
cause BC breaks unless you've passed a type that was incompatible with the type
previously documented in the PHPDoc comment. A list of changes can be found at
the bottom of this document.

### Return types

Return types will be added in version 2.0. These types are documented in a
PHPDoc comment and will eventually become a declared return type. You can
prepare for this  change (which will trigger a BC break in any class you may
extend) by adding the correct return type to your class at this time.

## Internal classes

Internal classes (i.e. annotated with `@internal`) will become final where
possible in a future release. At the same time, we will add return types to
these internal classes. Note that internal classes are not covered by our
backward compatibility promise, and you should not instantiate such classes
directly.

## Method signature changes by class

### MongoDB\Client

|                                                                                           1.13 | 1.15                                                                                  |
|-----------------------------------------------------------------------------------------------:|:--------------------------------------------------------------------------------------|
| `__construct($uri = 'mongodb://127.0.0.1', array $uriOptions = [], array $driverOptions = [])` | `__construct(?string $uri = null, array $uriOptions = [], array $driverOptions = [])` |
|                                                                         `__get($databaseName)` | `__get(string $databaseName)`                                                         |
|                                             `dropDatabase($databaseName, array $options = [])` | `dropDatabase(string $databaseName, array $options = [])`                             |
|                        `selectCollection($databaseName, $collectionName, array $options = [])` | `selectCollection(string $databaseName, string $collectionName, array $options = [])` |
|                                           `selectDatabase($databaseName, array $options = [])` | `selectDatabase(string $databaseName, array $options = [])`                           |

### MongoDB\Database

|                                                                               1.13 | 1.15                                                                                      |
|-----------------------------------------------------------------------------------:|:------------------------------------------------------------------------------------------|
| `__construct(MongoDB\Driver\Manager $manager, $databaseName, array $options = [])` | `__construct(MongoDB\Driver\Manager $manager, string $databaseName, array $options = [])` |
|                                                           `__get($collectionName)` | `__get(string $collectionName)`                                                           |
|                           `createCollection($collectionName, array $options = [])` | `createCollection(string $collectionName, array $options = [])`                           |
|                             `dropCollection($collectionName, array $options = [])` | `dropCollection(string $collectionName, array $options = [])`                             |
| `modifyCollection($collectionName, array $collectionOptions, array $options = [])` | `modifyCollection(string $collectionName, array $collectionOptions, array $options = [])` |
|                           `selectCollection($collectionName, array $options = [])` | `selectCollection(string $collectionName, array $options = [])`                           |

### MongoDB\Collection

|                                                                                                1.13 | 1.15                                                                                                              |
|----------------------------------------------------------------------------------------------------:|:------------------------------------------------------------------------------------------------------------------|
| `__construct(MongoDB\Driver\Manager $manager, $databaseName, $collectionName, array $options = [])` | `__construct(MongoDB\Driver\Manager $manager, string $databaseName, string $collectionName, array $options = [])` |
|                                           `distinct($fieldName, $filter = [], array $options = [])` | `distinct(string $fieldName, $filter = [], array $options = [])`                                                  |

### MongoDB\GridFS\Bucket

|                                                                               1.13 | 1.15                                                                                      |
|-----------------------------------------------------------------------------------:|:------------------------------------------------------------------------------------------|
| `__construct(MongoDB\Driver\Manager $manager, $databaseName, array $options = [])` | `__construct(MongoDB\Driver\Manager $manager, string $databaseName, array $options = [])` |
|             `downloadToStreamByName($filename, $destination, array $options = [])` | `downloadToStreamByName(string $filename, $destination, array $options = [])`             |
|                         `openDownloadStreamByName($filename, array $options = [])` | `openDownloadStreamByName(string $filename, array $options = [])`                         |
|                                 `openUploadStream($filename, array $options = [])` | `openUploadStream(string $filename, array $options = [])`                                 |
|                        `uploadFromStream($filename, $source, array $options = [])` | `uploadFromStream(string $filename, $source, array $options = [])`                        |
|                                                        `rename($id, $newFilename)` | `rename($id, string $newFilename)`                                                        |
