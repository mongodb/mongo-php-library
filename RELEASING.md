# Releasing

The following steps outline the release process for both new minor versions (e.g.
releasing the `master` branch as X.Y.0) and patch versions (e.g. releasing the
`vX.Y` branch as X.Y.Z).

The command examples below assume that the canonical "mongodb" repository has
the remote name "mongodb". You may need to adjust these commands if you've given
the remote another name (e.g. "upstream"). The "origin" remote name was not used
as it likely refers to your personal fork.

It helps to keep your own fork in sync with the "mongodb" repository (i.e. any
branches and tags on the main repository should also exist in your fork). This
is left as an exercise to the reader.

## Ensure PHP version compatibility

Ensure that the library test suite completes on supported versions of PHP.

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

## Update version info

The PHP library uses [semantic versioning](https://semver.org/). Do not break
backwards compatibility in a non-major release or your users will kill you.

Before proceeding, ensure that the `master` branch is up-to-date with all code
changes in this maintenance branch. This is important because we will later
merge the ensuing release commits up to master with `--strategy=ours`, which
will ignore changes from the merged commits.

## Update extension requirement

In `composer.json`, ensure that the version of `ext-mongodb` is correct for
the library version being released.

## Update CI matrices

This is especially important before releasing a new minor version.

If this is the first release of a minor version for the library, it is likely
following an extension release. The `vars` for calling `compile extension` from
`build-extension.yml` in the Evergreen configuration must be updated:

 * The `stable` task should specify no vars.
 * The `lowest` task should specify `EXTENSION_VERSION` with the version that
   was just released.
 * The `next-stable` task should specify `EXTENSION_BRANCH` with the branch that
   was just created.
 * The `next-minor` task should specify `EXTENSION_BRANCH: master`.

The `DRIVER_VERSION` environment variable for any GitHub Actions should also be
set to `stable`.

After regenerating the Evergreen configuration, stage any modified files,
commit, and push:

```console
$ git commit -m "Update composer.json and CI matrices for X.Y.Z"
$ git push mongodb
```

## Tag the release

Create a tag for the release and push:

```console
$ git tag -a -m "Release X.Y.Z" X.Y.Z
$ git push mongodb --tags
```

## Branch management

### Creating a maintenance branch and updating master branch alias

After releasing a new major or minor version (e.g. 1.9.0), a maintenance branch
(e.g. v1.9) should be created. Any development towards a patch release (e.g.
1.9.1) would then be done within that branch and any development for the next
major or minor release can continue in master.

When work begins on a major new version, create a maintenance branch for the
last minor version and update the `extra.branch-alias.dev-master` field
in the master branch's `composer.json` file. For example, after branching v1.99,
`composer.json` in the master branch may still read:

```
"branch-alias": {
    "dev-master": "1.x-dev"
}
```

The above would be changed to:

```
"branch-alias": {
    "dev-master": "2.x-dev"
}
```

Commit this change:

```console
$ git commit -m "Master is now 1.10-dev" composer.json
```

### After releasing a new minor version

After a new minor version is released (i.e. `master` was tagged), a maintenance
branch should be created for future patch releases:

```console
$ git checkout -b vX.Y
$ git push mongodb vX.Y
```

### After releasing a patch version

If this was a patch release, the maintenance branch must be merged up to master:

```console
$ git checkout master
$ git pull mongodb master
$ git merge vX.Y --strategy=ours
$ git push mongodb
```

The `--strategy=ours` option ensures that all changes from the merged commits
will be ignored. This is OK because we previously ensured that the `master`
branch was up-to-date with all code changes in this maintenance branch before
tagging.


## Publish release notes

The following template should be used for creating GitHub release notes via
[this form](https://github.com/mongodb/mongo-php-library/releases/new).

```markdown
The PHP team is happy to announce that version X.Y.Z of the MongoDB PHP library is now available.

**Release Highlights**

<one or more paragraphs describing important changes in this release>

A complete list of resolved issues in this release may be found in [JIRA]($JIRA_URL).

**Documentation**

Documentation for this library may be found in the [PHP Library Manual](https://mongodb.com/docs/php-library/current/).

**Installation**

This library may be installed or upgraded with:

    composer require mongodb/mongodb:X.Y.Z

Installation instructions for the `mongodb` extension may be found in the [PHP.net documentation](https://php.net/manual/en/mongodb.installation.php).
```

The URL for the list of resolved JIRA issues will need to be updated with each
release. You may obtain the list from
[this form](https://jira.mongodb.org/secure/ReleaseNote.jspa?projectId=12483).

If commits from community contributors were included in this release, append the
following section:

```markdown
**Thanks**

Thanks for our community contributors for this release:

 * [$CONTRIBUTOR_NAME](https://github.com/$GITHUB_USERNAME)
```

Release announcements should also be posted in the [MongoDB Product & Driver Announcements: Driver Releases](https://mongodb.com/community/forums/tags/c/announcements/driver-releases/110/php) forum and shared on Twitter.

## Documentation Updates for New Major and Minor Versions

New major and minor releases will also require documentation updates to other
projects:

 * Create a DOCSP ticket to add the new version to PHP's server and language
   [compatibility tables](https://mongodb.com/docs/drivers/php/#compatibility)
   in the driver docs portal. See
   [mongodb/docs-ecosystem#642](https://github.com/mongodb/docs-ecosystem/pull/642)
   for an example.

 * Create a DOCSP ticket to update the "current" and "upcoming" navigation links
   in the library's [documentation](https://mongodb.com/docs/php-library/current). This
   will require updating
   [mongodb/docs-php-library](https://github.com/mongodb/docs-php-library).

These tasks can be initiated prior to tagging a new release to ensure that the
updated content becomes accessible soon after the release is published.
