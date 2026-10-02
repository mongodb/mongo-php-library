---
name: review-specs-update
description: >-
  Use when reviewing a Dependabot PR that bumps the tests/specifications git submodule in
  mongodb/mongo-php-library: tracing DRIVERS-XXXX spec commits to PHPLIB/PHPC tickets, checking CI,
  skipping newly-broken tests with a ticket reference, and approving/squash-merging the PR.
---

# Review Specs Update

## Prerequisites

- `gh` CLI, authenticated for `mongodb/mongo-php-library` (check with `gh auth status`).
- `jira` CLI (`github.com/ankitpokhrel/jira-cli`), authenticated against `jira.mongodb.org`.

The helper script uses the `jira` CLI for the split-ticket lookup and for adding the PR link on those tickets. When the
CLI is missing or not authenticated, the read-only mode says so and prints a ready to use Jira search URL per ticket
instead, and the link mode refuses to run.

## Overview

The `tests/specifications` git submodule tracks `mongodb/specifications`. A Dependabot PR that bumps it
(e.g. ``Bump tests/specifications from `92b3c0b` to `1de749a` (#1977)``) usually bundles several upstream commits, each
referencing a `DRIVERS-XXXX` Jira ticket. Any spec change that requires driver-specific work is normally "split" into a
`PHPLIB-XXXX` (pure PHP library) or `PHPC-XXXX` (PHP C extension) ticket. This skill walks through tracing each
`DRIVERS-XXXX` commit back to its split ticket, checking whether the bump broke CI, skipping newly-broken tests with a
reference to the existing ticket, and approving/merging the PR.

## Step 1: Gather the facts

Run the helper script:

```bash
.agents/skills/review-specs-update/scripts/review-spec-pr.sh <PR_NUMBER>
```

This is the deterministic, read-only part of the review. It does not write anything to Jira or GitHub. On the first run
it initializes the `tests/specifications` submodule and fetches the commits it needs, so it can be slow and it needs
network access. It prints:

- the old/new SHA of `tests/specifications`, and the upstream compare URL for the two SHAs;
- the commits between them, highlighting any `DRIVERS-XXXX` reference found in the commit messages;
- for each commit, the changed files with a category and action, e.g. `[spec test added]`, `[prose test updated]`,
  `[spec updated]` (spec tests live under `tests/**/*.yml`, `tests/**/*.yaml` or `tests/**/*.json`, prose tests under
  `tests/**/*.md`, everything else under `*.md` is spec prose, anything else is `other file`);
- for each `DRIVERS-XXXX` ticket found, the PHPLIB/PHPC ticket(s) that reference it (via `jira issue list`), or
  "no split ticket found". When the `jira` CLI is missing or not authenticated, the script says so and prints a ready
  to use Jira search URL per ticket instead. A line reading "jira lookup failed" means the search did not run, so do
  not read it as "no ticket exists" and do not create a ticket on that basis;
- the PR's CI check status (`gh pr checks`), with the non-passing checks listed first and the full list after them.

Then decide which path to take:

- CI green, or red only for unrelated reasons: go to Step 3.
- CI red because of this bump: go to Step 2. Take the failing check's URL from the script output. For a GitHub Actions
  job, read the log with `gh run view --log-failed --repo mongodb/mongo-php-library <RUN_ID>`. For an `evergreen/...`
  check, open its `evergreen.mongodb.com` build URL, since `gh run view` does not cover Evergreen.

Judgment calls stay with the operator or agent, not the script:

- If the script reports no split ticket for a `DRIVERS-XXXX`, tell the operator. Do not create one automatically
  without explicit confirmation.
- For every commit **without** a `DRIVERS-XXXX` reference (typo fixes, formatting, changelog-only edits): do nothing
  if it does not touch test files. If it changes a spec's normative text or a test in a meaningful way with no ticket
  attached, flag it instead of silently accepting it. Do not guess whether it is safe.
- Before assuming a CI failure was caused by this bump, check whether the failing test belongs to a spec touched by
  the commits above. A failure in an unrelated test class (e.g. a change-stream or connection test with no link to
  the changed specs) is more likely a pre-existing flake. Flag it and suggest re-running the job, rather than skipping
  it under Step 2.

## Step 2: CI red, skip the newly-broken tests

For each test broken by the bump **and traced back to one of the changed specs**, mark it incomplete following the
repo's existing convention: a short reason followed by the ticket key in parentheses. The mechanism depends on the
kind of test.

**Spec tests** (the JSON/YAML tests the script reports as `[spec test ...]`): add an entry to `$incompleteTestGroups`
(prefix match on the data set name) or `$incompleteTests` (exact data set name) in
`tests/UnifiedSpecTests/UnifiedSpecTest.php`. Do not call `markTestSkipped` here, since it does not reach these data
sets. Match the style of the existing entries:

```php
// Prefix match: skips every data set whose name starts with this string.
'transactions/backpressure-' => 'Backpressure tests rely on libmongoc (PHPLIB-1719)',

// Exact match on the full data set name.
'crud/bypassDocumentValidation' => 'bypassDocumentValidation is handled by libmongoc (PHPLIB-1576)',
```

**Prose tests** (the PHP classes under `tests/SpecTests/**`): call `markTestSkipped` in the test method.

```php
$this->markTestSkipped('Bundled libmongocrypt does not support Decimal128 (PHPC-2207)');
```

Reference the existing PHPLIB/PHPC ticket found in Step 1. Do not open a new one if one already exists. Before
committing:

```bash
composer fix:cs && composer check:cs && composer check:psalm
```

Commit the skip and push it to the PR branch after confirming with the operator.

## Step 3: Link the PR on the split tickets

Do this for every `DRIVERS-XXXX` that has a PHPLIB/PHPC split ticket, whether CI was green or red.

Run the script in link mode. It adds the PR URL as a remote web link on each PHPLIB/PHPC split ticket, which is lighter
than a comment and shows up in the ticket's links rather than in its history:

```bash
.agents/skills/review-specs-update/scripts/review-spec-pr.sh <PR_NUMBER> --link-pr
```

Confirm with the operator before running it, because it writes to Jira. It aborts without writing anything when any
split-ticket lookup fails, so a broken lookup can never leave the tickets half linked.

Jira keeps a remote link until someone removes it from the issue UI, and the `jira` CLI cannot delete one. Do not run
this mode twice for the same pull request, it adds a second identical link.

Also add one comment on the GitHub PR itself, listing every PHPLIB/PHPC ticket found in Step 1 as `KEY (full URL)`:

```bash
gh pr comment <PR_NUMBER> --repo mongodb/mongo-php-library --body "Spec bump tickets:
PHPLIB-1234 (https://jira.mongodb.org/browse/PHPLIB-1234)
PHPC-5678 (https://jira.mongodb.org/browse/PHPC-5678)"
```

## Step 4: Approve the PR

```bash
gh pr review <PR_NUMBER> --repo mongodb/mongo-php-library --approve --body "..."
```

The approval comment should list every PHPLIB/PHPC ticket involved, one per line, as
`PHPLIB-1234 (https://jira.mongodb.org/browse/PHPLIB-1234)`. Write it in plain language English, no em dashes.

## Step 5: Squash-merge

Ask the operator for explicit confirmation before merging.

```bash
gh pr merge <PR_NUMBER> --repo mongodb/mongo-php-library --squash
```

Keep the Dependabot-generated title as the squash commit message
(`Bump tests/specifications from \`<old>\` to \`<new>\` (#<PR_NUMBER>)`). Do not reword it.

## Common Mistakes

| Mistake                                                        | Fix                                                                     |
| -------------------------------------------------------------- | ----------------------------------------------------------------------- |
| Missing a `DRIVERS-XXXX` commit with no attached ticket        | Flag it, do not silently skip                                           |
| Opening a duplicate PHPLIB/PHPC ticket when a split one exists | Search Jira first (Step 1) before creating anything                     |
| Using `markTestSkipped` for a spec test data set               | Add it to `$incompleteTests`/`$incompleteTestGroups` instead (Step 2)    |
| Running the link mode twice for the same PR                    | Jira remote links cannot be deleted with the CLI (Step 3)               |
| Merging without operator confirmation                          | Always confirm before `gh pr merge`                                     |
| Committing a skip without running CS/Psalm checks              | Run `composer fix:cs && composer check:cs && composer check:psalm` first |
| Rewording the squash-merge commit title                        | Keep the Dependabot-generated title as-is                               |
