# Aggregation Builder

The Aggregation Builder is a set of classes and functions that represent all the stages and operators of the MongoDB.

# Package

The design for the query builder requires that we use PHP 8.1 capacities: named arguments and union types. 
Since the `mongodb/mongodb` library have to stay compatible with PHP 7.4, this new feature is implemented in a separate
package `mongodb/builder` that requires PHP 8.1. The implementation being experimental and the API not stable at the
moment, the package will be tagged with a `0.x`. Once this package is stable, and `mongodb/mongodb` requires PHP 8.1,
the code will be merged in the `mongodb/mongodb` and the package `mongodb/builder` will be abandoned.
Once merged, `mongodb/mongodb` will have a "replace" rule.

# Operator Definition

Operators are defined in Yaml files, independently of the implementation. These definitions are used to generate the PHP
code and could be used to generate other implementations: other languages or other libraries like Doctrine or Laravel.

The JSON Schema of the Yaml files is defined in `generator/config/schema.json` and added in header of each Yaml file for validation.
PHPStorm will support it in its next version: https://youtrack.jetbrains.com/issue/IDEA-323117

Operators are categorized by where they can be used:

- A `pipeline` is a list of `stage`. 
- A `stage` is the root operator of aggregations pipelines.
- A `expression` is used in `stage` parameters and can be converted to `query` with `$expr`. 
- A `query` is used in stages `$match`, `$geoNear` and `$graphLookup`. It could be used in `find` commands.
- A `filter` is used to compose `query` when affected to a field path.
- An `accumulator` is not an expression. It can be used in these stages: `$group`, `$bucket`, `$bucketAuto`, `$setWindowFields`.
- A `window` operator is used in `$setWindowFields` stage. 

## Pipeline

A pipeline is a list of stages. It can be used in the `aggregate` command and the stages `$lookup`, `$facet` and `$unionWith`.
It has a dedicate class `Pipeline` that accepts a variadic list of stages as constructor.

A pipeline can be created by composing stages and other pipelines. The array unpacking operator `...` is used to inject
the stages of a pipeline in another pipeline.

```php
$pipeline1 = new Pipeline(
    Stage::group(...),
    Stage::project(...),
);
$pipeline2 = new Pipeline(
    Stage::match(...),
    Stage::limit(...),
    $pipeline1,
    Stage::limit(...),
);
```
Pipeline object are immutable/read-only.

## Stage

A stage is a root operator of an aggregation pipeline. They are not expressions and can't be used in other stages
without being wrapped in a pipeline.

## Expression

Expression namespace contains all the aggregation pipeline operators, the field paths, literal and variable.

For each BSON type, we have a `resolvesTo<Type>` interface that is implemented by the corresponding expression operators,
and a `<type>FieldName` class that is used to build field paths assuming the field is of the given type.

### Object

Object expression doesn't accept any PHP `object`, but only `stdClass` and BSON serializable objects. `array` are accepted
because that's the most common way to represent maps in PHP. But we recommend to use `object(... $fields)` helper to
create `stdClass`. It ensures the type is correct for empty arrays or list that would be converted to array instead of
object.

BSONTypes are skipped when encoding.

### Type hierarchy

The most specific interface implements the less specific one. For example, `resolvesToInt` implements `resolvesToLong`,
which implements `resolvesToNumber`, which implements `expression`.

The type hierarchy is designed with two aspects in mind: the operator parameters and the operator results.

#### Number

If an operator resolves to a `number`, we don't know if it is an `int`, a `float`, a `decimal`, or a `long`.
But if it resolves to an `int`, we know it is a `long`, a `number`, and an `expression`.

If an operator accepts a `number`, it also accepts an `int`, a `float`, a `decimal`, or a `long`.
So we use the `resolvesToNumber` type. As `resolvesToInt` implements `resolvesToNumber`, it is also accepted.

### Any

The `resolvesToAny` type means that we don't know the type of the expression. This interface implements all the other
`ResovesTo<Type>` interfaces because we can't restrict the expression from being used as parameter for parameters
that requires more specific types.
`resolvesToAny` is only used as result type (as interface for operators class). For parameters, we use the generic
`expression` interface, otherwise only operator resulting in `any` would be eligible, the operators with more specific
result types would be rejected.

We rely on the server will reject the query if the expression is not of the expected type.

### Field Path

FieldPath satisfies any types (implements resolvesToAny interface) because we don't know the type of the field.
Create a FieldPathInterface for encoding needs.

## Query & Field Query

The `query` are used in a `$match`, `$geoNear` or `$graphLookup` stages and `$elemMatch` operator.
The `fieldQuery` are used compose query. A query is a map of field name to filter and/or a list of other queries.

Queries can be created with `$and`, `$or`, `$nor`, `$jsonSchema`, `$text`, `$comment` operators or with the `QueryObject`
class when composed with filter. 
The factory function `Query::query()` accepts variadic arguments which can be named (for `filter` with field
path) or sequential (for `query` without field path). This function is used by stages that accept a `query` as parameter
to create the `QueryObject`. The encoder handle this object specifically to merge all the queries in a single object.

