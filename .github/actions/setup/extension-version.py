"""Resolve the extension version allowed by the ext-mongodb constraint.

The lowest version is the lower bound of the constraint. The highest version is
the latest matching release published on PECL. This mirrors the resolution done
by .evergreen/compile-extension.sh for the Evergreen build tasks.

Usage: extension-version.py lowest|stable path/to/composer.json
"""

import json
import re
import sys
import urllib.request
import xml.etree.ElementTree as ElementTree

ALL_RELEASES_URL = "https://pecl.php.net/rest/r/mongodb/allreleases.xml"
NAMESPACE = {"r": "http://pear.php.net/dtd/rest.allreleases"}


def fail(message):
    print(f"::error::{message}", file=sys.stderr)
    sys.exit(1)


def parse_constraint(path):
    with open(path) as file:
        constraint = json.load(file)["require"]["ext-mongodb"]

    match = re.fullmatch(r"\^(\d+)\.(\d+)(?:\.(\d+))?", constraint)

    if not match:
        fail(f"Unsupported ext-mongodb constraint: {constraint}")

    major, minor, patch = match.group(1), match.group(2), match.group(3) or "0"

    return constraint, int(major), (int(major), int(minor), int(patch))


def released_versions():
    try:
        with urllib.request.urlopen(ALL_RELEASES_URL, timeout=30) as response:
            document = ElementTree.parse(response)
    except Exception as error:
        fail(f"Cannot read the list of mongodb releases from PECL: {error}")

    for release in document.findall("r:r", NAMESPACE):
        version = release.find("r:v", NAMESPACE).text
        stability = release.find("r:s", NAMESPACE).text

        if stability == "stable":
            yield version, tuple(int(part) for part in version.split("."))


def main():
    target, composer_json = sys.argv[1], sys.argv[2]
    constraint, major, lowest = parse_constraint(composer_json)

    if target == "lowest":
        print("%d.%d.%d" % lowest)

        return

    # Releases are listed from the most recent one
    for version, parsed in released_versions():
        if lowest <= parsed and parsed[0] == major:
            print(version)

            return

    fail(f"No release matching {constraint} found on PECL")


main()
