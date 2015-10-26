#!/bin/bash

# HHVM requires gcc >= 4.8.0, but Ubuntu 12.04 only ships with 4.6
sudo add-apt-repository ppa:ubuntu-toolchain-r/test -y

# HHVM requires Boost 1.51, but Ubuntu 12.04 only ships with 1.48
sudo add-apt-repository ppa:boost-latest/ppa -y

# Install prebuilt packages for Ubuntu 12.04
sudo add-apt-repository ppa:mapnik/boost -y
sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0x5a16e7281be7a449
echo deb http://dl.hhvm.com/ubuntu precise main | sudo tee /etc/apt/sources.list.d/hhvm.list

sudo apt-get update
sudo apt-get install gcc-4.8 g++-4.8 libboost1.55-all-dev hhvm-dev -qqy

# Make gcc-4.8 the default compiler
sudo update-alternatives --install /usr/bin/gcc gcc /usr/bin/gcc-4.8 60 \
                         --slave /usr/bin/g++ g++ /usr/bin/g++-4.8
sudo update-alternatives --install /usr/bin/gcc gcc /usr/bin/gcc-4.6 40 \
                         --slave /usr/bin/g++ g++ /usr/bin/g++-4.6
sudo update-alternatives --set gcc /usr/bin/gcc-4.8

# Install libgoogle.log-dev
wget http://launchpadlibrarian.net/80433359/libgoogle-glog0_0.3.1-1ubuntu1_amd64.deb
sudo dpkg -i libgoogle-glog0_0.3.1-1ubuntu1_amd64.deb
rm libgoogle-glog0_0.3.1-1ubuntu1_amd64.deb
wget http://launchpadlibrarian.net/80433361/libgoogle-glog-dev_0.3.1-1ubuntu1_amd64.deb
sudo dpkg -i libgoogle-glog-dev_0.3.1-1ubuntu1_amd64.deb
rm libgoogle-glog-dev_0.3.1-1ubuntu1_amd64.deb

# Install libjemalloc
wget http://ubuntu.mirrors.tds.net/ubuntu/pool/universe/j/jemalloc/libjemalloc1_3.6.0-2_amd64.deb
sudo dpkg -i libjemalloc1_3.6.0-2_amd64.deb
rm libjemalloc1_3.6.0-2_amd64.deb
wget http://ubuntu.mirrors.tds.net/ubuntu/pool/universe/j/jemalloc/libjemalloc-dev_3.6.0-2_amd64.deb
sudo dpkg -i libjemalloc-dev_3.6.0-2_amd64.deb
rm libjemalloc-dev_3.6.0-2_amd64.deb

mkdir hhvm-mongodb
wget -qO- "$1" | tar xvz -C ./hhvm-mongodb --strip-components=1
cd hhvm-mongodb

cd libbson
./autogen.sh > /dev/null
cd ..

cd libmongoc
./autogen.sh > /dev/null
cd ..

hphpize
cmake .
make configlib
make

sudo cp mongodb.so /etc/hhvm
echo 'hhvm.dynamic_extensions[mongodb]=/etc/hhvm/mongodb.so' | sudo tee --append /etc/hhvm/php.ini > /dev/null
