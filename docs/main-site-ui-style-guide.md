# Main site UI style guide

How the main site pages look and are built. These are the pages organisers use
to set up competitions, events, entries, heats, results and officials.

The site is split in two, and the halves don't share styling:

| | Main site (this guide) | Digital Judge |
|---|---|---|
| Who | Organisers, at a desk | Officials, poolside on a phone |
| Where | `resources/views` (Blade + Alpine) | `resources/js` (Svelte 5 + Inertia) |
| Styles | Global classes in `resources/css/app.css` | Tailwind utilities + Svelte components |
| Guide | This file | `docs/judge-ui-style-guide.md` |

Don't use Svelte components or judge patterns on a Blade page. Don't add new
global classes for the judge app either.

Reference pages: `competition/officials.blade.php` (a list with inline
actions, and an "Add" button that opens a modal form) and `account.blade.php`.

## Layouts

- `layouts.competition` is for anything under `/comps/{comp}`. It renders the
  competition name, the date and the `.tabbed-bar` tabs. A new top-level area
  gets a tab there:
  - wrap it in the `@can('access', [$comp, '<access>'])` that matches its route
    middleware;
  - mark it `active` with `Str::startsWith(Route::currentRouteName(), 'comps.<area>')`.
- `layouts.admin` is for site admin pages under `/admin` (routes in the
  `isAdmin` middleware group). A new admin area gets a tab in its
  `.tabbed-bar`, marked `active` with
  `Str::startsWith(Route::currentRouteName(), 'admin.<area>')`. Admin pages
  can show full emails; the masking rule below is for pages any user can reach.
  Reference page: `admin/users/index.blade.php` (a paginated, searchable
  `.se-table`).
- `layouts.organisation` is for organisation pages under
  `/organisation/{organisation}` (the view gets `$org`). A new area gets a tab
  in its `.tabbed-bar`, inside the `@can('access', [$org, 'admin'])` block that
  matches the route group's middleware, marked `active` with
  `Str::startsWith(Route::currentRouteName(), 'orgs.<area>')`. Reference page:
  `organisation/officials.blade.php` (a searchable, filterable `.se-card` list
  with badges, a CSV export and a per-row action that opens a modal).
- `layouts.guest` is for account pages outside a competition.
- `layouts.core` is the shell. It loads Alpine, the `modals` state, and the
  `showSuccess()` / `showAlert()` toasts.

Pages set `@section('title')` and fill `@section('content')`.

## Building blocks

