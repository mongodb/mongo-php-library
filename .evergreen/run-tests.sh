#!/bin/sh
set -o errexit  # Exit the script with error if any of the commands fail

# Supported/used environment variables:
#       AUTH                    Set to enable authentication. Defaults to "noauth"
#       SSL                     Set to enable SSL. Defaults to "nossl"
#       MONGODB_URI             Set the suggested connection MONGODB_URI (including credentials and topology info)
#       MARCH                   Machine Architecture. Defaults to lowercase uname -m


AUTH=${AUTH:-noauth}
SSL=${SSL:-nossl}
MONGODB_URI=${MONGODB_URI:-}
TESTS=${TESTS:-}
API_VERSION=${API_VERSION:-}
IS_MATRIX_TESTING=${IS_MATRIX_TESTING:-}

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

OS=$(uname -s | tr '[:upper:]' '[:lower:]')
[ -z "$MARCH" ] && MARCH=$(uname -m | tr '[:upper:]' '[:lower:]')

echo "Running tests with $AUTH and $SSL, connecting to: $MONGODB_URI"

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

   *)
      php vendor/bin/simple-phpunit --configuration phpunit.evergreen.xml $PHPUNIT_OPTS
      ;;
esac
