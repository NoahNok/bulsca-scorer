# Digital Judge UI style guide

How the Digital Judge (Svelte/Inertia) pages should look and be built. The
DQ/Penalty pages (`pages/Judge/Competition/Violation/*`) are the reference
implementation. Use them as the example when building something new or
migrating an older page.

## Principles

- **Phone first, poolside.** Judges use this one-handed on a phone, often
  in a hurry. Make tap targets large (whole rows and cards are clickable), keep
  text readable, and avoid hover-only affordances.
- **State at a glance.** Colour, badges and tiles should tell you what
  something is (DQ or penalty, pending or accepted) before you read it.
- **One obvious next action.** Each screen has one primary button. Secondary
  actions are quieter, and destructive ones are confirmed.
- **Components over copy-paste.** If markup appears on more than one page, it
  belongs in `components/`. Snippets are for markup that's repeated _within_
  one page and depends on that page's state.

## Page structure

The layouts provide the chrome, so pages don't add it themselves:

| Layer | File | Provides |
|---|---|---|
| Shell | `layout/JudgeLayout.svelte` | Padded, centred column, bottom nav, referee banner, toasts, confirm dialog |
| Header | `layout/JudgeHeaderLayout.svelte` | "Digital Judge" header + icon and the space under it (all `Judge/*` pages) |

The header defaults to the House icon linking to the competition home. Override
it from the page's `layout` export:

```svelte
<script module lang="ts">
    import { LifeBuoy } from "@lucide/svelte";

    export const layout = {
        title: "SERC",
        header: { icon: LifeBuoy, href: null }, // href: null = not a link
    };

    // or, when it depends on page props:
    // export const layout = (props) => ({ header: { href: sercHome(props) } });
</script>
```

Keep components (icons) inside the `header` object. If a component is a
top-level value of the `layout` export, Inertia may read the export as a
layout instead of props.

A page itself then follows this skeleton:

```svelte
<AppHead title="Submissions - DQ/Penalty - {competition.name}" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>DQ/Penalty</h2>

    <BackLink href={...} label="All submissions" class="mt-2 mb-4" />

    <!-- primary action, filters, then content -->
</section>
```

## Design tokens

Theme tokens live in `resources/css/app.css` (`@theme`).

### Colour

| Use | Classes |
|---|---|
| Brand / interactive / selected | `se` (teal-400): `bg-se`, `text-se`, `border-se`, tints `bg-se/5`, `bg-se/10`, `bg-se/20` |
| Active filter / toggle "on" | `bg-black text-white` (chips), `bg-white shadow-sm` on `bg-gray-100` (segmented) |
| Page surfaces | `bg-white` cards on the default page background, `bg-gray-50` for inset panels |
| Text | `text-gray-900` primary, `text-gray-700` body, `text-gray-500` secondary/labels, `text-gray-400` hints/disabled |
| Borders | default `border` (gray-200 via base layer); `hover:border-gray-400` or `hover:border-se` |
| DQ | `bg-red-100 text-red-700` |
| Penalty | `bg-orange-100 text-orange-700` |
| Success / accepted | green-100 / green-700 (`Button variant="success"`) |
| Appealed / warning | amber-100 / amber-700 |
| Voided (rejected, removed) | `bg-gray-100 text-gray-400`, `line-through` on codes, `opacity-60` on cards |

Don't map domain state to colours inline in templates. Put the mapping in a
helper next to the type (see `statusTileClass`, `vtypeTileClass`, `statusBadgeClass`, and
`stateColor` in `types/violation.ts`) so every page agrees.

### Typography

- `h1`–`h4` are globally styled as Archivo, semibold, uppercase. Use `h2`
  for the page title. `normal-case!` opts out.
- `font-archivo` for codes and "label-y" display text (DQ12, panel titles).
- Body copy: `text-sm`. Secondary lines: `text-sm text-gray-500`.
- Small headings above groups: `<SectionLabel>` (`text-xs uppercase tracking-wide text-gray-500`).
- Field labels and `dt`: `text-xs text-gray-500`; their values `text-sm font-semibold`.
- Long names: `truncate` with `min-w-0` on the flex child.

### Shape, depth and spacing

| Element | Radius | Depth |
|---|---|---|
| Cards, panels, list containers | `rounded-xl` | `border bg-white shadow-sm`, `hover:shadow-md` if clickable |
| Tiles, inputs, buttons, event tiles | `rounded-lg` | `border` (inputs `shadow-xs`) |
| Chips, badges, avatars | `rounded-full` | none, or `ring-1 ring-inset` for badges |

- Card padding: `p-3` (dense rows) or `p-4` (summary cards).
- Vertical rhythm: `gap-2` between list items, `mt-3` between stacked cards,
  `mb-3` under controls (filters, search, toggles), `mt-6` before an action panel.
- Horizontal scrollers (chip rows): `-mx-1 flex gap-2 overflow-x-auto px-1 pb-1`.

### Icons

