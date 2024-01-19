# Code Generator for MongoDB PHP Library

This subproject is used to generate the code that is committed to the repository.
The `generator` directory is not included in `mongodb/builder` package and is not installed by Composer. 

## Contributing

Updating the generated code can be done only by modifying the code generator, or its configuration.

To run the generator, you need to have PHP 8.1+ installed and Composer.

1. Move to the `generator` directory: `cd generator`
1. Install dependencies: `composer install`
1. Run the generator: `./generate`

## Configuration

The `generator/config/*.yaml` files contains the list of operators and stages that are supported by the library.

### Test pipelines

Each operator can contain a `tests` section with a list if pipelines. To represent specific BSON objects,
it is necessary to use Yaml tags:

| BSON Type   | Example                                      |
|-------------|----------------------------------------------|
| Regex       | `!regex '^abc'` <br/> `!regex ['^abc', 'i']` |
| Int64       | `!long '123456789'`                          |
| Decimal128  | `!double '0.9'`                              |
| UTCDateTime | `!date 0`                                    |
| Binary      | `!binary 'IA=='`                             |

To add new test cases to operators, you can get inspiration from the official MongoDB documentation and use
the `generator/js2yaml.html` web page to manually convert a pipeline array from JS to Yaml.
