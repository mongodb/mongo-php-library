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

OS=$(uname -s | tr '[:upper:]' '[:lower:]')
[ -z "$MARCH" ] && MARCH=$(uname -m | tr '[:upper:]' '[:lower:]')

echo "Running $AUTH tests over $SSL, connecting to $MONGODB_URI"

OLD_PATH=$PATH
PATH=/opt/php/${PHP_VERSION}-64bit/bin:$OLD_PATH

# Run the tests, and store the results in a Evergreen compatible JSON results file
case "$OS" in
   *)
      php vendor/bin/phpunit -v
      ;;
esac

