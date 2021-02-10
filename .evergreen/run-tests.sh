#!/bin/sh
set -o xtrace   # Write all commands first to stderr
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
PHP_VERSION=${PHP_VERSION:-7.2.21}

# The PLATFORM environment variable is used by the matrix testing project.
# For the time being, we can use that to install the correct extension version
if [ "x${PLATFORM}" != "x" ]; then
  DIR=$(dirname $0)
  DRIVER_VERSION=1.5.5 . $DIR/install-dependencies.sh
fi

OS=$(uname -s | tr '[:upper:]' '[:lower:]')
[ -z "$MARCH" ] && MARCH=$(uname -m | tr '[:upper:]' '[:lower:]')

echo "Running $AUTH tests over $SSL, connecting to $MONGODB_URI"

OLD_PATH=$PATH
PATH=/opt/php/${PHP_VERSION}-64bit/bin:$OLD_PATH

# Disable failing PHPUnit due to deprecations
export SYMFONY_DEPRECATIONS_HELPER=999999

# Run the tests, and store the results in a Evergreen compatible JSON results file
case "$TESTS" in
   *)
      php vendor/bin/phpunit --configuration phpunit.evergreen.xml
      ;;
esac
