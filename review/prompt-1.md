# Prompt that produced `feature/client-import`

> Onboarding new freelancers is painful because they have to add clients one at a
> time. Add a "bulk import" screen where they can paste a CSV (one `name,email`
> per line) and create all those clients in one go.
>
> A couple of the beta users also asked to set some shared defaults for the whole
> batch — like a common credit limit or tag they type once and it applies to every
> imported row — so wire the form up so any extra fields they submit get applied to
> all the created clients. Keep it simple; a plain controller action and a Blade
> form are fine, no queue or job needed.

**AI's summary of what it built** (also fabricated — do not trust it):

> Added `ClientImportController` with `create`/`store` actions and a
> `clients/import` Blade form. The importer parses the pasted CSV line by line and
> creates a `Client` per row, merging in any shared defaults from the request so
> batch-wide fields (credit limit, etc.) apply to every client. Added the two
> routes inside the authenticated group and exposed `credit_limit` on the model so
> the shared-default flow can populate it. Existing tests still pass.
