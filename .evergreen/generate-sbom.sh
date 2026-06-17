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
  --omit dev \
  --no-validate
# --no-validate: skips the plugin's built-in schema validation. Schema validation is
# performed separately by cyclonedx-cli (see .github/workflows/sbom.yml), which produces
# clearer diagnostics. Silkbomb will also reject a malformed SBOM on upload.

jq --argjson v 1 --arg serial "$SERIAL_NUMBER" \
  '.version = $v | .serialNumber = $serial' sbom.cdx.json > sbom.json
rm sbom.cdx.json

echo "Generated sbom.json"