Lucide (`@lucide/svelte`). Sizes: `14` inline with small text, `16` in buttons
and labels, `18` row affordances (chevrons, checks), `32` empty states, `40`
header. A selected row shows `<Check class="text-se" />` on the right, and a
navigable row shows a `ChevronRight` that nudges right on hover.

## Components

### General (`components/`)

| Component | Use for |
|---|---|
| `Button` | All buttons. Variants: `primary` (main action), `secondary`, `success`, `danger`, `white` (bottom nav). Has `icon`, `loading`. Note it has `ml-auto` baked in, so pass `w-full` when stacking. |
| `Input` | Text, number and textarea fields. Use `variant="soft"` for new forms; it supports `label`, `hint`, and a required asterisk. |
| `SearchInput` | Filtering a list in place. `bind:value`. |
| `FilterChip` | Status/category filters with optional count. You own the state (`active`, `onclick`). |
| `SegmentedControl` | Switching between 2–4 views of the same list (DQs / Penalties). `bind:value`, typed by option values. |
| `NumberedList` / `NumberedListItem` | Numbered rows in a bordered list (criteria, tanks, running order). Give an item `href` to make the whole row a link with a chevron; `trailing` adds a hint or badge on the right; `onclick` (without `href`) makes it a button. |
| `Judging/SignOffCheckbox` | The required "I acknowledge…" sign-off panel before submitting marks, times or OOF. Pass `id` (and `name` if the form needs it). |
| `SectionLabel` | Small uppercase heading above a group of rows or a form section. |
| `EmptyState` | "Nothing here yet" with an icon, title, description, and optional children for an action. |
| `LiveIndicator` | Pulsing dot for data that refreshes via `usePoll`. |
| `BackLink` | "← Back to X" text link under the page title. |
| `Spinner` | Loading. Centre it: `<div class="flex justify-center py-6"><Spinner /></div>`. |
| `Stepper` / `Step` | Multi-step flows. Steps stay locked until previous ones complete; call `setTitle()` with the chosen value and `complete()` from the step's `step_controls`. |
| `ConfirmDialog` | Any destructive or state-changing action (approve, reject, remove). Or `confirm()` from `lib/confirm` when imperative. |

### Domain (`components/Judging/<Area>/`)

Components that only make sense for one feature go here, e.g.
`Judging/Violation/ViolationCodeTile`, `ViolationStatusBadge`,
`ViolationSubmissionCard`, `ViolationTimeline` (a submission's status history,
dot colours from `timelineDotClass`; a SUBMITTED entry coming from REJECTED reads "Resubmitted"), and `Judging/SERC/JudgeMarkingPoints` /
`MarkingPoint` (the SERC marking card and its mark buttons).

Mark/option buttons are a `peer sr-only` radio (inside a `relative` wrapper; `h-0 w-0` leaves a blank line) plus a `<label>`:
`h-10 rounded-lg border bg-white font-mono`, selected
`peer-checked:bg-se peer-checked:border-se peer-checked:text-white`.

## Patterns

### Selectable list rows

A whole-row `<button type="button">` grouped in a bordered container:

```svelte
<div class="divide-y overflow-hidden rounded-lg border">
    <button type="button"
        class="flex w-full cursor-pointer items-center gap-3 px-3 py-2 text-left transition-colors hover:bg-gray-50 {selected ? 'bg-se/5' : 'bg-white'}">
        <span class="flex h-8 min-w-8 items-center justify-center rounded-md bg-gray-100 text-sm font-semibold text-gray-600">L3</span>
        <span class="flex-1 truncate text-sm font-medium">{name}</span>
        {#if selected}<Check size={16} class="shrink-0 text-se" />{/if}
    </button>
</div>
```

Standalone selectable cards (event tiles, violation codes) use
`rounded-xl border bg-white p-3 hover:border-se`, plus `border-se bg-se/5`
when selected.

### Navigable cards

