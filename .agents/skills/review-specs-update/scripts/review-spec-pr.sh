#!/usr/bin/env bash
set -euo pipefail

# Usage: review-spec-pr.sh <PR_NUMBER>
#
# Deterministic, read-only gathering for reviewing a tests/specifications submodule
# bump PR: prints the old/new submodule SHA, the commits between them with their
# changed files classified, any DRIVERS-XXXX tickets referenced, the PHPLIB/PHPC
# ticket(s) that split from each of them (via the jira CLI), and the PR's CI status.
#
# Does not write anything to Jira or GitHub.

if [ $# -ne 1 ]; then
    echo "Usage: $0 <PR_NUMBER>" >&2
    exit 1
fi

PR_NUMBER="$1"
REPO="mongodb/mongo-php-library"
SUBMODULE_PATH="tests/specifications"
UPSTREAM_REPO="mongodb/specifications"

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

echo "tests/specifications: $OLD_SHA -> $NEW_SHA"
echo

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../../../.." && pwd)"

if git -C "$REPO_ROOT/$SUBMODULE_PATH" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    if ! git -C "$REPO_ROOT/$SUBMODULE_PATH" cat-file -e "$OLD_SHA" 2>/dev/null \
        || ! git -C "$REPO_ROOT/$SUBMODULE_PATH" cat-file -e "$NEW_SHA" 2>/dev/null; then
        git -C "$REPO_ROOT/$SUBMODULE_PATH" fetch --all --quiet
    fi
    COMMITS=$(git -C "$REPO_ROOT/$SUBMODULE_PATH" log --oneline "$OLD_SHA..$NEW_SHA")

    # Classify a changed file into a category label, given its git status letter (A/M/D/...).
    classify_file() {
        local status="$1" path="$2" kind action

        case "$path" in
            */tests/*.yml | */tests/*.json)
                kind="unified test"
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
    echo "DRIVERS tickets referenced:"
    DRIVERS_TICKETS=$(echo "$COMMITS" | grep -oE 'DRIVERS-[0-9]+' | sort -u -t- -k2 -n || true)
    if [ -n "$DRIVERS_TICKETS" ]; then
        echo "$DRIVERS_TICKETS"
    else
        echo "(none found)"
    fi
    echo

    if [ -n "$DRIVERS_TICKETS" ] && command -v jira >/dev/null 2>&1; then
        echo "PHPLIB/PHPC split tickets:"
        echo "$DRIVERS_TICKETS" | while IFS= read -r ticket; do
            SPLIT=$(jira issue list --jql "project in (PHPLIB, PHPC) AND text ~ \"$ticket\"" --plain --no-headers 2>/dev/null || true)
            if [ -n "$SPLIT" ]; then
                echo "  $ticket ->"
                echo "$SPLIT" | sed 's/^/    /'
            else
                echo "  $ticket -> (no split ticket found)"
            fi
        done
        echo
    fi
else
    echo "$SUBMODULE_PATH is not initialized locally." >&2
    echo "Run 'git submodule update --init $SUBMODULE_PATH' to list commits locally, or compare manually at:" >&2
    echo "https://github.com/$UPSTREAM_REPO/compare/$OLD_SHA...$NEW_SHA" >&2
    exit 0
fi

echo "CI status:"
gh pr checks "$PR_NUMBER" --repo "$REPO" || true
