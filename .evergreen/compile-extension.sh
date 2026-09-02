#!/usr/bin/env bash
set -o errexit  # Exit the script with error if any of the commands fail

PATH="$PHP_PATH/bin:$PATH"

# Branch of the latest stable minor version of the extension.
EXTENSION_STABLE_BRANCH="v2.5"

# Branch of the next minor version of the extension, still in development.
EXTENSION_NEXT_MINOR_BRANCH="v2.x"

# Set this to a branch name (e.g. "v2.x") to compile every task from source
# while the library depends on an unreleased version of the extension. This
# also overrides the "lowest" target, as the lowest released version cannot run
# the library code in that case. Reset to an empty value once the extension is
# released. See the "Continuous integration" section of CONTRIBUTING.md.
EXTENSION_DEV_BRANCH=""
# EXTENSION_DEV_BRANCH="v2.x"

# Lowest version of the extension allowed by the composer.json constraint.
lowest_extension_version ()
{
   php -r '
      $constraint = json_decode(file_get_contents($argv[1]), true)["require"]["ext-mongodb"];

      if (!preg_match("/^\^(\d+)\.(\d+)(?:\.(\d+))?$/", $constraint, $matches)) {
         fwrite(STDERR, sprintf("Unsupported ext-mongodb constraint: %s\n", $constraint));
         exit(1);
      }

      printf("%d.%d.%d", $matches[1], $matches[2], $matches[3] ?? 0);
   ' ${PROJECT_DIRECTORY}/composer.json
}

# Turn EXTENSION_TARGET into the EXTENSION_BRANCH or EXTENSION_VERSION expected
# by install_extension. Both variables can also be set explicitly, e.g. in a
# patch build, in which case they take precedence over EXTENSION_TARGET.
resolve_extension_target ()
{
   if [ "x${EXTENSION_BRANCH}" != "x" ] || [ "x${EXTENSION_VERSION}" != "x" ]; then
      return
   fi

   if [ "x${EXTENSION_DEV_BRANCH}" != "x" ]; then
      EXTENSION_BRANCH="${EXTENSION_DEV_BRANCH}"

      return
   fi

   case "${EXTENSION_TARGET:-stable}" in
      stable)
         # Latest release from PECL, i.e. the highest version allowed by composer.json
         ;;
      lowest)
         EXTENSION_VERSION=$(lowest_extension_version)
         ;;
      next-stable)
         EXTENSION_BRANCH="${EXTENSION_STABLE_BRANCH}"
         ;;
      next-minor)
         EXTENSION_BRANCH="${EXTENSION_NEXT_MINOR_BRANCH}"
         ;;
      *)
         echo "Unknown EXTENSION_TARGET: ${EXTENSION_TARGET}" >&2
         exit 1
         ;;
   esac
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
   else
      # The base images ship a channel snapshot that predates recent releases,
      # so PECL would silently resolve down to an older version.
      pecl channel-update pecl.php.net

      if [ "${EXTENSION_VERSION}" != "" ]; then
         echo "Installing driver version ${EXTENSION_VERSION} from PECL"
         MAKEFLAGS=-j20 pecl install -f mongodb-${EXTENSION_VERSION}
      else
         echo "Installing latest driver version from PECL"
         MAKEFLAGS=-j20 pecl install -f mongodb
      fi
   fi

   cp ${PROJECT_DIRECTORY}/.evergreen/config/php.ini ${PHP_PATH}/lib/php.ini

   php --ri mongodb
}

resolve_extension_target
install_extension
