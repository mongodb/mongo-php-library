#!/bin/bash

echo Loading MO for $DEPLOYMENT

if [[ -z $TRAVIS_BUILD_DIR ]]; then
    export TRAVIS_BUILD_DIR=`pwd`;
fi

# Replace the default client certificate with the new one to make sure mo keeps working
cp ${TRAVIS_BUILD_DIR}/mongo-orchestration/ssl/client.pem ${MO_PATH}/lib/client.pem

URI_FIELD="mongodb_uri"
URI_SUFFIX=""

case $DEPLOYMENT in
  SHARDED_CLUSTER)
    CONFIG="sharded_clusters/cluster.json"
    URI_SUFFIX="/?retryWrites=false"
    ;;
  SHARDED_CLUSTER_RS)
    CONFIG="sharded_clusters/cluster_replset.json"
    ;;
  STANDALONE_AUTH)
    CONFIG="standalone/standalone-auth.json"
    URI_FIELD="mongodb_auth_uri"
    ;;
  STANDALONE_OLD)
    CONFIG="standalone/standalone-old.json"
    ;;
  STANDALONE_SSL)
    CONFIG="standalone/standalone-ssl.json"
    URI_SUFFIX="/?ssl=true&sslallowinvalidcertificates=true"
    ;;
  REPLICASET)
    CONFIG="replica_sets/replicaset.json"
    ;;
  REPLICASET_SINGLE)
    CONFIG="replica_sets/replicaset-one-node.json"
    ;;
  REPLICASET_OLD)
    CONFIG="replica_sets/replicaset-old.json"
    ;;
  *)
    CONFIG="standalone/standalone.json"
    ;;
esac

${TRAVIS_BUILD_DIR}/.travis/mo.sh ${TRAVIS_BUILD_DIR}/mongo-orchestration/$CONFIG start > /tmp/mo-result.json

if [ $? -ne 0 ]; then
  cat /tmp/mo-result.json
  cat ${TRAVIS_BUILD_DIR}/server.log
  exit 1
fi

cat /tmp/mo-result.json | tail -n 1 | php ${TRAVIS_BUILD_DIR}/.travis/get_uri.php $URI_FIELD $URI_SUFFIX > /tmp/uri.txt

echo -n "MongoDB Test URI: "
cat /tmp/uri.txt
echo

echo "Raw MO Response:"
cat /tmp/mo-result.json

echo
