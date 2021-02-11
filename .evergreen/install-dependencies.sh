#!/bin/sh
set -o xtrace   # Write all commands first to stderr
set -o errexit  # Exit the script with error if any of the commands fail

install_extension ()
{
   # Workaround to get PECL running on PHP 7.0
   export PHP_PEAR_PHP_BIN=${PHP_PATH}/bin/php
   export PHP_PEAR_INSTALL_DIR=${PHP_PATH}/lib/php

   rm -f ${PHP_PATH}/lib/php.ini

   if [ "x${DRIVER_BRANCH}" != "x" ] || [ "x${DRIVER_REPO}" != "x" ]; then
      CLONE_REPO=${DRIVER_REPO:-https://github.com/mongodb/mongo-php-driver}
      CHECKOUT_BRANCH=${DRIVER_BRANCH:-master}

      echo "Compiling driver branch ${CHECKOUT_BRANCH} from repository ${CLONE_REPO}"

      mkdir -p /tmp/compile
      rm -rf /tmp/compile/mongo-php-driver
      git clone ${CLONE_REPO} /tmp/compile/mongo-php-driver
      cd /tmp/compile/mongo-php-driver

      git checkout ${CHECKOUT_BRANCH}
      git submodule update --init
      phpize
      ./configure --enable-mongodb-developer-flags
      make all -j20 > /dev/null
      make install

      cd ${PROJECT_DIRECTORY}
   elif [ "x${DRIVER_VERSION}" != "x" ]; then
      echo "Installing driver version ${DRIVER_VERSION} from PECL"
      pecl install -f mongodb-${DRIVER_VERSION}
   else
      echo "Installing latest driver version from PECL"
      pecl install -f mongodb
   fi

   sudo cp ${PROJECT_DIRECTORY}/.evergreen/config/php.ini ${PHP_PATH}/lib/php.ini

   php --ri mongodb
}

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

# Functions to fetch MongoDB binaries
. ${DRIVERS_TOOLS}/.evergreen/download-mongodb.sh
OS=$(uname -s | tr '[:upper:]' '[:lower:]')

get_distro

case "$DISTRO" in
   cygwin*)
      echo "Install Windows dependencies"
      ;;

   darwin*)
      echo "Install macOS dependencies"
      ;;

   linux-rhel*)
      echo "Install RHEL dependencies"
      ;;

   linux-ubuntu*)
      echo "Install Ubuntu dependencies"
      sudo apt-get install -y awscli || true
      ;;

   sunos*)
      echo "Install Solaris dependencies"
      sudo /opt/csw/bin/pkgutil -y -i sasl_dev || true
      ;;

   *)
      echo "All other platforms..."
      ;;
esac

case "$DEPENDENCIES" in
   lowest*)
      COMPOSER_FLAGS="${COMPOSER_FLAGS} --prefer-lowest"
      ;;

   *)
      ;;
esac

PHP_PATH=/opt/php/${PHP_VERSION}-64bit
OLD_PATH=$PATH
PATH=$PHP_PATH/bin:$OLD_PATH

install_extension
install_composer

php composer.phar update $COMPOSER_FLAGS
