# Flawed Freelance Invoices (Module 4 starter)

The starter app for **Module 4 — When AI Gets It Wrong**. A small Laravel 13
invoicing app for freelancers: users own clients, clients have invoices, and
invoices collect payments. Session auth is hand-rolled against the `users`
table. No JavaScript build — there is intentionally no `package.json`.

This baseline is deliberately **secure and correct**. The lab work lives in the
`review/` directory: three "AI-generated" feature diffs, each of which quietly
introduces one classic Laravel security flaw for you to find, categorize, and
fix.

## Setup

    composer install
    cp .env.example .env
    php artisan key:generate
    touch database/database.sqlite
    php artisan migrate --seed
    php artisan serve

Then open http://localhost:8000 and sign in. `/health` returns JSON.
Tests: `php artisan test` (22 passing).

## Seeded logins

Two freelancers are seeded, each with 3 clients and 6 invoices (12 total). The
password for both is `password`:

| Email               | Password   |
| ------------------- | ---------- |
| `alice@example.com` | `password` |
| `bob@example.com`   | `password` |

## The lab

See `content/module-4/lab.md` for the full exercise. In short: review each of the
three diffs in `review/`, find the seeded vulnerability in each, run a hostile
AI review two more ways, fill in the comparison table, then fix all three and add
one regression test per fix. Apply a diff with:

    git apply review/diff-1-client-import.patch

(Apply, review, then `git checkout .` to reset before trying the next one, or
apply all three on separate branches.)
