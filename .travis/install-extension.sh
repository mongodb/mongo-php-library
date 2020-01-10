#!/bin/sh

INI=~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
# tpecl is a helper to compile and cache php extensions
tpecl () {
    local ext_name=$1
    local ext_so=$2
    local ext_dir=$(php -r "echo ini_get('extension_dir');")
    local ext_cache=~/php-ext/$(basename $ext_dir)/$ext_name
    if [[ -e $ext_cache/$ext_so ]]; then
        echo extension = $ext_cache/$ext_so >> $INI
    else
        mkdir -p $ext_cache
        echo yes | pecl install -f $ext_name &&
        cp $ext_dir/$ext_so $ext_cache
    fi
}

if [ "x${DRIVER_VERSION}" != "x" ]; then
  echo "Installing driver version ${DRIVER_VERSION} from PECL"
  tpecl mongodb-${DRIVER_VERSION} mongodb.so
fi
