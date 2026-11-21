#!/usr/bin/env bash
set -euo pipefail

# Usage: review-spec-pr.sh <PR_NUMBER> [--link-pr]
#
# Deterministic gathering for reviewing a tests/specifications submodule bump PR.
#
# Without a flag it prints the old/new submodule SHA, the commits between them with
# their changed files classified, any DRIVERS-XXXX tickets referenced, the PHPLIB/PHPC
# ticket(s) that split from each of them (via the jira CLI when available), and the
# PR's CI status. It writes nothing to Jira or GitHub.
#
# With --link-pr it writes to Jira: it adds the PR URL as a remote web link on every
# PHPLIB/PHPC split ticket, instead of commenting on those tickets. Ask the operator
# before using this mode.
#
# Both modes may initialize the tests/specifications submodule in the local checkout,
# because the commits cannot be listed without it.

LINK_PR=0
case "${2:-}" in
    '') ;;
    --link-pr) LINK_PR=1 ;;
    *)
        echo "Unknown argument: ${2}" >&2
        echo "Usage: $0 <PR_NUMBER> [--link-pr]" >&2
        exit 1
        ;;
esac

if [ $# -lt 1 ] || [ $# -gt 2 ]; then
    echo "Usage: $0 <PR_NUMBER> [--link-pr]" >&2
    exit 1
fi

PR_NUMBER="$1"
if ! [[ "$PR_NUMBER" =~ ^[0-9]+$ ]]; then
    echo "PR_NUMBER must be numeric, got: $PR_NUMBER" >&2
    exit 1
fi

REPO="mongodb/mongo-php-library"
SUBMODULE_PATH="tests/specifications"
UPSTREAM_REPO="mongodb/specifications"
PR_URL="https://github.com/$REPO/pull/$PR_NUMBER"

DIFF=$(gh pr diff "$PR_NUMBER" --repo "$REPO")

# Scope to the diff hunk for this submodule only (a PR can touch several submodules,
# e.g. generator/mql-specifications, tests/drivers-evergreen-tools).
SUBMODULE_DIFF=$(echo "$DIFF" | awk -v path="$SUBMODULE_PATH" '
    /^diff --git/ { in_hunk = ($0 ~ ("b/" path "$")) }
    in_hunk { print }
')

OLD_SHA=$(echo "$SUBMODULE_DIFF" | grep -m1 -- "-Subproject commit" | awk '{print $3}' || true)
NEW_SHA=$(echo "$SUBMODULE_DIFF" | grep -m1 -- "+Subproject commit" | awk '{print $3}' || true)

if [ -z "$OLD_SHA" ] || [ -z "$NEW_SHA" ]; then
    echo "Could not find a $SUBMODULE_PATH submodule pointer change in PR #$PR_NUMBER" >&2
    exit 1
fi

echo "$SUBMODULE_PATH: $OLD_SHA -> $NEW_SHA"
echo "Upstream compare: https://github.com/$UPSTREAM_REPO/compare/$OLD_SHA...$NEW_SHA"
echo

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../../../.." && pwd)"

SUBMODULE_DIR="$REPO_ROOT/$SUBMODULE_PATH"

# Test for the submodule's own .git rather than asking git about the directory: for an
# uninitialized submodule, git -C <dir> walks up and reports the superproject, so any
# check based on rev-parse says "yes, this is a work tree" and the SHAs below would be
# looked up in the wrong repository.
if [ ! -e "$SUBMODULE_DIR/.git" ]; then
    echo "$SUBMODULE_PATH is not initialized locally. Initializing it, this clones the submodule."
    if ! git -C "$REPO_ROOT" submodule update --init "$SUBMODULE_PATH" || [ ! -e "$SUBMODULE_DIR/.git" ]; then
        echo "Could not initialize $SUBMODULE_PATH, so the commits cannot be listed locally." >&2
        echo "Compare them at https://github.com/$UPSTREAM_REPO/compare/$OLD_SHA...$NEW_SHA" >&2
        exit 1
    fi
    echo
fi

# Make sure both endpoints are available locally, not just the new one: the submodule
# checkout may be missing the old object. When the superproject is a shallow clone,
# which is what actions/checkout produces by default, the submodule is cloned shallow
# too and holds a single commit, and a plain fetch does not unshallow it.
missing_endpoint() {
    ! git -C "$REPO_ROOT/$SUBMODULE_PATH" cat-file -e "$1" 2>/dev/null
}

if missing_endpoint "$OLD_SHA" || missing_endpoint "$NEW_SHA"; then
    if [ "$(git -C "$REPO_ROOT/$SUBMODULE_PATH" rev-parse --is-shallow-repository)" = "true" ]; then
        git -C "$REPO_ROOT/$SUBMODULE_PATH" fetch --unshallow --quiet
    else
        git -C "$REPO_ROOT/$SUBMODULE_PATH" fetch --all --quiet
    fi
fi

if missing_endpoint "$OLD_SHA" || missing_endpoint "$NEW_SHA"; then
    echo "Could not fetch the commits between $OLD_SHA and $NEW_SHA in the submodule." >&2
    echo "Compare them at https://github.com/$UPSTREAM_REPO/compare/$OLD_SHA...$NEW_SHA" >&2
    exit 1
fi

COMMITS=$(git -C "$REPO_ROOT/$SUBMODULE_PATH" log --oneline "$OLD_SHA..$NEW_SHA")

# Split tickets declare their parent in the description ("split from {{DRIVERS-XXXX}}"),
# so a text search on the DRIVERS key finds them.
split_ticket_jql() {
    printf 'project in (PHPLIB, PHPC) AND text ~ "%s"' "$1"
}

# jira-cli exits non-zero and prints "No result found" when a query matches nothing.
# An empty result must not be read as a failure, and a failure must not be read as
# "no ticket exists": that mistake leads to creating a duplicate ticket.
#
# Sets SPLIT_OUTPUT and returns 0 when the query ran, 1 when nothing matched, 2 when
# the query itself failed.
query_split_tickets() {
    if SPLIT_OUTPUT=$(jira issue list --jql "$(split_ticket_jql "$1")" --plain --no-headers 2>&1); then
        SPLIT_OUTPUT=$(printf '%s\n' "$SPLIT_OUTPUT" | sed $'s/\033\\[[0-9;]*m//g')
        return 0
    fi
    SPLIT_OUTPUT=$(printf '%s\n' "$SPLIT_OUTPUT" | sed $'s/\033\\[[0-9;]*m//g')
    if printf '%s' "$SPLIT_OUTPUT" | grep -q 'No result found'; then
        return 1
    fi
    return 2
}

jira_search_url() {
    local ticket="$1"
    printf 'https://jira.mongodb.org/issues/?jql=%s' \
        "project%20in%20(PHPLIB%2C%20PHPC)%20AND%20text%20~%20%22$ticket%22"
}

JIRA_USABLE=0
JIRA_ERR=''
if command -v jira >/dev/null 2>&1; then
    if ! JIRA_ERR=$(jira me 2>&1 >/dev/null); then
        # Strip ANSI colour codes and keep the first non-empty line of the error.
        JIRA_ERR=$(printf '%s\n' "$JIRA_ERR" | sed $'s/\033\\[[0-9;]*m//g' | awk 'NF { print; exit }')
    else
        JIRA_USABLE=1
    fi
fi

DRIVERS_TICKETS=$(echo "$COMMITS" | grep -oE 'DRIVERS-[0-9]+' | sort -u -t- -k2 -n || true)

if [ "$LINK_PR" -eq 1 ]; then
    if [ "$JIRA_USABLE" -ne 1 ]; then
        echo "The jira CLI is required to link the PR on the split tickets, and it is not usable." >&2
        echo "Reported error: ${JIRA_ERR:-not installed}" >&2
        exit 1
    fi

    if [ -z "$DRIVERS_TICKETS" ]; then
        echo "No DRIVERS-XXXX ticket referenced by this bump, so there is no split ticket to link." >&2
        exit 0
    fi

    # Collect the split tickets. Report lookups that failed, so a broken lookup is
    # never mistaken for "no split ticket exists".
    LINK_KEYS=''
    LOOKUP_FAILURES=0
    while IFS= read -r ticket; do
        [ -n "$ticket" ] || continue
        STATUS=0
        query_split_tickets "$ticket" || STATUS=$?
        case "$STATUS" in
            0)
                KEYS=$(printf '%s\n' "$SPLIT_OUTPUT" | grep -oE '(PHPLIB|PHPC)-[0-9]+' || true)
                LINK_KEYS="$LINK_KEYS$KEYS"$'\n'
                ;;
            1) ;;
            *)
                printf 'Split-ticket lookup failed for %s:\n%s\n' "$ticket" "$SPLIT_OUTPUT" >&2
                LOOKUP_FAILURES=$((LOOKUP_FAILURES + 1))
                ;;
        esac
    done < <(printf '%s\n' "$DRIVERS_TICKETS")

    if [ "$LOOKUP_FAILURES" -gt 0 ]; then
        echo "Aborting before linking anything: $LOOKUP_FAILURES lookup(s) failed." >&2
        exit 1
    fi

    UNIQUE_KEYS=$(printf '%s\n' "$LINK_KEYS" | sed '/^$/d' | sort -u -t- -k2 -n)
    if [ -z "$UNIQUE_KEYS" ]; then
        echo "No PHPLIB/PHPC split ticket found for this bump, so there is nothing to link."
        exit 0
    fi

    # Jira renders backticks literally in a link title, and Dependabot puts them around
    # the SHAs in the PR title.
    PR_TITLE=$(gh pr view "$PR_NUMBER" --repo "$REPO" --json title --jq .title | tr -d '`')

    echo "Linking $PR_URL on the split tickets:"
    LINK_FAILURES=0
    while IFS= read -r key; do
        [ -n "$key" ] || continue
        if LINK_ERR=$(jira issue link remote "$key" "$PR_URL" "$PR_TITLE" 2>&1); then
            printf '  %s -> linked\n' "$key"
        else
            # Strip ANSI colour codes and keep the first non-empty line of the error.
            LINK_ERR=$(printf '%s\n' "$LINK_ERR" | sed $'s/\033\\[[0-9;]*m//g' | awk 'NF { print; exit }')
            printf '  %s -> link failed: %s\n' "$key" "$LINK_ERR" >&2
            LINK_FAILURES=$((LINK_FAILURES + 1))
        fi
    done < <(printf '%s\n' "$UNIQUE_KEYS")

    echo
    if [ "$LINK_FAILURES" -gt 0 ]; then
        echo "$LINK_FAILURES link(s) failed." >&2
        exit 1
    fi
    echo "Re-running this mode adds the same link again. Jira only removes remote links from the"
    echo "issue UI, so avoid re-running it and check the ticket before you do."
    exit 0
