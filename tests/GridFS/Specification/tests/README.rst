GridFS Tests
============

The YAML and JSON files in this directory are platform-independent tests
meant to exercise a driver's implementation of GridFS.

Converting to JSON
==================

The tests are written in YAML because it is easier for humans to write
and read, and because YAML supports a standard comment format. Each test
is also provided in JSON format because in some languages it is easier
to parse JSON than YAML.

If you modify any test, you should modify the YAML file and then
regenerate the JSON file from it. 
	
One way to convert the files is using an online web page. I used:

http://www.json2yaml.com/

It's advertised as a JSON to YAML converter but it can be used in either direction.

Note: the yaml2json utility from npm is not capable of converting these YAML tests
because it doesn't implement the full YAML spec.
	
Format
======

Each test file has two top level sections:

1. data
2. tests

The data section defines the initial contents of the files and chunks
collections for all tests in that file.

The tests section defines the tests to be run. The format of the tests
section will vary slightly depending on what tests are being defined.
In general, they will have the following sections:

1. description
2. arrange
3. act
4. assert

The arrange section, if present, defines changes to be made to the 
initial contents of the files and chunks collections (as defined by
the data section) before this particular test is run. These changes
are described in the form of write commands that can be sent directly
to MongoDB.

The act section defines what operation (with which arguments) should
be performed.

The assert section defines what should be true at the end of the test.
This includes checking the return value of the operation, as well as
checking the expected contents of the files and chunks collections. The
expected contents of the files and chunks collections are described
in the form of write commands that modify collections named
expected.files and expected.chunks. Before running these commands,
load the initial files and chunks documents into the expected.files
and expected.chunks collections and then run the commands. At that point
you can assert that fs.files and expected.files are the same, and that
expected.chunks and fs.chunks are the same. 

For operations that are expected to succeed the assert section contains
a "result" element describing the expected result. For operations
that are expected to fail the assert section contains an "error"
element describing the expected failure.

The "result" element is either the expected result when it is possible to 
know the result in advance, or it is the special value "&result"
which means that we expect a result (not a failure) but the actual
value of the result could be anything. The notation "&result" is
modeled after YAML syntax for defining an anchor, and the 
result value may be referenced later in the assert section as
"*result".

Another special notation in the assert section is "*actual", which
is used when the value of a field cannot be known in advance of the
test, so the assert logic should accept whatever the actual value
ended up being.