We customize the `Stage::match()` factory function to shortcut the `Query::query()` function.
```php
$or = Query::or(...);
$and = Query::and(...);

['eq' => $eq, 'and' => $and] = Query::functions();

Stage::match(
    // $or, $and, $nor, $comment, $jsonSchema, $text can't have a field name
    // An exception will be thrown if a field name is used
    Query::or(
        Query::query(foo:  Query::eq(...)),
        Query::and(
            Query::query(bar:  Query::eq(...)),
            Query::query(baz:  Query::eq(...)),
        )
    ),
    Query::comment('...'),
    // Equality query on a field
    foo: '...',
    // Negate a query with $not
    bar:    Query::no  Query🚬::gt(...)),
    // Use array unpacking for complex field path
    ...['foo.$.baz' =>  Query::eq(...)],
    // Multiple filters on the same field
    baz:    Query::lt(...),  Query::gt(...)],
)
```

Without the custom factory function for `$match` stage, the queries would be written with a single root query object.
```php
Stage::match(Query::or(Query::query(...), Query::query(...)));
Stage::match(Query::query(...));
```

**Notes:**
- Before PHP 8.1, we cannot combine named arguments and argument unpacking
- Arguments must be provided in this order: sequential arguments, array unpacking positional, array unpacking named, named arguments.
```php
variadic('positional', ...['unpacked positional'], ...['key' => 'unpacked named'], name: 'named');
```
- PHP triggers a fatal error if an unpacked named argument conflicts with an other named argument.
```php
variadic(...['name' => 'unpacked named'], name: 'named');
// Uncaught Error: Named parameter $name overwrites previous argument
```

## Projection

Projection operators have a dedicated namespace and interface to not be confused expression operators with the same
name.

# Specificities

Some operators have specificities that are not covered by the generic builder.

## `$group` stage: required and variadic parameters in the same object

To encode, the required `_id` parameter is rendered at the same level of the list for fields. 

**Implementation:** The `GroupStage` has a special `Encode::Group` value exploited by the encoder.

## `$slice` expression operator and `$slice` projection operator: optional values in an array

These operators are encoded as an array of values. The meaning of the values depends on the number of values.

https://www.mongodb.com/docs/v7.0/reference/operator/projection/slice/
https://www.mongodb.com/docs/v7.0/reference/operator/aggregation/slice/

## Date Expression Operators

Concerned operators are: `$dayOfMonth`, `$dayOfWeek`, `$dayOfYear`, `$hour`, `$isoDayOfWeek`, `$isoWeek`, `$isoWeekYear`,
`$millisecond`, `$minute`, `$month`, `$second`, `$week`, `$year`.

These date expression operators accept have a "date expression" as parameter or an object with
a `date` and an optional `timezone` properties.

```
{ <operator>: <dateExpression> }
{ <operator>: { date: <dateExpression> } }
{ <operator>: { date: <dateExpression>, timezone: <tzExpression> } }
```

In order to normalize encoding, we always use an object with `date` and `timezone` properties and never the short form
even if the timezone is not specified.

Date Expression can be `ResolveToDate`, `ResolveToTimestamp` or `ResolveToObjectId`. But we don't introduce a new
meta type like `number` or `any` as we can list this types directly in the Yaml config.

## `$setWindowFields` stage's output

The output parameter of the `$setWindowFields` stage is an object which mixes a "window operator" and the optional `window`
parameter at the same level.

Window operators implement the `WindowInterface` interface and are encoded like other operators.

```
output: {
   <output field 1>: {
      <window operator>: <window operator parameters>,
      window: {
         documents: [ <lower boundary>, <upper boundary> ],
         range: [ <lower boundary>, <upper boundary> ],
         unit: <time unit>
      }
   },
   <output field 2>: { ... },
   ...
   <output field n>: { ... }
}
```

We create a helper with a specific encoding that merges the operator and the window parameters.

```php
class Window {
    public static function output(
        WindowInterface $operator,
        Optional|array $documents,
        Optional|array $range,
        Optional|string $unit
    ): WindowInterface;
}
```

Which is encoded to:
```
(object) {
    $<operator name>: <operator parameters>,
    window: {
        documents: $documents,
        range: $range,
        unit: $unit
    }
}
```

And used like this:
```php
$setWindowFields = Stage::setWindowFields(
    // ... other parameters
    output: object(
        cumulativeQuantityForYear: Window::output(
            Expression::sum(Expression::fieldPath('quantity')),
            documents: [null, 0],
            range: [null, 0],
            unit: 'second'
        ),
    ),
);
```

Is `WindowOutput` are nested, the `window` parameter is replaced, not merged.

# Code Generator

We use a separate project inside the repository to generate the code. It has a single command that reads the configuration
and create the PHP files using `nette/php-generator` package.

* `ExpressionClassGenerator` create the expression classes, interface and enum defined in `expressions.php`.
* `ExpressionFactoryGenerator` create factory functions for the classes generated by `ExpressionClassGenerator`.
* `OperatorClassGenerator` create the operator classes defined in `operators.php` and Yaml files.
* `OperatorFactoryGenerator` create factory functions for the classes generated by `OperatorClassGenerator`.

The directories `src/Builder/{Expression|Stage|Query}` contains only generated code, so we can easily regenerate it
and the configuration of tools is simplified.

## Factory

Factory functions are static methods generated in an `@internal` trait. This traits are use in `enum`, which are
used as static final classes. Using traits allows to customize the factory functions if needed (ie. `Query::regex` function).

## `$regex` query

The `$regex` query requires a `MongoDB\BSON\Regex` object as parameter. We create a custom factory function to create
this object from a string.

```php
function regex(MongoDB\BSON\Regex|string $pattern, string $flags = ''): RegexOperator
```

## `$` positional projection

