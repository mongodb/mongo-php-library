# Releasing

## Update extension requirement (new minor versions only)

In `composer.json`, ensure that the version of `ext-mongodb` is correct for
the library version being released. For a library version x.y.z, we always
require extension version x.y.0 as a minimum version.

After bumping the extension version in composer.json, the `vars` for calling
`compile extension` from `build-extension.yml` in the Evergreen configuration
must be updated:

* The `stable` task should specify no vars.
* The `lowest` task should specify `EXTENSION_VERSION` with the version that
  was just released.
* The `next-stable` task should specify `EXTENSION_BRANCH` with the branch that
  was just created.
* The `next-minor` task should specify `EXTENSION_BRANCH: master`.

The `DRIVER_VERSION` environment variable for any GitHub Actions should also be
set to `stable`.

After making changes, create a pull request targeting the default branch and
wait for this PR to be merged before proceeding with the release.

## Transition JIRA issues and version

All issues associated with the release version should be in the "Closed" state
and have a resolution of "Fixed". Issues with other resolutions (e.g.
"Duplicate", "Works as Designed") should be removed from the release version so
that they do not appear in the release notes.

Check the corresponding ".x" fix version to see if it contains any issues that
are resolved as "Fixed" and should be included in this release version.

Update the version's release date and status from the
[Manage Versions](https://jira.mongodb.org/plugins/servlet/project-config/PHPLIB/versions)
page.

## Trigger the release workflow

Releases are done automatically through a GitHub Action. Visit the corresponding
[Release New Version](https://github.com/mongodb/mongo-php-library/actions/workflows/release.yml)
workflow page to trigger a new build. Select the correct branch (e.g. `v1.18`)
and trigger a new run using the "Run workflow" button. In the following prompt,
enter the version number and the corresponding JIRA version ID for the release.
This version ID can be obtained from a link in the "Version" column on the
[PHPLIB releases page](https://jira.mongodb.org/projects/PHPLIB?selectedItem=com.atlassian.jira.jira-projects-plugin%3Arelease-page&status=unreleased).

The automation will create and push the necessary tag, create a draft release,
and publish all required SSDLC assets. The release is created in a draft state
and can be published once the release notes have been updated. For new minor
versions, the release automation will also create a new branch for this release
version.

Pre-releases (alpha, beta and RC stability) can be released using the automation
as well. GitHub Releases for pre-release versions will be marked as such and
will not be marked as "latest" release. Examples for valid pre-release versions
include:
* `1.20.0-alpha1`
* `1.20.0-beta2`
* `1.20.0-rc1`

## Publish release notes

The GitHub release notes are created as a draft, and without any release
highlights. Fill in release highlights and publish release notes.
