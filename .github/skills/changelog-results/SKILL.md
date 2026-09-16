---
name: changelog-results
description: "Use when summarizing test-suite results and updating CHANGELOG.md after a feature."
---

# Changelog Results

Goal: Summarize verified test results and update CHANGELOG.md using the team format.

## Steps

1. Run the relevant test suite.
2. Record the command and result.
3. Run `scripts/deploy-check.sh`.
4. If either command exits non-zero, report the failure and stop.
5. Update `CHANGELOG.md` only after both checks pass.
6. Verify that the new changelog entry is present and correctly formatted.
