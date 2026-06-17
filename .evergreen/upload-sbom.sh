#!/usr/bin/env bash
set -eo pipefail

: "${branch_name:?}"
: "${AWS_ACCESS_KEY_ID:?}"
: "${AWS_SECRET_ACCESS_KEY:?}"
: "${AWS_SESSION_TOKEN:?}"

silkbomb="901841024863.dkr.ecr.us-east-1.amazonaws.com/release-infrastructure/silkbomb:2.0"
docker pull "${silkbomb}"

docker run --rm -v "$(pwd):/pwd" \
  --user "$(id -u):$(id -g)" \
  --env 'AWS_ACCESS_KEY_ID' --env 'AWS_SECRET_ACCESS_KEY' --env 'AWS_SESSION_TOKEN' \
  "${silkbomb}" upload \
  --repo mongodb/mongo-php-library \
  --branch "${branch_name}" \
  --sbom-in /pwd/sbom.json
