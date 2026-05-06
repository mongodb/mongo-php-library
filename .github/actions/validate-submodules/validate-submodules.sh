#!/usr/bin/env bash
# Validates that each submodule commit:
#   1. Is on the upstream branch configured in .gitmodules
#   2. Does not regress from the target branch of the PR (BASE_REF)
#
# Required env vars:
#   REPO      - GitHub repository (e.g. "mongodb/mongo-php-library")
#   PR_SHA    - Head commit SHA of the PR
#   BASE_REF  - Target branch name (e.g. "v2.x")
set -eo pipefail

errors=0

# Fetch .gitmodules from the PR head via API
tmpfile=$(mktemp)
trap 'rm -f "$tmpfile"' EXIT
gh api "repos/$REPO/contents/.gitmodules?ref=$PR_SHA" --jq '.content' | base64 -d > "$tmpfile"

while IFS=" " read -r key path; do
  name="${key#submodule.}"
  name="${name%.path}"

  url=$(git config -f "$tmpfile" "submodule.$name.url")
  subrepo=$(echo "$url" | sed 's|https://github.com/||;s|\.git$||')
  branch=$(git config -f "$tmpfile" "submodule.$name.branch" 2>/dev/null \
    || gh api "repos/$subrepo" --jq '.default_branch')

  pr_sha=$(gh api "repos/$REPO/contents/$path?ref=$PR_SHA" --jq '.sha')
  base_sha=$(gh api "repos/$REPO/contents/$path?ref=$BASE_REF" --jq '.sha' 2>/dev/null || true)

  echo "::group::Checking submodule: $name"
  printf "  Repo:        %s\n" "$subrepo"
  printf "  PR commit:   %s\n" "$pr_sha"
  printf "  Base commit: %s\n" "${base_sha:-unknown}"

  # Check 1: pr_sha is on the upstream branch
  # "behind" or "identical" means pr_sha is an ancestor of the branch HEAD
  status=$(gh api "repos/$subrepo/compare/$branch...$pr_sha?per_page=1" --jq '.status' 2>/dev/null || echo "error")
  if [ "$status" = "behind" ] || [ "$status" = "identical" ]; then
    echo "  ✓ Commit is on the upstream branch"
  else
    echo "::error::Submodule '$name': commit $pr_sha is not on branch '$branch' (status: $status)"
    errors=$((errors + 1))
  fi

  # Check 2: pr_sha must not be older than the target branch's submodule commit
  # "ahead" or "identical" means pr_sha is a descendant of base_sha
  if [ -n "$base_sha" ] && [ "$base_sha" != "$pr_sha" ]; then
    status=$(gh api "repos/$subrepo/compare/$base_sha...$pr_sha?per_page=1" --jq '.status' 2>/dev/null || echo "error")
    if [ "$status" = "ahead" ] || [ "$status" = "identical" ]; then
      printf "  ✓ Moves forward from target branch (%s → %s)\n" "${base_sha:0:8}" "${pr_sha:0:8}"
    else
      echo "::error::Submodule '$name' regresses: target branch has $base_sha but PR has $pr_sha (status: $status)"
      errors=$((errors + 1))
    fi
  fi

  echo "::endgroup::"
done < <(git config -f "$tmpfile" --get-regexp 'submodule\..*\.path')

if [ $errors -gt 0 ]; then
  echo "::error::$errors submodule check(s) failed"
  exit 1
fi

echo "All submodule checks passed"
