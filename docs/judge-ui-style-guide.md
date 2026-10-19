# Digital Judge UI style guide

How the Digital Judge (Svelte/Inertia) pages should look and be built. The
DQ/Penalty pages (`pages/Judge/Competition/Violation/*`) are the reference
implementation. Use them as the example when building something new or
migrating an older page.

**Scope:** this guide covers only the Svelte/Inertia Digital Judge app
(`resources/js`, served on the `judge.` subdomain). The main site, where
organisers set up competitions, is Blade + Alpine in `resources/views`, styled
with the global classes in `resources/css/app.css`. It follows
[`main-site-ui-style-guide.md`](main-site-ui-style-guide.md) instead. The two
don't share components, tokens or patterns.

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
| Shell | `layout/JudgeLayout.svelte` | Padded, centred column, bottom tab bar (`JudgeTabBar`: Home, DQ/Penalty, Alerts, Online, plus the "Referee mode" band for the head ref), toasts, confirm dialog, the officials-online sheet and poke animation (joins the competition's presence channel), and the Alerts sheet (starts `alerts` from `lib/alerts.svelte` and `announcements` from `lib/announcements.svelte`) |
| Header | `layout/JudgeHeaderLayout.svelte` | "Digital Judge" header + icon, the space under it, and the `AnnouncementBanner` on competition pages (all `Judge/*` pages) |

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

### Navigation

The bottom tab bar is the same on every competition page: **Home** (competition
home), **DQ/Penalty** (submissions list), **Alerts** (an unread count of DQ/Penalty
alerts plus Announcements, which opens `AlertsSheet`) and **Online** (a count of the officials online, which opens
`OfficialsSheet`), with the current area highlighted. Alerts and Online only show
when Reverb is configured (`echoEnabled` from `lib/echo`).
For the head ref, a "Referee mode" band sits along the top of the bar, so the
role is always visible without covering the page header.
On the dashboard and account pages (no competition joined) the bar has
**Account** and **Log out** tabs instead; Log out confirms first and clears the
remembered "Rejoin" competition.
Keep it to a few large, labelled tabs; some judges aren't confident with phones.
Pages don't add their own bottom navigation or floating buttons. Put
page-specific actions in the page: `BackLink` for going up a level (e.g.
"Change heat"), and a quiet text button (`text-sm text-gray-600 hover:text-se`
with a 14px icon) beside the relevant heading for secondary actions (e.g.
"Notes" on SERC marking, "Leave competition" at the bottom of the competition home).

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
| `Button` | All buttons. Variants: `primary` (main action), `secondary`, `success`, `danger`, `white`. Has `icon`, `loading`. Note it has `ml-auto` baked in, so pass `w-full` when stacking. |
| `Input` | Text, number and textarea fields. Use `variant="soft"` for new forms; it supports `label`, `hint`, and a required asterisk. |
| `SearchInput` | Filtering a list in place. `bind:value`. |
| `SearchSelect` | Picking one item from a list that may be long (e.g. the seconder from the competition's officials). Looks like a soft `Input` (`label`, `hint`, `required`). `options` are `{ value, label, description? }` with string values; `bind:value`. A search box appears in the dropdown once there are more than `searchThreshold` (8) options. `allowClear` adds a "None" row. Use `SearchInput` instead when the whole list is already on the page. |
| `FilterChip` | Status/category filters with optional count. You own the state (`active`, `onclick`). |
| `SegmentedControl` | Switching between 2–4 views of the same list (DQs / Penalties). `bind:value`, typed by option values. |
| `NumberedList` / `NumberedListItem` | Numbered rows in a bordered list (criteria, tanks, running order). Give an item `href` to make the whole row a link with a chevron; `trailing` adds a hint or badge on the right; `onclick` (without `href`) makes it a button. |
| `Judging/SignOffCheckbox` | The required "I acknowledge…" sign-off panel before submitting marks, times or OOF. Pass `id` (and `name` if the form needs it). Children replace the default wording; `bind:checked` if you need the state. |
| `SectionLabel` | Small uppercase heading above a group of rows or a form section. |
| `EmptyState` | "Nothing here yet" with an icon, title, description, and optional children for an action. |
| `LiveIndicator` | Pulsing dot for data that refreshes via `usePoll`. |
| `BackLink` | "← Back to X" text link under the page title. |
| `JudgeTabBar` | The fixed bottom tab bar (Home, DQ/Penalty, Alerts, Online). For the head ref it shows a teal "Referee mode" band along its top edge. Without a `competition` it shows "Account" and "Log out" tabs (the dashboard and `Judge/Account`). Rendered by `JudgeLayout` on every competition page, the dashboard and the account page; pages never render it. |
| `Avatar` | An official's round avatar: their initials, with their profile photo (`ProfilePhoto` from `types/presence`, a 192px `webp` + `jpg` pair) faded in over them once it has downloaded. The photo is a lazy, low-priority `<picture>`, so slow wifi or a failed download only ever shows initials. Defaults to `size-9`; pass size and text classes in `class` (e.g. `size-16 text-xl`), `dimmed` for offline officials, and overlays such as the presence dot as children. Use it anywhere an official is shown with an avatar. |
| `OfficialsSheet` | The slide-out from the right listing the competition's officials: an **Online** section (name, role, Active/Idle badge from `presenceStatusBadgeClass`, and a dot on the `Avatar` from `presenceStatusDotClass`; both teal when active, orange when idle after a minute without input or with the tab hidden) and a dimmed **Offline** section for everyone else, fetched from `judge.competition.officials` when it opens. Tapping someone online pokes them. Rendered by `JudgeLayout` and opened by the Online tab via `presence.sheetOpen`; pages never render it. |
| `AlertsSheet` | The slide-out from the right, opened by the Alerts tab via `alerts.sheetOpen`. At the top, an **Announcements** `SectionLabel` (for Referees with a quiet "New Announcement" text button beside it that opens `AnnouncementCompose`, and "No announcements yet." when empty) and an `AnnouncementCard` per Announcement; then a **DQ/Penalty** `SectionLabel` and the history of live DQ/Penalty alerts, newest first: code tile, a sentence (`alertText`), time ago and status badge; tapping a row opens the submission. Alerts come from `lib/alerts.svelte`: judges get their own submissions' status changes, the referee also gets new and resubmitted ones. Each also pops a toast with a "View" link. The alert history is per device (localStorage) and only covers alerts received while online. Opening the sheet marks alerts and Announcements read. Rendered by `JudgeLayout`; pages never render it. |
| `AnnouncementBanner` | The newest Announcement this device hasn't dismissed, pinned (`sticky`) under the header on every competition page: an amber panel (`announcementBannerClass`, `rounded-xl border p-3 shadow-sm`) with a Megaphone, a `font-archivo` "Announcement · Sam · 2 min ago" kicker, the text and an X to dismiss. Tapping it opens the Alerts sheet. `role="status"`; it flies down into place (`in:fly|global`, `y: -12`) and slides away when dismissed, with no motion under `prefers-reduced-motion`. Rendered by `JudgeHeaderLayout`; pages never render it. Announcements never toast: toasts auto-dismiss, and an Announcement stays until dismissed. |
| `PokeAnimation` | The full-screen "{name} says hi! 👋" lifebuoy animation played when someone pokes you (the 👋 waves once the caption is in). Rendered by `JudgeLayout`; caption only under `prefers-reduced-motion`. |
| `LegalNotice` | The "By continuing you agree to the Terms of Service and Privacy Policy" line at the bottom of a page that signs someone in or creates their account (`Auth/Login`, `Auth/Register`). It's the only place the judge app links to the policies: there's no footer. The urls come from the shared `legal` prop (Blade pages on the main domain), so use plain `<a>`s, not `<Link>`. |
| `Spinner` | Loading. Centre it: `<div class="flex justify-center py-6"><Spinner /></div>`. |
| `Stepper` / `Step` | Multi-step flows. Steps stay locked until previous ones complete; call `setTitle()` with the chosen value and `complete()` from the step's `step_controls`. |
| `ConfirmDialog` | Any destructive or state-changing action (approve, reject, remove). Or `confirm()` from `lib/confirm` when imperative. |

### Domain (`components/Judging/<Area>/`)

Components that only make sense for one feature go here, e.g.
`Judging/Violation/ViolationCodeTile`, `ViolationStatusBadge`,
`ViolationSubmissionCard`, `ViolationTimeline` (a submission's status history,
dot colours from `timelineDotClass`; a SUBMITTED entry coming from REJECTED reads "Resubmitted"), and `Judging/SERC/JudgeMarkingPoints` /
`MarkingPoint` (the SERC marking card and its mark buttons),
`Judging/Announcement/AnnouncementCompose` (the Referees' compose modal, a
`GenericDialog` with `bind:open`: a soft textarea with a "n/280" hint, a `bg-gray-50`
`Info` note that everyone sees it and it can be withdrawn but not edited, and a "Send"
`Button`; the modal is the confirm step, so there's no extra `confirm()`) and
`AnnouncementCard` (sender `Avatar`, text, time ago, a new dot and `bg-se/5` tint from
`announcementCardClass` in `types/announcement.ts`; for Referees a quiet "Withdraw"
button, confirmed, which deletes it for everyone), and
`Judging/ConfirmResults/*` for the head ref's Confirm Results pages:
`ConfirmResultsSummary` (fact strip + amber warning panel), `HeatResultsCard`
(a speed heat: lane, OOF, time, violations), `SERCEntityResultsCard` (one team's
marks per judge, totals, violations, notes), `ViolationList` (applied DQs/penalties
and pending submissions with their descriptions) and `ConfirmResultsPanel` (sign-off,
submit and `ActionStatusModal`).

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
`ChevronRight`. A row that starts something on the server (e.g. "Start judging"
on a SERC using League Objectives) is the same row as a `<Link method="post" as="button">`.
A head-ref-only row at the bottom uses the `bg-se/5` tint. When
that row's action is done it becomes a static (non-link) row in the success hue:
`bg-green-50`, a `bg-green-100 text-green-700` tile with the same icon, `text-green-800`
label and a trailing `Check` instead of the chevron (e.g. "Results Confirmed").
Space items `gap-5`.

A **locked** item (SERCs before the referee releases them; the server sends
`locked: true` and no links) keeps the `h3` name but replaces the links with one
static row: a `bg-gray-100 text-gray-400` tile with `Lock` and a `text-gray-500`
explanation, with no chevron. For the head ref, the group's state change sits with the
group: while locked, an amber panel (`rounded-xl border border-amber-200 bg-amber-50 p-4`)
above the cards explains who can see them and holds a full-width primary `Button`
("Release SERCs"). Once released, a quiet "Lock SERCs" text button sits beside the
`SectionLabel`. Both confirm first.

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
- **Live detail pages** poll with `usePoll` (`only` the changing props) and show
  a `LiveIndicator` by the heading. When polling brings in a change someone else
  made, announce it: a toast (8s, like a live alert; toasts always auto-dismiss), a brief `ring-2 ring-se` on the
  summary card and `navigator.vibrate?.(200)`. Track the last seen value so the
  viewer's own actions aren't announced (see `Violation/Submission`).
- **Summary card** at the top: type label + status badge, large tile + name/event, description with `border-l-2 pl-3`.
- **Fact strip:** `<dl class="grid grid-cols-3 divide-x rounded-xl border bg-white">` with `p-3` cells.
- **Key/value rows:** `<dl class="divide-y rounded-xl border bg-white">`, label left, value right.
- **Missing values:** show `–`, or an italic `text-gray-400` sentence ("No details given.").

### Confirm Results pages

`ConfirmResults/SERC` and `ConfirmResults/Event` (head ref only, `judge.referee`
middleware) show results in draw/heat order for checking, then confirm the event.
Shape: title + `BackLink`, `SectionLabel`, `ConfirmResultsSummary`, an `Info` panel,
then an `<InfiniteScroll>` of result cards. The `next` snippet shows a `Spinner`
while `hasMore`, and only reveals `ConfirmResultsPanel` once every page has loaded,
so the head ref has to scroll past every result before confirming. Missing results
and pending submissions warn (amber) but don't block confirmation.

Missing values in results use an amber `–` rather than gray, since they need attention.

### Centred pages (sign-in)

Pages with no tab bar (`Auth/Login`, `Auth/Register`, `Auth/ForgotPassword`) set `header: { overlay: true }` and centre a
`mx-auto w-full max-w-sm` column in the viewport (`flex h-full flex-col justify-center`).
Same title skeleton (`font-archivo` kicker + `h2`), a `text-sm text-gray-500`
sentence, then the form directly below (`mt-6 grid gap-3`, no card around it).
Code/PIN fields: `type="text" inputmode="numeric" autocomplete="one-time-code"`
(not `type="number"`, which drops leading zeros and shows spinners), centred `font-mono`.
Strip non-digits on input and `submit()` once the code is complete; keep the submit button as a fallback.
Only auto-submit when the code is the last field (e.g. not on `ForgotPassword`, where the new password follows it).
When a server-rendered stage (like the PIN stage) has to survive a validation error, re-render that stage with
`errors` rather than redirecting back, which would land on the first stage.
An alternate sign-in method (password instead of PIN) is a quiet text button under the
form that swaps the fields and `action`, not a second primary button. Links between the
auth pages ("Create an account", "Forgot password?", "Back to sign in") are quiet text links too.
Pages that sign someone in or create their account end with `<LegalNotice class="mt-6" />`.

`Judge/Landing` (the judge site's root) is the same shape with no form: one
primary "Start judging" `Button` into Digital Judge, then a quiet "Use Legacy
Digital Judge" text button that `confirm()`s before a full page load to the
Blade legacy app (not `router.visit`, since it isn't an Inertia page), and the
personal-device disclaimer in `text-xs text-gray-500` at the bottom.

### Privileged actions

Group role-specific actions (e.g. head referee) in their own panel at the
bottom: `rounded-xl border border-se/40 bg-se/5 p-4`, an icon + `font-archivo uppercase`
title, a sentence of guidance if needed, then side-by-side `ConfirmDialog`s with `triggerClass="w-full"`.

### Forms

- `grid grid-cols-2 gap-x-3 gap-y-3`; full-width fields wrap in `col-span-2`.
- Group fields under `<SectionLabel class="col-span-2 -mb-1 mt-2">`.
- Submit is a full-width `Button` (`col-span-2 w-full`) with `loading={form.processing}`.
- Validate client-side with `toastError(...)` before posting.
- **Image uploads**: shrink the image with `downscaleImage(file)` from `lib/image` before posting
  (a phone photo becomes a ~60 KB JPEG), then send it with `forceFormData: true`. The server still
  validates and re-encodes it (see `ProfilePhotoService`). The account page's photo picker is the example.

### Feedback and live data

- Success and error toasts: `toastSuccess` / `toastError` from `lib/toast.svelte`.
  Server flashes with a `toast` key are shown automatically by the layout.
  Pass `action: { label, href }` to `toast()` to add a link under the text
  (e.g. "View" on an alert); following it dismisses the toast. `onClick` runs
  when the toast is tapped anywhere (body, action or close) and dismisses it;
  alerts use it to mark themselves read.
- Polling lists: `usePoll(5000, { only: [...] })` plus a `LiveIndicator` next to the title.
- **Long lists** (results for a whole event): page them on the server with
  `Inertia::scroll($paginator)` and render with `<InfiniteScroll data="prop" preserveUrl>`
  from `@inertiajs/svelte`. Put other props in closures so scroll requests (partial
  reloads of just that prop) don't recompute them. Batch-load each page's related rows
  (`whereIn`) instead of querying per row. See `ConfirmResultsController`.
- Animate list reordering with `animate:flip={{ duration: 200 }}` and reveal async sections with `transition:slide`.
- **Relative times** ("Just now", "5 min ago"): `timeAgo()` from `lib/timeAgo.svelte`.
  It ticks every 30s while something on screen reads it, so don't add your own timer.
- **Announcements**: `announcements` from `lib/announcements.svelte.ts`, started by
  `JudgeLayout`. They're stored on the server: the list is fetched on start and each time
  the presence channel (re)subscribes, and `announcement.sent` / `announcement.withdrawn`
  arrive live on `judge.officials.{id}`. Read `announcements.live`, `.unread` and
  `.banner`. Read and dismissed are per device (localStorage); something new buzzes once
  (`navigator.vibrate?.(200)`), never for your own. Withdrawing deletes it, so it
  disappears for everyone.
- **Presence** (who's online): `presence` from `lib/presence.svelte.ts`, joined by
  `JudgeLayout` for the current competition (`judge.officials.{id}` presence channel,
  authorised in `routes/channels.php`). Read `presence.sorted`, `presence.count` and
  `presence.connected`. Statuses (Active/Idle) and pokes are whispers, so nothing is
  stored. Get the websocket client from `getEcho()` in `lib/echo.ts` rather than
  `window.Echo`, which is the legacy Blade app's. Map statuses to colours with
  `presenceStatusBadgeClass` in `types/presence.ts`.

### Requests

- **Page navigations and form posts** that should reload props: `useForm` /
  `<Form>`, or `router`.
- **Background requests** (fetch data, save without leaving the page): `useHttp`.
  Shared state modules in `lib/` (outside any component) can't use the hook, so they
  use `fetch` with `Accept: application/json` (see `announcements.refresh()`).
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
  On a 409 the modal shows the response's `message` (e.g. "Already marked — ask a
  referee to change it."), so return `abort(409, '...')` for refusals the judge
  should read; any other error shows the generic "Something went wrong".

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

Some judge pages predate this guide. When you touch one:

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
