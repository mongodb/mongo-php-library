# Converting BSON data through codecs

The codec system is a more advanced way to convert BSON data to native types and back, designed for libraries with more
advanced use cases, e.g. object mappers. It is designed to decouple the serialisation logic from the data model,
allowing for more flexible implementations.

## Encoders and Decoders

The codec interface is split into two smaller interfaces: encoders and decoders. Both interfaces are marked as internal,
as users are only expected to interact with the Codec interface. The interfaces are typed using Psalm generics, allowing
for better type checking when they are used. Without type annotations, the interfaces are equivalent to the following:

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

## Codec interface

The `Codec` interface combines decoding and encoding into a single interface. This will be used for most values except
for documents where a more specific `DocumentCodec` is provided.

The base interface supports encoding from a `NativeType` to a `BSONType` and back. Helper methods to determine whether a
value is supported are provided. The `decodeIfSupported` and `encodeIfSupported` methods are useful to have a codec
encode or decode a value only if it is supported. If it is not supported, the original value is returned.

```php
namespace MongoDB\Codec;

/**
 * @psalm-template BSONType
 * @psalm-template NativeType
 * @template-extends Decoder<BSONType, NativeType>
 * @template-extends Encoder<BSONType, NativeType>
 */
interface Codec extends Decoder, Encoder
{
}
```

## Document codec

The document codec is special as it is guaranteed to always encode to a BSON document instance and decode to a PHP
object. Document codecs can be provided to a `MongoDB\Collection` instance to have it automatically decode data read
from the database. Likewise, any supported value is encoded before writing to the database in `insert` and `replace`
operations.

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

## Using codecs

The `MongoDB\Collection` class and all operations that work with documents now take a `codec` option. This can be
an instance of a `DocumentCodec` that will be used to encode documents (for insert and replace operations) and decode
them into PHP objects when reading data.

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

