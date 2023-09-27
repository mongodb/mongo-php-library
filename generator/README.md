# Code Generator for MongoDB PHP Library

This subproject is used to generate the code that is committed to the repository.
The `generator` directory is not included in `mongodb/mongodb` package and is not installed by Composer. 

## Contributing

Updating the generated code can be done only by modifying the code generator, or its configuration.

To run the generator, you need to have PHP 8.2+ installed and Composer.

1. Move to the `generator` directory: `cd generator`
1. Install dependencies: `composer install`
1. Run the generator: `bin/console generate`
1. To apply the coding standards of the project, run `vendor/bin/phpcbf` from the root of the repository: `cd .. && vendor/bin/phpcbf`

## Configuration

The `generator/config/*.yaml` files contains the list of operators and stages that are supported by the library.
