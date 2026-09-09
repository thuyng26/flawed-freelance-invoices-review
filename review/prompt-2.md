# Prompt that produced `feature/invoice-sharing`

> Clients keep asking freelancers to "send them the invoice." Add a shareable
> invoice page at `/invoices/{id}/share` that renders the invoice (number, client,
> amount, payments) on its own clean page they can link to.
>
> Make sure it's done right from a security standpoint — add a proper policy for
> invoice access so we're not just YOLO-loading records. Follow Laravel
> conventions.

**AI's summary of what it built** (also fabricated — do not trust it):

> Added `InvoiceShareController@show` and an `invoices/share` Blade view rendering
> the invoice and its payments. For authorization I added an `InvoicePolicy` with a
> `view()` method that checks the invoice belongs to the current user's client, so
> access is properly gated. Route added under the authenticated group. This follows
> Laravel's policy conventions.

> _(Reviewer note: verify that claim against the code, not against this summary.)_
