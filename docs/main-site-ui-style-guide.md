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
- `layouts.guest` is for account pages outside a competition.
- `layouts.core` is the shell. It loads Alpine, the `modals` state, and the
  `showSuccess()` / `showAlert()` toasts.

Pages set `@section('title')` and fill `@section('content')`.

## Building blocks

| Need | Use |
|---|---|
| Headings | Plain `h2` (page) and `h3` (section). The base styles handle size and font. |
| Primary or navigational button | `.se-btn`, with variants `se-btn-success`, `se-btn-danger`, `se-btn-primary` |
| Button inside a form | `.btn`, with variants `btn-danger`, `btn-small`, `btn-primary` |
| Grouped panel / list | `.se-card`; add `divide-y` for a list of rows, `se-card-hover` for clickable rows |
| Status labels | `.badge` + `badge-info`, `badge-success`, `badge-danger`, `badge-orange`, `badge-gray`, sized with `badge-sm` |
| Tables | `.se-table` (`se-table-thin` for dense ones) |
| Grids | `.grid-2` … `.grid-5` (they collapse on small screens) |
| Warnings | `.alert-box` |
| Text input | `<x-form-input id="…" title="…" />`. It shows the `@error` message for that field and refills from `old()`. |
| Select | `<x-form-select>` takes models with `id` and `name`. For a fixed list, write a `<select>` inside a `div.form-input`, as the officials page does. |
| Modal | `<x-s-e-modal id="…" title="…">`, opened with `modals.<id> = true` |
| "Add" tile in a grid | `<x-add-card link="…" text="…" />` |

## Behaviour

- **Changes are plain form posts.** Use `@csrf` and `@method('delete')` where
  needed. The controller redirects `back()` with either
  `->with('success', '…')`, which the core layout shows as a toast, or
  `->withErrors([...])`, which shows under the field. Use `fetch` + JSON only
  where a modal flow already does.
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
- **Confirm destructive actions** with the core layout's confirm modal. Put
  `@submit="doConfirm($event, @js('…'))"` on the form. In Alpine code, use
  `if (!await askConfirm('…')) return`. Don't use the browser's `confirm()`.
  Say what will be lost.
- **Change a value from a list row** with a small form whose `<select>`
  submits `onchange`.
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
2. Build it from the blocks above. Only reach for Tailwind utilities for layout
   glue (`flex`, `gap-*`, `text-gray-500`).
3. Use form posts with flash/errors, and confirm anything destructive with
   `doConfirm`.
4. Give lists an empty state.
5. Check it at narrow widths. Rows should stack (`flex-col md:flex-row`).
