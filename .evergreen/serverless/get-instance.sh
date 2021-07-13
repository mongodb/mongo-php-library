#!/bin/bash

set -o errexit
set +o xtrace

if [ -z "$SERVERLESS_INSTANCE_NAME" ]; then
    echo "Instance name must be provided via SERVERLESS_INSTANCE_NAME environment variable"
    exit 1
fi

if [ -z "$SERVERLESS_DRIVERS_GROUP" ]; then
    echo "Drivers Atlas group must be provided via SERVERLESS_DRIVERS_GROUP environment variable"
    exit 1
fi

if [ -z "$SERVERLESS_API_PRIVATE_KEY" ]; then
    echo "Atlas API private key must be provided via SERVERLESS_API_PRIVATE_KEY environment variable"
    exit 1
fi

if [ -z "$SERVERLESS_API_PUBLIC_KEY" ]; then
    echo "Atlas API public key must be provided via SERVERLESS_API_PUBLIC_KEY environment variable"
    exit 1
fi

API_BASE_URL="https://account-dev.mongodb.com/api/private/nds/serverless/groups/$SERVERLESS_DRIVERS_GROUP"

curl \
  --silent \
  --show-error \
  -u "$SERVERLESS_API_PUBLIC_KEY:$SERVERLESS_API_PRIVATE_KEY" \
  -X GET \
  --digest \
  --header "Accept: application/json" \
  "${API_BASE_URL}/instances/${SERVERLESS_INSTANCE_NAME}?pretty=true" \

echo ""
