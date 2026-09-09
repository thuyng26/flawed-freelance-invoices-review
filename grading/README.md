# Course grading suite

This directory is owned by the course, not by the lab. It contains the
weight-aware autograding checks that CI runs after each push (see
`.github/workflows/grade.yml`):

- `tests/ModuleN/` — Pest checks for module N, run as
  `vendor/bin/pest grading/tests/ModuleN`.
- `weights.json` — check names mapped to rubric weights (each module sums
  to 100) with CORE/GATE/SOFT flags.
- `score.py` — parses the junit output, sums the weights of passing checks,
  and posts the signed score to the course site.

Your own tests live in `tests/` — the grading suite runs them as a gate but
never counts them as evidence of completion. Editing the files in here only
changes the number your own repo reports about itself; the course treats
self-reported grades accordingly, and admin review reads the real diff.
