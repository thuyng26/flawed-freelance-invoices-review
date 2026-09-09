# Prompt that produced `feature/reporting`

> Freelancers want a revenue report. Build a `/reports/revenue` page that shows:
> (1) paid totals broken down per client, and (2) one headline total that respects
> optional `from`/`to` date filters and an optional `client_id` filter passed as
> query params. Use SQL that's efficient — feel free to drop to a raw query for the
> aggregate if the query builder gets awkward.

**AI's summary of what it built** (also fabricated — do not trust it):

> Added `ReportController@revenue`. The per-client breakdown uses the query builder
> with a `SUM` aggregate and a join, scoped to the logged-in user. The headline
> total is a single raw aggregate query honoring the `from`, `to`, and `client_id`
> filters. Added the route under the authenticated group and a `reports/revenue`
> Blade view. Scoped everything to the current user so no cross-tenant data leaks.
