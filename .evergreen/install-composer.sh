#!/usr/bin/env bash
set -o errexit  # Exit the script with error if any of the commands fail

# Supported environment variables
DEPENDENCIES=${DEPENDENCIES:-} # Specify "lowest" to prefer lowest dependencies
COMPOSER_FLAGS="${COMPOSER_FLAGS:-}" # Optional, additional Composer flags

PATH="$PHP_PATH/bin:$PATH"

install_composer ()
{
  EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

  if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
  fi

  php composer-setup.php --quiet
  rm composer-setup.php
}

case "$DEPENDENCIES" in
   lowest*)
      COMPOSER_FLAGS="${COMPOSER_FLAGS} --prefer-lowest"
      ;;

   *)
      ;;
esac

cp ${PROJECT_DIRECTORY}/.evergreen/config/php.ini ${PHP_PATH}/lib/php.ini

php --ri mongodb

install_composer

# Remove psalm as it's not compatible with PHP 8.4: https://github.com/vimeo/psalm/pull/10928
if [ "$PHP_VERSION" == "8.4" ]; then
  php composer.phar remove --no-update --dev vimeo/psalm
fi

php composer.phar update $COMPOSER_FLAGS