| Need | Use |
|---|---|
| Headings | Plain `h2` (page) and `h3` (section). The base styles handle size and font. |
| Button (anywhere, including inside forms) | `.se-btn`, with variants `se-btn-success`, `se-btn-danger`, `se-btn-primary`. Don't use the older `.btn` classes. |
| Grouped panel / list | `.se-card`; add `divide-y` for a list of rows, `se-card-hover` for clickable rows |
| Status labels | `.badge` + `badge-info`, `badge-success`, `badge-danger`, `badge-orange`, `badge-gray`, sized with `badge-sm` |
| Tables | `.se-table` (`se-table-thin` for dense ones) |
| Grids | `.grid-2` … `.grid-5` (they collapse on small screens) |
| Warnings | `.se-alert-box` (red), `.se-alert-box alert-info` (blue) |
| Text input | `<x-form-input id="…" title="…" />`. It shows the `@error` message for that field and refills from `old()`. |
| Field wrapper | `div.se-form-input` around a label and its input or select. Give the inputs `mb-0!` (the wrapper's `mb-4` spaces the fields) and the last field in a form `imb-0`. Don't use the older `div.form-input` on new markup. |
| Hint under a field | `<small class="info">` inside the wrapper. A bare `<small>` is red, for errors, and the wrapper's rule beats plain utilities like `text-gray-500`. |
| Select | `<x-form-select>` takes models with `id` and `name`. For a fixed list, write a `<select>` inside a `div.se-form-input`, as the officials page does. |
| Modal | `<x-s-e-modal id="…" title="…">`, opened with `modals.<id> = true` |
| "Add" tile in a grid | `<x-add-card link="…" text="…" />` |

## Behaviour

- **Changes are plain form posts.** Use `@csrf` and `@method('delete')` where
  needed. The controller redirects `back()` with either
  `->with('success', '…')`, which the core layout shows as a toast, or
  `->withErrors([...])`, which shows under the field. Use `fetch` + JSON only
  where a modal flow already does.
- **Errors that don't belong to a field** (e.g. "you can send it again in 5
  minutes") go back with `->with('alert-error', '…')`, which the core layout
  shows as an alert toast.
- **QR codes:** generate them with `simplesoftwareio/simple-qrcode` (the
  `QrCode` facade). The Digital Judge one lives on the printable
  `digitaljudge/qrs.blade.php` page (`dj.qrs`); link to that rather than
  building another.
- **Use Alpine for small client-side behaviour**, such as autocomplete,
  toggles and modals. Keep it inline in the view.
- **Picking a person:** copy the search box in the Add Official modal.
  - The search input has a magnifying-glass icon on the left (`pl-10!` on the
    input).
  - Results drop down below it. Picking one swaps the input for a card with a
    "Change" button.
  - Search by name or email on an endpoint scoped to the competition.
  - **Mask emails** (`j***@example.com`) unless the query is the full address,
    and submit the picked account's id, not its email. Any signed-in user can
    create a competition, so unmasked results would let anyone look up anyone's
    email.
  - Only show extra fields such as "Name" when they're needed: here, when an
    email was typed that no account uses.
  - Before anything's typed, the box can offer likely picks (the Add Official
    modal lists people from the organisation's other competitions). Mask their
    emails the same way.
- **Search and filter a list** with a `get` form posting back to the same
  route: a search input like the admin users page's (`q`), plus a `<select>`
  filter that submits `onchange`. Show "Showing X of Y" and a "Clear" link
  while a filter is on, and an empty state for no matches. The controller
  reads both from the query string and ignores unknown filter values.
- **CSV export:** a `se-btn` link next to the page heading to a `…/export`
  route that returns `response()->streamDownload()` with `fputcsv`. Pass the
  page's current filters through so the export matches what's on screen.
- **Open a modal for one row** (e.g. "Add to a competition" on the
  organisation Officials tab): the row's button does
  `$dispatch('<event>', {...}); modals.<id> = true`, and the modal's form
  listens with `@<event>.window`. For a failed submit, the controller passes
  the row back (from `old()`) so the reopened modal still shows it.
- **Confirm destructive actions** with the core layout's confirm modal. Put
  `@submit="doConfirm($event, @js('…'))"` on the form. In Alpine code, use
  `if (!await askConfirm('…')) return`. Don't use the browser's `confirm()`.
  Say what will be lost.
- **Change a value from a list row** with a small form whose `<select>`
  submits `onchange`. If a change needs confirming (e.g. demoting the only
  referee), use Alpine instead: `@change="if (!await askConfirm(…)) {
  $el.value = '<old>'; return } $el.form.submit()"`.
- **Warn about a missing setup step** with a `.se-alert-box` above
  the list, as the officials page does when there's no referee.
- **Put "add" forms in a modal.** Open it from a `se-btn se-btn-success`
  button next to the page heading. Submit it from the modal footer with
  `form="<form id>"`.
  - Give the form request its own `$errorBag`, and pass that bag to any
    `withErrors()` in the controller.
  - Open the modal with `:open="$errors-><bag>->any()"`, so a failed submit
    reopens it showing its errors.
  - Read field errors with `@error('field', '<bag>')`. Errors from other forms
    on the page then won't open the modal.
- **Guard routes** with `can:access,comp,"<access>"` (types are listed in
  `Competition::$accessTypes`). Repeat the same check on the tab.
- Don't modify `public/js/editable-table.js`. Layer page-specific table
  behaviour inline in the Blade view.

## Checklist for a new page

1. Extend the right layout. If it's a new area, add a tab with the matching
   `@can`.
2. Build it from the blocks above. Where an `se-` class exists for something (`se-btn`, `se-card`,
   `se-alert-box`, `se-table`, `se-modal`), use it over the older
   un-prefixed class. Only reach for Tailwind utilities for layout
   glue (`flex`, `gap-*`, `text-gray-500`).
3. Use form posts with flash/errors, and confirm anything destructive with
   `doConfirm`.
4. Give lists an empty state.
5. Check it at narrow widths. Rows should stack (`flex-col md:flex-row`).
