#!/usr/bin/env bash
set -o errexit  # Exit the script with error if any of the commands fail

PATH="$PHP_PATH/bin:$PATH"

source ${PROJECT_DIRECTORY}/.extension-version

# Lowest or highest version of the extension allowed by the composer.json
# constraint. The lowest version is the lower bound of the constraint, the
# highest one is the latest matching release published on PECL.
extension_version ()
{
   php -r '
      $constraint = json_decode(file_get_contents($argv[2]), true)["require"]["ext-mongodb"];

      if (!preg_match("/^\^(\d+)\.(\d+)(?:\.(\d+))?$/", $constraint, $matches)) {
         fwrite(STDERR, sprintf("Unsupported ext-mongodb constraint: %s\n", $constraint));
         exit(1);
      }

      $lowest = sprintf("%d.%d.%d", $matches[1], $matches[2], $matches[3] ?? 0);

      if ($argv[1] === "lowest") {
         echo $lowest;
         exit(0);
      }

      $releases = @simplexml_load_file("https://pecl.php.net/rest/r/mongodb/allreleases.xml");

      if ($releases === false) {
         fwrite(STDERR, "Cannot read the list of mongodb releases from PECL\n");
         exit(1);
      }

      // Releases are listed from the most recent one
      foreach ($releases->r as $release) {
         $version = (string) $release->v;

         if ((string) $release->s !== "stable") {
            continue;
         }

         if (version_compare($version, $lowest, ">=") && version_compare($version, ($matches[1] + 1) . ".0.0", "<")) {
            echo $version;
            exit(0);
         }
      }

      fwrite(STDERR, sprintf("No release matching %s found on PECL\n", $constraint));
      exit(1);
   ' "$1" ${PROJECT_DIRECTORY}/composer.json
}

# Turn EXTENSION_TARGET into the EXTENSION_BRANCH or EXTENSION_VERSION expected
# by install_extension. Both variables can also be set explicitly, e.g. in a
# patch build, in which case they take precedence over EXTENSION_TARGET.
resolve_extension_target ()
{
   if [ "x${EXTENSION_BRANCH}" != "x" ] || [ "x${EXTENSION_VERSION}" != "x" ]; then
      return
   fi

   if [ "${EXTENSION_REQUIRE_NEXT_MINOR}" = "true" ]; then
      EXTENSION_TARGET="next-minor"
   fi

   case "${EXTENSION_TARGET:-stable}" in
      stable)
         EXTENSION_VERSION=$(extension_version highest)
         ;;
      lowest)
         EXTENSION_VERSION=$(extension_version lowest)
         ;;
      next-stable)
         EXTENSION_BRANCH="${EXTENSION_STABLE_BRANCH}"
         ;;
      next-minor)
         EXTENSION_BRANCH="${EXTENSION_NEXT_MINOR_BRANCH}"
         ;;
      *)
         echo "Unknown extension target: ${EXTENSION_TARGET}" >&2
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
