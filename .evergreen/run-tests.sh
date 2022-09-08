#!/bin/sh
set -o errexit  # Exit the script with error if any of the commands fail

# Supported environment variables
API_VERSION=${API_VERSION:-} # Optional API_VERSION environment variable
CRYPT_SHARED_LIB_PATH="${CRYPT_SHARED_LIB_PATH:-}" # Optional path to crypt_shared library
DRIVER_MONGODB_VERSION=${DRIVER_MONGODB_VERSION:-} # Required if IS_MATRIX_TESTING is "true"
IS_MATRIX_TESTING=${IS_MATRIX_TESTING:-} # Specify "true" to enable matrix testing. Defaults to empty string. If "true", DRIVER_MONGODB_VERSION and MONGODB_VERSION will also be checked.
MONGODB_URI=${MONGODB_URI:-} # Connection string (including credentials and topology info)
MONGODB_VERSION=${MONGODB_VERSION:-} # Required if IS_MATRIX_TESTING is "true"
SKIP_CRYPT_SHARED="${SKIP_CRYPT_SHARED:-no}" # Specify "yes" to ignore CRYPT_SHARED_LIB_PATH. Defaults to "no"
SSL=${SSL:-no} # Specify "yes" to enable SSL. Defaults to "no"
TESTS=${TESTS:-} # Optional test group. Defaults to all tests

# For matrix testing, we have to determine the correct driver version
if [ "${IS_MATRIX_TESTING}" = "true" ]; then
   case "${DRIVER_MONGODB_VERSION}" in
      '4.4')
         export EXTENSION_VERSION='1.8.2'
         ;;
      '4.2')
         export EXTENSION_VERSION='1.6.1'
         ;;
      '4.0')
         export EXTENSION_VERSION='1.5.5'
         ;;
   esac

   case "${MONGODB_VERSION}" in
      latest)
         MONGODB_VERSION_NUMBER='5.0'
         ;;
      *)
         MONGODB_VERSION_NUMBER=$MONGODB_VERSION
         ;;
   esac

   PHPUNIT_OPTS="--dont-report-useless-tests --exclude-group matrix-testing-exclude-server-${MONGODB_VERSION_NUMBER}-driver-${DRIVER_MONGODB_VERSION},matrix-testing-exclude-server-${MONGODB_VERSION_NUMBER}-driver-${DRIVER_MONGODB_VERSION}-topology-${TOPOLOGY}"

   DIR=$(dirname $0)
   . $DIR/install-dependencies.sh
fi

# Enable verbose output to see skipped and incomplete tests
PHPUNIT_OPTS="${PHPUNIT_OPTS} -v --configuration phpunit.evergreen.xml"

# Determine if MONGODB_URI already has a query string
SUFFIX=$(echo "$MONGODB_URI" | grep -Eo "\?(.*)" | cat)

if [ "$SSL" = "yes" ]; then
   if [ -z "$SUFFIX" ]; then
      MONGODB_URI="${MONGODB_URI}/?ssl=true&sslallowinvalidcertificates=true"
   else
      MONGODB_URI="${MONGODB_URI}&ssl=true&sslallowinvalidcertificates=true"
   fi
fi

echo "Running tests with URI: $MONGODB_URI"

# Disable PHPUnit test failures due to deprecations
# See: https://symfony.com/doc/current/components/phpunit_bridge.html#internal-deprecations
export SYMFONY_DEPRECATIONS_HELPER=999999

# Export environment vars that may be referenced by the test suite
export API_VERSION="${API_VERSION}"
export CRYPT_SHARED_LIB_PATH="${CRYPT_SHARED_LIB_PATH}"
export MONGODB_URI="${MONGODB_URI}"

# Run the tests, and store the results in a junit result file
case "$TESTS" in
   atlas-data-lake*)
      php vendor/bin/simple-phpunit $PHPUNIT_OPTS --testsuite "Atlas Data Lake Test Suite"
      ;;

   csfle)
      php vendor/bin/simple-phpunit $PHPUNIT_OPTS --group csfle
      ;;

   versioned-api)
      php vendor/bin/simple-phpunit $PHPUNIT_OPTS --group versioned-api
      ;;

   serverless)
      php vendor/bin/simple-phpunit $PHPUNIT_OPTS --group serverless
      ;;

   *)
      php vendor/bin/simple-phpunit $PHPUNIT_OPTS
      ;;
esac
