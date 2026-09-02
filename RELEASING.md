# Releasing

## Check that CI uses released versions of the extension

A feature may have been developed against an extension version that was not
released yet. In that case the `ext-mongodb` constraint in `composer.json` was
updated during development, and CI was configured to compile the extension from
a development branch of the `mongodb/mongo-php-driver` repository. A library
release must not ship in that state, because the extension it requires is
released first.

Two variables control this, both named `EXTENSION_DEV_BRANCH`:

 * in `.evergreen/compile-extension.sh`, for the Evergreen build tasks
 * in `.github/actions/setup/action.yml`, for the GitHub Actions workflows

Both must hold an empty value. If either one holds a branch name, reset it, and
set `EXTENSION_STABLE_BRANCH` in `.evergreen/compile-extension.sh` to the branch
of the latest released extension minor version, for example `v2.5`. The
"Continuous integration" section of [CONTRIBUTING.md](CONTRIBUTING.md) explains
how these variables are used.

If this is missed, the release workflow fails before creating the release tag.

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
workflow page to trigger a new build. Select the correct branch and trigger a
new run using the "Run workflow" button. Patch releases must be run from the
corresponding `vA.B` branch (e.g. `v1.18`). Non-patch releases (`*.0`,
including pre-releases) may be run from either `vA.B` or `vA.x`; when run from
`vA.x`, the workflow will create the new `vA.B` branch automatically.

When triggering the workflow, fill in the following fields:

* **Branch**: `vA.x` for a new minor release (e.g. `v2.x`), or `vA.B` for a
  new patch release (e.g. `v2.2`)
* **Version**: the version to be released (e.g. `2.3.0` or `2.2.2`)
* **Jira version**: the version ID obtained from a link in the "Version" column
  on the [PHPLIB releases page](https://jira.mongodb.org/projects/PHPLIB?selectedItem=com.atlassian.jira.jira-projects-plugin%3Arelease-page&status=unreleased)

The automation will create and push the necessary tag, create a draft release,
and publish all required SSDLC assets. Wait for all SSDLC assets to be uploaded
before publishing the release. For new minor versions, the release automation
will also create a new branch for this release version.

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