fi

# Classify a changed file into a category label, given its git status letter (A/M/D/...).
classify_file() {
    local status="$1" path="$2" kind action

    case "$path" in
        */tests/*.yml | */tests/*.yaml | */tests/*.json)
            kind="spec test"
            ;;
        */tests/*.md)
            kind="prose test"
            ;;
        *.md)
            kind="spec"
            ;;
        *)
            kind="other file"
            ;;
    esac

    case "$status" in
        A) action="added" ;;
        D) action="deleted" ;;
        *) action="updated" ;;
    esac

    echo "$kind $action"
}

if [ -z "$COMMITS" ]; then
    echo "No commits between $OLD_SHA and $NEW_SHA. The submodule pointer may have moved"
    echo "backwards, or the range may be empty for another reason. Check the compare URL above."
    echo
else
    echo "$COMMITS" | while IFS= read -r line; do
        SHA=$(echo "$line" | cut -d' ' -f1)
        echo "$line"
        git -C "$REPO_ROOT/$SUBMODULE_PATH" show --name-status --pretty=format: "$SHA" | sed '/^$/d' | while IFS=$'\t' read -r status path new_path; do
            # Renames report as "R100 old/path new/path"; keep the new path and treat as an update.
            if [ -n "$new_path" ]; then
                status=M
                path="$new_path"
            fi
            label=$(classify_file "${status:0:1}" "$path")
            printf '    [%s] %s\n' "$label" "$path"
        done
        echo
    done
fi

echo "DRIVERS tickets referenced:"
if [ -n "$DRIVERS_TICKETS" ]; then
    echo "$DRIVERS_TICKETS"
else
    echo "(none found)"
fi
echo

if [ -n "$DRIVERS_TICKETS" ] && [ "$JIRA_USABLE" -eq 1 ]; then
    echo "PHPLIB/PHPC split tickets:"
    while IFS= read -r ticket; do
        [ -n "$ticket" ] || continue
        STATUS=0
        query_split_tickets "$ticket" || STATUS=$?
        case "$STATUS" in
            0)
                echo "  $ticket ->"
                printf '%s\n' "$SPLIT_OUTPUT" | sed 's/^/    /'
                ;;
            1)
                echo "  $ticket -> (no split ticket found)"
                ;;
            *)
                echo "  $ticket -> (jira lookup failed, do not treat this as 'no ticket exists')"
                printf '%s\n' "$SPLIT_OUTPUT" | sed 's/^/    /'
                ;;
        esac
    done < <(printf '%s\n' "$DRIVERS_TICKETS")
    echo
elif [ -n "$DRIVERS_TICKETS" ]; then
    if [ "$JIRA_USABLE" -ne 1 ]; then
        echo "The jira CLI is installed but not usable, so the split-ticket lookup is skipped."
        echo "Reported error: ${JIRA_ERR:-not installed}"
    else
        echo "The jira CLI was not found, so the split-ticket lookup is skipped."
    fi
    echo "PHPLIB/PHPC split tickets: look each of these up in Jira, or open:"
    while IFS= read -r ticket; do
        [ -n "$ticket" ] || continue
        printf '  %s -> %s\n' "$ticket" "$(jira_search_url "$ticket")"
    done < <(printf '%s\n' "$DRIVERS_TICKETS")
    echo
fi

CHECKS=$(gh pr checks "$PR_NUMBER" --repo "$REPO" || true)

echo "CI status:"
if [ -z "$CHECKS" ]; then
    echo "  (no checks reported)"
else
    # gh pr checks prints tab-separated columns outside a TTY: name, status, elapsed, url.
    NOT_PASSING=$(echo "$CHECKS" | awk -F'\t' '$2 != "pass" && $2 != "skipping"')
    if [ -n "$NOT_PASSING" ]; then
        echo "  Not passing:"
        echo "$NOT_PASSING" | sed 's/^/    /'
    else
        echo "  All checks passed."
    fi
    echo
    echo "All checks:"
    echo "$CHECKS"
fi
