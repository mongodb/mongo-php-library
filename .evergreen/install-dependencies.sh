#!/usr/bin/env bash
set -o errexit  # Exit the script with error if any of the commands fail

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
