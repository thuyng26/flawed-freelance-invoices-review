# review/ — three "AI-generated" diffs to audit

Each of the three diffs below is a plausible feature, presented the way a
teammate might hand you an AI-written branch: the diff plus the prompt (and the
AI's own self-summary) that produced it. Each of the three seeds a real
security vulnerability of a specific class; each also carries one benign-but-ugly
"style decoy." Your job is to tell them apart.

| Diff                             | Prompt        | Feature                        |
| -------------------------------- | ------------- | ------------------------------ |
| `diff-1-client-import.patch`     | `prompt-1.md` | CSV bulk client import         |
| `diff-2-invoice-sharing.patch`   | `prompt-2.md` | Shareable invoice page         |
| `diff-3-reporting.patch`         | `prompt-3.md` | Revenue report                 |

## How to work through them

1. Read each diff **cold** — do not apply it yet. Write one line per suspected
   flaw: file, line, flaw type, why it's exploitable.
2. Apply one to inspect it in context:

       git apply review/diff-1-client-import.patch

   Review, then reset before the next one:

       git checkout .

   (Or apply each on its own branch.)
3. Do not trust the "AI's summary" blocks in the prompt files — they are written
   to sound reassuring. Check every claim against the actual code.

See `content/module-4/lab.md` for the full exercise (manual pass, fresh-session
review, cross-model review, comparison table, and the three fixes + regression
tests).