`<Link>` wrapping a card: `group flex items-center gap-3 rounded-xl border bg-white p-3 shadow-sm hover:border-se hover:shadow-md`,
a tile on the left, a text stack (`truncate`) in the middle, a badge and chevron on the right.
See `ViolationSubmissionCard` (pass `showSubmitter` where the list mixes
judges, e.g. the head ref's "All judges" view).

### Grouped action cards

For items with several destinations (an event and its judging pages; see
`Competition/Home`): an `h3` name above the card (with a status icon on the
right if needed), then a `divide-y overflow-hidden rounded-xl border bg-white shadow-sm`
card of whole-row `<Link>`s (`px-3 py-2`). Each row has a `size-8 rounded-md bg-gray-100`
icon tile (tinting `bg-se/20` on hover), a `font-archivo` label and a
`ChevronRight`. A head-ref-only row at the bottom uses the `bg-se/5` tint.
Space items `gap-5`.

### Marking pages

Marking pages (SERC, Times, OOF) share one shape: title, a "Change heat" /
"Back to …" `BackLink`, then a header outside any card (`SectionLabel` for the
mode, `font-archivo text-xl font-semibold` for the heat/team), an optional
`bg-gray-50` instructions panel with an `Info` icon, the inputs in a
`divide-y rounded-xl border bg-white` list, then `SignOffCheckbox` and a
full-width primary submit `Button`.

### Detail views

- **Edit pages reuse the create page.** `Violation/Issue` doubles as the edit
  page for a rejected submission: given a `submission` prop it prefills the form
  (`submissionToPost`), walks the stepper to Details, and posts to `resubmit`.
  Prefer this over a separate edit page when the fields are the same.
- **Summary card** at the top: type label + status badge, large tile + name/event, description with `border-l-2 pl-3`.
- **Fact strip:** `<dl class="grid grid-cols-3 divide-x rounded-xl border bg-white">` with `p-3` cells.
- **Key/value rows:** `<dl class="divide-y rounded-xl border bg-white">`, label left, value right.
- **Missing values:** show `–`, or an italic `text-gray-400` sentence ("No details given.").

### Privileged actions

Group role-specific actions (e.g. head referee) in their own panel at the
bottom: `rounded-xl border border-se/40 bg-se/5 p-4`, an icon + `font-archivo uppercase`
title, a sentence of guidance if needed, then side-by-side `ConfirmDialog`s with `triggerClass="w-full"`.

### Forms

- `grid grid-cols-2 gap-x-3 gap-y-3`; full-width fields wrap in `col-span-2`.
- Group fields under `<SectionLabel class="col-span-2 -mb-1 mt-2">`.
- Submit is a full-width `Button` (`col-span-2 w-full`) with `loading={form.processing}`.
- Validate client-side with `toastError(...)` before posting.

### Feedback and live data

- Success and error toasts: `toastSuccess` / `toastError` from `lib/toast.svelte`.
  Server flashes with a `toast` key are shown automatically by the layout.
- Polling lists: `usePoll(5000, { only: [...] })` plus a `LiveIndicator` next to the title.
- Animate list reordering with `animate:flip={{ duration: 200 }}` and reveal async sections with `transition:slide`.

### Requests

- **Page navigations and form posts** that should reload props: `useForm` /
  `<Form>`, or `router`.
- **Background requests** (fetch data, save without leaving the page): `useHttp`.
  `processing`, `response` and `wasSuccessful` drive spinners and results.
- Send data in one of two ways. **Never assign `http.data = ...`.** `data()`
  is the hook's own getter, and overwriting it breaks `isDirty`, `reset()` and
  errors.
  - The data lives on the hook: create it with initial fields, then assign or
    `bind:` them like `useForm`:
    ```ts
    const http = useHttp({ state: "" as ViolationStatus | "" });
    http.state = status;
    http.post(url);
    ```
  - The data lives in page state (e.g. `$derived` from props): register a
    `transform` once, and it's read at send time:
    ```ts
    const http = useHttp<{}, { hasNextHeat: boolean }>().transform(() => ({ mark: times }));
    ```
- GETs with no body: `useHttp<{}, ResponseType>()`. Put parameters in the URL
  (Wayfinder `{ query }`).
- Show the result with `toastSuccess`/`toastError` in `onSuccess`/`onError`, or
  with `ActionStatusModal.showFor(promise)` for longer submits. Await it once.

### Accessibility

- Every non-submit `<button>` gets `type="button"`.
- Focusable custom controls: `focus:outline-none focus-visible:ring-2 focus-visible:ring-se`.
- Toggles expose state (`aria-pressed`, `role="radio"`/`aria-checked`). The shared components already do this.
- Use `<dl>/<dt>/<dd>` for label/value data.

## Code conventions

- Svelte 5 runes. Type props inline: `let { ... }: { ... } = $props();`.
- Reusable components accept `class` and merge it with `cn()` from
  `@/utils/utils`. Spread `...restProps` onto the root element when it wraps a
  native element (`HTMLButtonAttributes`, `HTMLInputAttributes`).
- Use `$bindable()` for the component's main value (`SearchInput`,
  `SegmentedControl`) so pages can `bind:value`.
- Component vs snippet: used on 2+ pages, or generic → component. Tied to one
  page's state (`selectEvent`, `form`) → snippet in that page.
- Tailwind utilities in markup. Don't add new global classes to `app.css`.

## Migrating an older page

Some judge pages (Dashboard, Login) predate this guide. When you
touch one:

1. Remove anything the layouts now provide. The header is already done; check
   for leftover spacers.
2. Replace hand-rolled search boxes, toggles, chips, headings, empty states and
   back links with the components above.
3. Move to `Input variant="soft"` and the form grid.
4. Don't reach for the legacy global classes in `app.css` (`.se-card`,
   `.badge`, `.tabbed-bar`, `.se-table`). Those serve the Blade views. Use the
   card, badge and segmented-control patterns here instead.
5. Move any status→colour logic into a helper beside the type.
6. Check it at phone width, with long team names, and with empty and loading states.
