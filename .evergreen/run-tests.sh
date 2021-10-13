#!/bin/sh
set -o errexit  # Exit the script with error if any of the commands fail

# Supported/used environment variables:
#   SSL               Set to "yes" to enable SSL. Defaults to "nossl"
#   MONGODB_URI       Set the suggested connection MONGODB_URI (including credentials and topology info)
#   API_VERSION       Optional API_VERSION environment variable for run-tests.php
#   IS_MATRIX_TESTING Set to "true" to enable matrix testing. Defaults to empty string. If "true", DRIVER_MONGODB_VERSION and MONGODB_VERSION will also be checked.
#   MOCK_SERVICE_ID   Set to "1" to enable service ID mocking for load balancers. Defaults to empty string.

SSL=${SSL:-nossl}
MONGODB_URI=${MONGODB_URI:-}
API_VERSION=${API_VERSION:-}
IS_MATRIX_TESTING=${IS_MATRIX_TESTING:-}
MOCK_SERVICE_ID=${MOCK_SERVICE_ID:-}

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

# For load balancer testing, we need to enable service ID mocking
if [ "${MOCK_SERVICE_ID}" = "1" ]; then
   PHPUNIT_OPTS="${PHPUNIT_OPTS} -d mongodb.mock_service_id=1"
fi

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

# Disable failing PHPUnit due to deprecations
export SYMFONY_DEPRECATIONS_HELPER=999999

# Run the tests, and store the results in a Evergreen compatible JSON results file
case "$TESTS" in
   atlas-data-lake*)
      MONGODB_URI="mongodb://mhuser:pencil@127.0.0.1:27017"
      php vendor/bin/simple-phpunit --configuration phpunit.evergreen.xml --testsuite "Atlas Data Lake Test Suite" $PHPUNIT_OPTS
      ;;

   versioned-api)
      php vendor/bin/simple-phpunit --configuration phpunit.evergreen.xml --group versioned-api $PHPUNIT_OPTS
      ;;

   serverless)
      php vendor/bin/simple-phpunit --configuration phpunit.evergreen.xml --group serverless $PHPUNIT_OPTS
      ;;

   *)
      php vendor/bin/simple-phpunit --configuration phpunit.evergreen.xml $PHPUNIT_OPTS
      ;;
esac
