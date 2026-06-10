#!/usr/bin/env bash
# Environment Variables:
#   None required — all inputs are derived from the repository.
set -eo pipefail

SERIAL_NUMBER="urn:uuid:dc42a43b-4ace-4c42-9a6e-0b9e28fdd100"

echo "Installing CycloneDX PHP Composer plugin"
composer config allow-plugins.cyclonedx/cyclonedx-php-composer true
composer require --dev cyclonedx/cyclonedx-php-composer:6.2.0 --no-update

echo "Updating dependencies"
# --ignore-platform-reqs: SBOM generation doesn't need to run the code, so extension
# availability and exact PHP patch versions don't matter here.
# --no-scripts: skip git submodule updates and other scripts that require a full dev setup.
composer update --ignore-platform-reqs --no-scripts

echo "Generating SBOM"
composer CycloneDX:make-sbom \
  --spec-version=1.5 \
  --output-format=JSON \
  --output-file=sbom.cdx.json \
  --omit dev

echo "Updating sbom.json with version tracking"

CURRENT_VERSION=$(jq -r '.version // 0' sbom.json 2>/dev/null || echo 0)
NEW_CONTENT=$(jq -S 'del(.version, .metadata.timestamp)' sbom.cdx.json)
OLD_CONTENT=$(jq -S 'del(.version, .metadata.timestamp)' sbom.json 2>/dev/null || echo '{}')

if [ "$NEW_CONTENT" = "$OLD_CONTENT" ]; then
  NEW_VERSION=$CURRENT_VERSION
  echo "SBOM content unchanged, keeping version ${NEW_VERSION}"
else
  NEW_VERSION=$((CURRENT_VERSION + 1))
  echo "SBOM content changed, incrementing version to ${NEW_VERSION}"
fi

jq --argjson v "$NEW_VERSION" --arg serial "$SERIAL_NUMBER" \
  '.version = $v | .serialNumber = $serial' sbom.cdx.json > sbom.json
rm sbom.cdx.json

echo "Generated sbom.json (version ${NEW_VERSION})"
