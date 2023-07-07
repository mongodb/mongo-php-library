# Converting BSON data through codecs

Codecs provide a more flexible way to convert BSON data to native types and back and address most of the shortcomings of
the previous type map system. The codec system is designed to be used by libraries that need to convert BSON data into
native types, for example object mappers. Unlike the type map system, codecs allow converting any BSON type to a native
type directly when reading data from the database. Together with lazy decoding of BSON structures, this allows
for a more flexible and efficient way to handle BSON data.

## Encoders and Decoders

The codec interface is comprised by two smaller interfaces: encoders and decoders. Both interfaces are marked as
internal, as users are only expected to interact with the Codec interface. The interfaces are typed as generics through
`@template` annotations, allowing for better type checking when they are used. Without type annotations, the interfaces
are equivalent to the following:

```php
namespace MongoDB\Codec;

interface Decoder
{
    public function canDecode(mixed $value): bool;

    public function decode(mixed $value): mixed;

    public function decodeIfSupported(mixed $value): mixed;
}

interface Encoder
{
    public function canEncode(mixed $value): bool;

    public function encode(mixed $value): mixed;

    public function encodeIfSupported(mixed $value): mixed;
}
```

## Codec Interface

The `Codec` interface combines decoding and encoding into a single interface. This will be used for most values except
for documents where a more specific `DocumentCodec` is provided.

The base interface supports encoding from a `NativeType` to a `BSONType` and back. Helper methods to determine whether a
value is supported are provided. The `decodeIfSupported` and `encodeIfSupported` methods are useful to have a codec
encode or decode a value only if it is supported. If it is not supported, the original value is returned.

```php
namespace MongoDB\Codec;

interface Codec extends Decoder, Encoder
{
}
```

## Document Codec

The document codec is special as it is guaranteed to always encode to a BSON document instance and decode to a PHP
object. Document codecs will be used by `MongoDB\Collection` instances to automatically decode BSON data into PHP
objects when reading data, and to encode PHP objects when inserting or replacing data.

```php
namespace MongoDB\Codec;

use MongoDB\BSON\Document;

/** 
 * @template ObjectType of object
 * @extends Codec<ObjectType, Document> 
 */
interface DocumentCodec extends Codec
{
}
```

## Built-in codecs

By default, two codecs are provided: an `ArrayCodec` and an `ObjectCodec`. These two codecs are used to recursively
encode and decode values in arrays and `stdClass` instances, respectively. When encoding or decoding an object,
`ObjectCodec` only handles public properties of the object and ignores private and protected properties.

## Future Work

### Using Codecs

The `MongoDB\Collection` class and all operations that work with documents now take a `codec` option. This option is
passed along to the various operations that already take a `typeMap` option. Collections only support a `DocumentCodec`
instance to guarantee that data always encodes to a BSON document and decodes to a PHP object.

All operations that return documents will use the codec to decode the documents into PHP objects. This includes
the various `find` and `findAndModify` operations in collections as well as the `aggregate` and `watch` operations in
collections, databases, and the client object itself.

When writing data, any operation that takes an entire document will use the codec to automatically encode the document.
This is limited to `insertOne`, `insertMany`, `replaceOne`, and `findOneAndReplace` operations in collections. `update`
operations will not use the codec to encode documents, as they only support update operators and can't work with the
entire document.


### Codecs and type maps

When providing a value for the `codec` option, it takes precedence over the `typeMap` option. An exception is made
when the `codec` option was specified on the collection level, but an operation is given a `typeMap` option. In
this case, the type map is used. The precedence order is as follows:

* operation-level `codec` option
* operation-level `typeMap` option
* collection-level `codec` option
* collection-level `typeMap` option

Codecs are not inherited from the client or the database object, as they are purely used for operations that return
documents. However, database- or client-level aggregation commands will take an operation-level codec option to
decode the resulting documents.
