"""Weight-aware grade scorer.

Reads grading-junit.xml (produced by `pest grading/tests/Module$GRADE_MODULE
--log-junit`) plus grading/weights.json, sums the weights of passing checks,
and writes payload.json for the signed webhook POST. The score comes ONLY from
the grading suite; the learner's own suite is a gate check inside it (G4).

Debug locally with:
    GRADE_MODULE=1 ./vendor/bin/pest grading/tests/Module1 --log-junit grading-junit.xml || true
    GRADE_MODULE=1 GITHUB_REPOSITORY=x GITHUB_ACTOR=x GITHUB_SHA=x GRADE_LESSON_SLUG=lab python3 grading/score.py
"""

import json
import os
import xml.etree.ElementTree as ET

REPORT_LIMIT = 3500


def junit_results(path):
    """Map testcase name -> 'pass' | 'fail'. Missing file -> empty map."""
    results = {}
    try:
        root = ET.parse(path).getroot()
    except (OSError, ET.ParseError):
        return results
    for case in root.iter("testcase"):
        bad = any(case.find(tag) is not None for tag in ("failure", "error", "skipped"))
        results[case.get("name", "")] = "fail" if bad else "pass"
    return results


def main():
    module = os.environ["GRADE_MODULE"]
    here = os.path.dirname(os.path.abspath(__file__))
    with open(os.path.join(here, "weights.json")) as fh:
        manifest = json.load(fh)

    checks = manifest["modules"].get(module)
    results = junit_results("grading-junit.xml")

    if checks is None:
        score = 0
        report = f"No grading checks defined for module {module}. Score recorded as 0."
    else:
        total = sum(c["weight"] for c in checks)
        score = 0
        lines = []
        core_failed = []
        for check in checks:
            # MISSING (vs FAIL) flags a weights.json/testcase-name mismatch —
            # a renamed test would otherwise silently always score 0.
            status = results.get(check["name"], "MISSING")
            passed = status == "pass"
            if passed:
                score += check["weight"]
            elif check.get("core"):
                core_failed.append(check["name"])
            flags = "".join(
                f" [{flag.upper()}]" for flag in ("core", "gate", "soft") if check.get(flag)
            )
            label = "PASS" if passed else ("MISSING" if status == "MISSING" else "FAIL")
            lines.append(f"{label} ({check['weight']}){flags} {check['name']}")

        score = max(0, min(100, round(score * 100 / total))) if total else 0
        report = f"Auto-grade module {module}: {score}/100.\n" + "\n".join(lines)
        if core_failed:
            report += "\nCORE checks failing: " + "; ".join(core_failed) + "."
        if not results:
            score = 0
            report += "\nGrading suite produced no results (failed to boot?). Score recorded as 0."
        note = manifest.get("notes", {}).get(module)
        if note:
            report += "\nNote: " + note

    if len(report) > REPORT_LIMIT:
        report = report[: REPORT_LIMIT - 12] + "\n[truncated]"

    payload = {
        "repo": os.environ["GITHUB_REPOSITORY"],
        "github_username": os.environ["GITHUB_ACTOR"],
        "sha": os.environ["GITHUB_SHA"],
        "module": int(module),
        "lesson_slug": os.environ["GRADE_LESSON_SLUG"],
        "score_pct": score,
        "report": report,
    }
    with open("payload.json", "w") as fh:
        json.dump(payload, fh, separators=(",", ":"))
    print(report)


if __name__ == "__main__":
    main()
