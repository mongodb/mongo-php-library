#!/bin/sh
set -o errexit  # Exit the script with error if any of the commands fail

set_php_version ()
{
   PHP_VERSION=$1

   if [ ! -d "/opt/php" ]; then
      echo "PHP is not available"
      exit 1
   fi

   if [ -d "/opt/php/${PHP_VERSION}-64bit/bin" ]; then
      export PHP_PATH="/opt/php/${PHP_VERSION}-64bit"
   else
      # Try to find the newest version matching our constant
      export PHP_PATH=`find /opt/php/ -maxdepth 1 -type d -name "${PHP_VERSION}.*-64bit" -print | sort -V -r | head -1`
   fi

   if [ ! -d "$PHP_PATH" ]; then
      echo "Could not find PHP binaries for version ${PHP_VERSION}. Listing available versions..."
      ls -1 /opt/php
      exit 1
   fi

   echo 'PHP_PATH: "'$PHP_PATH'"' > php-expansion.yml
   export PATH=$PHP_PATH/bin:$PATH
}

install_extension ()
{
   rm -f ${PHP_PATH}/lib/php.ini

   if [ "x${EXTENSION_BRANCH}" != "x" ] || [ "x${EXTENSION_REPO}" != "x" ]; then
      CLONE_REPO=${EXTENSION_REPO:-https://github.com/mongodb/mongo-php-driver}
      CHECKOUT_BRANCH=${EXTENSION_BRANCH:-master}

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
   elif [ "x${EXTENSION_VERSION}" != "x" ]; then
      echo "Installing driver version ${EXTENSION_VERSION} from PECL"
      pecl install -f mongodb-${EXTENSION_VERSION}
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

set_php_version $PHP_VERSION
install_extension
install_composer

php composer.phar update $COMPOSER_FLAGS
