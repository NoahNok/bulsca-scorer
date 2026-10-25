# v2 UI style guide

How redesigned main-site pages look and are built. The v2 home page at `/v2`
(`resources/views/landing/v2/home.blade.php`) is the reference
implementation, and `/v2` ends in a sample sheet of every control in this
theme (`landing/v2/partials/kit.blade.php`). Use both as the example when you
redesign a page.

**Scope:** pages that have been redesigned onto `landing.v2.layout`. Pages that
haven't moved yet still follow
[`main-site-ui-style-guide.md`](main-site-ui-style-guide.md), and the Digital
Judge follows [`judge-ui-style-guide.md`](judge-ui-style-guide.md). Don't mix
v2 classes into an un-migrated page, and don't use the old `se-` classes on a v2
page: redesign the whole page or leave it alone.

## Principles

- **The favicon is the brand.** The "S." mark (`#111827` tile, white S, teal
  dot) sets the palette: ink, white and one teal accent. Teal marks what's live,
  selected or actionable. Don't spend it on decoration.
- **What's happening now comes first.** Lead with the live or most recent
  thing (today's competition, the latest result). Explanation comes after.
- **Whole rows are targets.** A row or card that leads somewhere is one link
  with one hover state, not a row with a small link at the end.
- **Light and dark are equals.** Every page works in both, from the OS
  setting or the header toggle. Check both before you call a page done.
- **One moment of motion.** Use motion to show what changed (a toast
  arriving, a tab moving, the theme switching), never as decoration. Respect
  `prefers-reduced-motion` every time.

## Page structure

| Layer | File | Provides |
|---|---|---|
| Shell | `landing/v2/layout.blade.php` | The favicon, fonts, Vite CSS/JS, Alpine (with the focus plugin), the header (brand, Explore, Officials, Sign in/Management, theme toggle) and the footer's legal links |
| Shared styles | `resources/css/v2.css` | Every token, component and pattern below, plus the dark re-skin of `<x-search-all />`. Loaded by the layout |
| Brand mark | `landing/v2/partials/mark.blade.php` | The favicon's "S." as inline SVG: `@include('landing.v2.partials.mark', ['size' => 32, 'live' => true])`. With `live`, its dot pulses |
| Samples | `landing/v2/partials/kit.blade.php` | The catalogue: every shared component in use. Only the sheet's own layout (`k-` classes) is styled there |
| Theme | `landing/v2/partials/theme-script.blade.php`, `resources/js/lib/theme.ts` | The script that applies a saved theme before the first paint (in the `<head>` of every v2 page), and the toggle logic. Blade's Alpine toggle reaches it as `window.v2Theme` (set in `basic-app.js`) |

A page `@extends('landing.v2.layout')`, puts styles only it uses in
`@section('v2-styles')` and its markup in `@section('content')`. Once a
second page needs a style, move it into `v2.css` with a `v2-` name. The layout
is standalone, not `layouts.core`, because core's header and footer can't do
dark mode. So it doesn't get core's toasts, `modals` state or `customConfirm`.
Use the patterns below instead.

Content sits in a `max-width: 76rem` column with a `1rem` side gutter, to line
up with the header: `.v2-container`. Full-bleed bands (like the pool hero)
break out of it.

### The Management app (Svelte)

Management and Admin pages are Svelte + Inertia, in an app separate from the
Digital Judge ([ADR 0002](adr/0002-public-blade-management-svelte.md)). They
use this guide's tokens and `v2.css` classes, not the judge guide or judge
components.

| Layer | File | Provides |
|---|---|---|
| Root view | `resources/views/manage.blade.php` | The theme script, fonts, `manage.css` and `manage.ts` |
| Styles | `resources/css/manage.css` | The app's own Tailwind build, separate from `app.css` (see Tailwind below). It imports `v2.css` into Tailwind's `components` layer, so utilities beat v2 classes, and has none of `app.css`'s global rules (`se-` classes, `h1`–`h4`, the `[title]` tooltip) |
| Entry | `resources/js/manage.ts` | The Inertia app. Resolves only `pages/Manage/**` and `pages/Admin/**` (`app.ts` leaves them out) |
| Shell | `resources/js/manage/layout/ManageLayout.svelte` | The same `.v2-header` and `.v2-footer` as the Blade layout, with Explore, Admin (for Admins), Sign out and the theme toggle, and a `.v2-container` for the page, with 0.5625rem above it (0.75rem from `sm`) so every page's title has room under the header. Pages don't add their own top space |
| Competition frame | `resources/js/manage/layout/CompetitionLayout.svelte` | Inside the shell for `pages/Manage/Competition/*`: the competition's name (`clamp(1.75rem, 3.5vw, 2.5rem)`, smaller than a page statement), its date (Archivo, not Anton), venue, championship and Live/Private badges, the organisation logo, its page tabs (`.v2-pagetabs`) and the Organisers' status message as an info alert. It reads the page's `competition` prop, which `App\Helpers\CompetitionHeader::for()` builds, so every page in it sends that. The tabs come from `App\Helpers\CompetitionTabs`, which the Blade `layouts/competition.blade.php` draws too, so both always agree; each tab says whether it's Svelte (an Inertia `<Link>`) or Blade (a plain `<a>`). To port the next tab: render it as `Manage/Competition/<Page>`, send `competition`, and flip its `svelte` flag in `CompetitionTabs` |
| Signed-out frame | `resources/js/manage/layout/AuthLayout.svelte` | Used instead of the shell for `pages/Manage/Auth/*` (sign in, register, forgot and reset password, Invites). The `PoolBand` (one lane with its big ghosted number; the mouse picks the lane by height and swims a top-down swimmer along it by width, with bubbles and ripples on a canvas behind it. It treads water when the mouse stops, rarely dives for a few strokes, and stays under while a password field has focus) beside the form on desktop (phones get the brand mark and name in the top bar instead), the theme toggle, the session flashes as alerts, and the legal links. It holds each page's band line and the `.a-title`, `.a-lead`, `.a-form` and `.a-aside` classes the pages use |
| Event components | `resources/js/manage/events/` | Pieces only the event pages use. `SpeedResultsGrid` (the Enter results grid: a row per result in heat and lane order with time, DQ and, when the event takes them, penalties inputs, heat pills and a search; one Save posts only the changed rows to `App\Services\SpeedResultEntry`, which checks every row first and saves none if any cell is wrong, putting each error under its cell as `rows.{id}.{cell}`; a failed save clears the filters so every failing row shows) and `ScoringSchemaEditor` (an event's scoring: before it has any, start from an organisation's schema (`App\Services\ScoringSchemas::availableFor`) or blank; then the result equation, penalty function, global and local variables, automatic penalties and DQs, grouping and ranking, on `useForm`, dropping half-filled rows on save, with "Start again from another schema" in a `<details>`) |
| Components | `resources/js/manage/components/` | `BrandMark` (the Svelte copy of `mark.blade.php`), `ThemeToggle`, `Field` (a `.v2-field` with its error and hint wired up, and show/hide on passwords), `Alert` (a `.v2-alert` with its icon), `SubmitButton` (full-width, spinner and disabled while processing), `LegalNotice` (the policy line under forms that sign in or create an account), `PoolBand`, `FormPage` (a page that's one form, like New competition and New organisation: a "Home" link back, the uppercase title and a muted lead, then the form in a 32rem column centred in the container, its fields spaced by the form's gap; end it with a `.p-actions` row: the full-width `SubmitButton` and a muted Cancel link under it) and `CompetitionTable` (competitions an Organiser can manage, as whole-row "Manage" links: a muted Archivo date, the name (with a muted organisation name when given), and a status of Live now, Today or "In N days" plus Private and Limited access badges beside the link; no venue; past rows are muted), `Tabs` (the `.v2-tabs` slider over snippet panels, below), `SidebarPage` (the default for a page with main content and a sidebar: Overview, Entries & Leagues, an event's page; see "Two-column pages" below), `SideMenu` (a menu beside the content showing one part at a time, for a page with several parts like competition Settings: a sticky column of links from `56rem`, a row that scrolls sideways above the content on phones. Pass `items` (`id`, `label`, `rule` for a divider above it, `danger` for red), bind `value` if the page needs it, and put each part in a `SideMenuPart` (`id` matching its item, `labelledby` its heading), which hides itself unless it's current. Give each part its own form with `UnsavedChanges` in it: switching away from a part with unsaved changes asks to discard them, and discarding remounts the part back to its saved values (`ondiscard` resets anything the page keeps outside the form, like a live preview). The part follows the url's hash, the first item having none), `UnsavedChanges` (put `<UnsavedChanges dirty={isDirty} />` in any `Form`'s snippet, with `setDefaultsOnSuccess` on the form, and leaving with unsaved changes asks first: a `SideMenu` switch, an Inertia visit to another page (the guard in `manage/unsaved.ts`, which `ManageLayout` installs), or a plain link, reload or closing the tab (the browser's own prompt). Saves and other POSTs go ahead. Use it on every edit form; not on create forms or a delete confirmation), `MultiSelect` (a `.v2-field` for picking several options, on bits-ui's `Combobox`: the chosen ones as chips with remove buttons, typing filters the list, Backspace removes the last chip. The form gets one hidden input per chosen value, so give it a `name` ending in `[]`. Inside a `<dialog>` it puts its list in the dialog, and Escape closes the list, not the dialog. Use it instead of a native `multiple` select or a long run of checkboxes), `Switch` (a `.v2-switch` that a form sends as 1 or 0, so off is saved too, with an optional hint), `RichText` (Quill from `/vendor`, on the tokens, sending its HTML as a hidden input), `Toasts` (the `.v2-toast` stack for `lib/toast.svelte.ts`) and `ConfirmDialog` (answers `confirm()` from `lib/confirm.ts` with a `<dialog class="v2-modal">`, Cancel first). `ManageLayout` mounts `Toasts` and `ConfirmDialog` once, and installs the unsaved changes guard. Their prop types are in `resources/js/manage/types.ts`. The app's own components go here |
| Routes | `routes/app.php` | Every signed-in page, Blade or Svelte, on the `app.` subdomain, like the Digital Judge on `judge.`. `HandleInertiaRequests` gives every Inertia page on that domain the `manage` root view, and shares `manage.isAdmin`, `manage.site.home` and `.explore` (the public site's urls) and `manage.flash` (the `status` and `alert-error` session flashes, and `success`, which `ManageLayout` toasts after every visit) in place of `judge`. A save answers with `back()->with('success', '…')` and puts errors that aren't about a field's format (already invited, wrong name) on that field with `ValidationException::withMessages`. Name new routes `manage.*` (Admin pages `manage.admin.*`). The app's home (`manage.home`, `pages/Manage/Home.svelte`) is at `/`: Recent cards, then one `CompetitionTable` of everything you can manage with a pill filter per organisation (yours, then ones you were invited into) and Personal, showing upcoming and the 3 latest past until "Show all". `/dashboard` redirects to it. New competition (`manage.competitions.create`, `/create`, `?org=<id>` preselects one) and New organisation (`manage.organisations.create`, `/organisation/create`) are `FormPage`s; a competition lands on its Overview. A competition's Overview (`comps.view`, `/comps/{comp}`, `pages/Manage/Competition/Overview.svelte`) lists its events as rows by your access (leagues live on Entries & Leagues, not here), and for Organisers links to Settings and the activity log under the events, and beside them the Digital Judge panel (the one bold panel: `--v2-teal-soft` with a teal border in light, the dark `--v2-tile` band in dark; the PIN in teal Anton, QR code, releasing or locking the SERCs, on and off). Settings (`manage.competitions.settings`, `/comps/{comp}/settings`, Organisers only) is a `SideMenu`. Every part is its own form with its own Save button and "Unsaved changes": Details, Setup, Heats, Names and Status all post to `comps.settings`, which takes any subset of its fields; a rule in the menu splits them from Digital Judge events, Accounts (a table; a row opens a dialog to change access or remove them, and Invite opens another) and Delete (type the name; its menu item is in danger red). A failed save focuses the first field with an error, and links can open a part by its hash (`#setup`, `#digital-judge`). Use `SideMenu` for any other settings page with several parts, rather than stacking them. Entries & Leagues (`manage.competitions.entries`, `/comps/{comp}/entries`, `pages/Manage/Competition/Entries.svelte`; `/entities` redirects there) lists the clubs, teams and competitors as one tree table (a club, its teams indented, each team's competitors indented again, then teams without a club and competitors without a team), with their leagues and a seed column per seedable event, and the leagues as rows beside it. Entries are added from the Add buttons at the top, which stick to the top of the screen with the heading and a search box as you scroll the table (a rule shows under them once stuck). The search filters by name, keeping a match's club and team and everything under it. Hovering a club row shows a "+ Team" button and a team row a "+ Competitor" button (`.v2-over-row`, also on keyboard focus, hidden on touch screens), opening the add dialog with that club or team already picked. Every row opens a dialog that saves or removes that one entry or league at once (`CompetitionEntriesController`, `LeagueController`), so there's no Edit page or unsaved state; removing a club or team confirms what it takes with it. An entry's leagues are a `MultiSelect`. Events (`manage.competitions.events`, `/comps/{comp}/events`, `pages/Manage/Competition/Events.svelte`) shows a section per type you have access to (Speed events with `speed`, SERCs with `serc` or `serc_writer`), each a grid of cards like Home's Recent ones: the name, then one line of badges under it: Complete or Incomplete, "Confirmed by Referee" and Hidden (no line saying what it scores). The last card in each grid is a dashed add card: "Add speed event" opens a dialog (event and what it scores) that adds it with a result for every Entity and opens its page, and "Add SERC" goes to the SERC setup page. A speed event's page (`manage.competitions.events.speeds.show`, `/comps/{comp}/events/speeds/{event}`, `pages/Manage/Competition/SpeedEvent.svelte`, `Manage\SpeedEventController`) is two columns split 2:1 like Entries & Leagues, with nothing above them. The first holds a muted "← Events" link, the event name with its Complete/Incomplete and Hidden badges, then its results. The second, from the top of the page and sticky, is the actions column (under the first column on phones, as Leagues is): Edit results and Settings as whole-row links with chevrons to the event's own pages, the "Shown in results" switch (saves at once), then Print results (the Blade print page, in a new tab). The results have no heading of their own under the name: a danger alert with a button per heat whose times and Digital Judge order of finish disagree, opening that heat in a dialog, league pills that reload only the results, a search and the ranked table. Edit results (`manage.competitions.events.speeds.results.edit`, `/results`, `SpeedEventResults.svelte`) is a full-width page, no sidebar: a muted "← {event}" link back, the "Edit results" heading and a lead, then `SpeedResultsGrid`. Settings (`manage.competitions.events.speeds.settings`, `/settings`, `SpeedEventSettings.svelte`) has the same link back and a "Settings" heading above a `SideMenu` like competition Settings: Setup (what it scores, confirming that changing it deletes its results, and Has penalties), Scoring (`ScoringSchemaEditor`), then a rule and Delete (type the event's name, as for a competition; `destroy` checks it). Its old `/edit`, `/edit-result` and `/scoring-settings` urls redirect to `/settings`, `/results` and `/settings#scoring`. SERC pages are still Blade |

Links from `app.` to public pages use `RouteHelpers::mainRoute()` in Blade
and `manage.site` in Svelte. `route()` would build them on `app.`. Link to a
Blade page with a plain `<a>`, not Inertia's `<Link>`, which can only load
Inertia pages.

A Svelte page uses the same markup and classes as the kit for components
(`.v2-btn`, `.v2-field`, `.v2-alert` and so on), and Tailwind for everything
else.

#### Tailwind

- **Layout, spacing and type size are utilities**: `flex flex-col gap-2`,
  `grid sm:grid-cols-2`, `mt-4`, `text-sm font-semibold uppercase`. Don't write
  a scoped class for what a few utilities say. Utilities beat v2 classes, so
  `class="v2-btn w-full"` works.
- **Colours are only the tokens**: `manage.css` removes Tailwind's palette and
  maps each `--v2-*` token to a colour, so `text-v2-muted`, `bg-v2-card`,
  `border-v2-border`, `bg-v2-teal-soft`, `text-v2-danger-text`. They switch
  with the theme on their own, so you rarely need `dark:`. `text-gray-600` or
  `bg-white` generate nothing. Add a token to `v2.css` and its
  `--color-v2-*` line to `manage.css` together. A colour no token covers, used
  once, is an arbitrary value (`dark:border-[#374151]` on the Digital Judge
  panel). Make it a token when a second place needs it. Opacity works on
  tokens: `border-v2-teal/35`.
- **`dark:`** follows the theme like `v2.css` does: the OS setting unless the
  toggle sets `data-theme`. Use it for the odd dark-only change, like the
  Digital Judge panel's `--v2-tile` band.
- **Fonts:** `font-sans` is Archivo (the default) for everything, dates and
  data included: write dates as `font-semibold` Archivo (Home, `CompetitionTable`,
  the competition frame). `font-anton` is only for the Digital Judge PIN; don't
  use it for dates, counts or other data.
- **Headings have no global styles** here, unlike Blade: Tailwind's reset
  leaves them at body size. Give each one its size, weight and `uppercase`.
- **Reusable components:** once a layout or pattern is settled (the user is
  happy with it, not while it's still being iterated on), lift it into
  `resources/js/manage/components/` with a small props API, like `SideMenu`
  from competition Settings, and list it under Components above. Prefer
  reusing one of those over writing the markup again on a new page.
- **Repeated markup:** when one page repeats a class list (Home's filter
  pills), keep it in a `const` in the script and pass it as `class={chip}`.
  Style table cells from the row with `*:` (`*:py-3.5 *:first:pl-3`, see
  `CompetitionTable`), and hover children with `group` / `group-hover:`. A
  repeated piece of markup, like a row's chevron, is a `{#snippet}`. Style
  HTML you don't write (`{@html}`, like the status message) with `[&_a]:`.
- **Animations** are named in `manage.css`'s `@theme`: `animate-rise` (fades
  in rising half a rem, 300ms; the sign-in pages), `animate-rise-fast` (200ms;
  a dialog opening), `animate-fade-in` (a `Tabs` panel) and `animate-spinner`
  (`SubmitButton`). Always pair one with `motion-reduce:animate-none`. Add a
  new one there with its `@keyframes`, not in a `<style>` block: Svelte
  renames `@keyframes` in a component, and a scoped animation can't be
  turned off by `motion-reduce:`. Transitions are utilities too:
  `transition-[height] duration-250 motion-reduce:transition-none`.
- **Sibling spacing:** utilities beat `v2.css`, so `*:mt-0` on a grid or
  fieldset drops the `.v2-field + .v2-field` and `.v2-check + .v2-check`
  margins and lets `gap` space them. `Switch` spaces itself from the one
  above with `[&+&]:mt-4`.
- **Use a `<style>` block** only for what utilities can't say: styling markup
  a library or child component renders (`RichText`'s Quill toolbar), canvas
  and long animation sequences (`PoolBand`), and the classes a layout hands
  to every page in it (`AuthLayout`'s `.a-title`, `.a-lead`, `.a-form`,
  `.a-aside`; `FormPage`'s form and `.p-actions`). A `<style>` block is
  unlayered, so it beats every utility. Move a style into `v2.css` once a
  second page, Blade or Svelte, needs it.
- `manage.css` only scans `resources/js/manage`, `resources/js/pages/Manage`
  and the root view. Add `pages/Admin` to its `@source` list when the first
  Admin page lands.
- Every page, layout and component is on utilities, except the `<style>`
  uses above.

A choice between two kinds of the same thing in a form (New competition's organisation or Personal)
is a `.v2-tabs` (`--v2-tabs: 2`) with `role="tab"` buttons, each controlling a `role="tabpanel"` that
holds that kind's fields, so only the chosen tab's fields are sent. Arrow keys move between the tabs. The panels sit in a wrapper whose height animates to the chosen one's (250ms, off under reduced motion), and the new panel fades in, so the fields below don't jump.
Hide the tabs when only one kind is possible, and say which in a muted line instead.
`Tabs` does all of this: pass `tabs`, bind `value`, label it (`labelledby` or `label`) and render each
panel from its `panel` snippet. Tabs are for a choice within a form, not for moving between parts
of a settings page: use `SideMenu` for that.
On phones the tabs' padding tightens so five fit.

**Two-column pages.** A page with main content and a sidebar (a competition's Overview, Entries & Leagues, an event's page) is a `SidebarPage`: the main content as its children and the sidebar as its `sidebar` snippet, split 2:1 from `56rem` (`gap-10`), with nothing above the two columns. The page's own heading (and any back link or badges) goes at the top of the first column, so the sidebar starts at the top of the page beside it, and the heading stays in the same place whatever the first column shows. On phones the sidebar follows the content. The page picks the sidebar's element: a `<section>` with its own heading for a second list (Leagues), or a `<nav>` of actions for the content beside it, with `sticky` so it stays in view. Style a list of actions as whole rows with `sidebarRow` (exported by `SidebarPage`, a current one marked `aria-current="true"`) between `border-v2-border` rules, as on a speed event. An action that edits much of the page (its results, its settings) opens a page of its own rather than swapping the first column. Use `SideMenu` instead for settings split into several forms, like competition Settings.

**Slider or page tabs?** `.v2-tabs` is for a choice within a page. Links between pages (a competition's
Overview, Events, Results and so on) are `.v2-pagetabs`: muted text tabs with a teal underline under
the one marked `aria-current="page"`, on a bottom border, scrolling sideways without a scrollbar when
they don't fit.

Organisation logos go through `OrganisationLogoService` (PNG, JPEG or WebP up to 10 MB, scaled to fit
512px and stored as one transparent `.webp`), on the Svelte and Blade pages alike. Preview them round,
with `object-fit: contain`, as the site shows them.

Forms use Inertia's `<Form>` with `Field` and `SubmitButton`. A form that can
land on a Blade page or another domain (signing in, accepting an Invite)
answers with `Inertia::location()`, which does a full visit. A plain redirect
would try to load the Blade page as an Inertia response. Errors that aren't
about one field (wrong credentials, throttling) go on their own key (`auth`)
and show as a danger `Alert` above the fields.

Page changes between Svelte pages are view transitions: `manage.ts` turns `viewTransition` on for every
GET visit (not form posts, whose error answer is the same page), and `v2.css` keeps the `.v2-header` and
`.v2-footer` still while the page between fades and rises in (about 250ms, off under reduced motion).
Full page loads, like a form landing on a Blade page, don't animate. The theme toggle marks its own
transition with `data-theme-switching` on `<html>`, which swaps this for its circular reveal.

Svelte transitions don't check `prefers-reduced-motion` on their own: pass
`duration: prefersReducedMotion.current ? 0 : …` (from `svelte/motion`). Don't
use `<aside>` in the app: an old rule in `app.css` collapses it to zero width.

## Tokens

Defined on `:root` in `v2.css`. Always use the variable, never the hex, so
dark mode follows.

| Token | Light | Dark | Use |
|---|---|---|---|
| `--v2-bg` | `#f9fafb` | `#030712` | Page background, field backgrounds |
| `--v2-card` | `#ffffff` | `#111827` | Panels, cards, the status/modal surface |
| `--v2-sunk` | `#f3f4f6` | `#1f2937` | Neutral badges, tab track, row hover in lists |
| `--v2-ink` | `#111827` | `#f9fafb` | Text, primary button |
| `--v2-muted` | `#6b7280` | `#9ca3af` | Secondary text, past results, hints |
| `--v2-border` | `#e5e7eb` | `#1f2937` | Borders and row dividers |
| `--v2-teal` | `#00d5be` | `#00d5be` | The accent: live dot, today line, focus ring, accent button |
| `--v2-teal-text` | `#0f766e` | `#2dd4bf` | Teal *text* (links, "Live now"). `--v2-teal` is too light for small text on white |
| `--v2-tile` | `#111827` | `#111827` | The favicon tile colour, for dark bands that stay dark in both themes |

| `--v2-teal-soft` | teal 8% | teal 12% | Info alerts, live badge |
| `--v2-row-hover` | teal 8% | teal 14% | Whole-row link hover and focus |
| `--v2-danger`, `-soft`, `-text` | `#dc2626`, `#fef2f2`, `#b91c1c` | `#ef4444`, 12%, `#fca5a5` | DQs, deletes, errors |
| `--v2-success`, `-soft`, `-text` | `#059669`, `#ecfdf5`, `#047857` | `#10b981`, 12%, `#6ee7b7` | Approved, published |
| `--v2-warning-soft`, `-text` | `#fef3c7`, `#92400e` | same | Provisional |

## Dark mode

The page follows the OS, and the header toggle can force a theme by setting
`data-theme="light"` or `"dark"` on `<html>`. It's saved in `localStorage` as
`v2-theme`, and a script in `<head>` applies it before the first paint. So
**every dark rule is written twice**:

```css
@media (prefers-color-scheme: dark) {
    html:not([data-theme="light"]) {
        .my-thing { border: 1px solid var(--v2-border); }
    }
}

html[data-theme="dark"] {
    .my-thing { border: 1px solid var(--v2-border); }
}
```

Most things need neither block because they use tokens. Write one only when
dark needs something the tokens can't give: an extra border on a `--v2-tile`
band, a deeper photo overlay, a stronger hover tint. A token override
(`:root { … }`) inside the block becomes `& { … }`.

The toggle switches themes with a circular View Transitions reveal from the
button, and falls back to an instant switch.

## Typography

| Role | Face | Treatment |
|---|---|---|
| Body, UI | Archivo (self-hosted, `/fonts/google/fonts.css`) | 400–600 |
| Section headings | Archivo | 800, uppercase, `1.75rem`; page statements up to `clamp(2rem, 4.5vw, 3.25rem)` with `line-height: 1.05` |
| Panel and list headings | Archivo | 800, uppercase, `1–1.125rem` |
| Names in lists | Archivo | 700, sentence case. Set `text-transform: none`: the global `h2`/`h3` rules uppercase everything |
| Dates and numerals | Anton on the public v2 pages only | 400. Short dates like `27 Sep`, tabular. The Management app uses Archivo for dates and data (see "The Management app") |

On Blade pages, the global `h1`–`h4` rules in `app.css` are unlayered, so
they beat Tailwind utilities. Style headings with a page class, not
`normal-case`. The Management app doesn't load them (see Tailwind above).

Don't add other fonts. Use one heading level per section and no eyebrow labels
above headings.

## Components

All in `v2.css`, so they're available on every v2 page. The kit partial shows
each one in use; copy its markup.

| Need | Use |
|---|---|
| Button | `.v2-btn` (plain: card background, border). `.v2-btn-primary` for the one main action (ink, inverts in dark), `.v2-btn-accent` (teal, for a "create" call to action), `.v2-btn-danger`, `.v2-btn-ghost`, `.v2-btn-sm`, and `disabled` |
| Link | `.v2-link` (teal-text, 600, no wrap), `.v2-link-muted` (secondary), `.v2-link-inline` (underlined, inside a sentence) |
| Live / status dot | `<span class="v2-dot is-live" aria-hidden="true"></span>`. Drop `is-live` for a still dot |
| Search | `<x-search-all />`, re-skinned on the tokens. On a photo, give its input a fixed light surface (see `.f-hero` in `home.blade.php`) |
| Field | `div.v2-field` with a `<label>` and `.v2-input` (input, `select`, `textarea`). Add `.is-invalid` with `aria-invalid` and a `.v2-error` message linked by `aria-describedby`; `.v2-hint` for help text |
| Checkbox / radio | `<label class="v2-check"><input type="checkbox"> Text</label>` (native, teal `accent-color`) |
| Switch | `<label class="v2-check"><button class="v2-switch" role="switch" :aria-checked="…"></button><span>Text</span></label>`. The label lets the text toggle it |
| Badge | `.v2-badge` + `is-provisional`, `is-success`, `is-danger`, `is-live` |
| Alert | `.v2-alert` + `is-info`, `is-success`, `is-danger`, with an icon and a `<strong>` title |
| Tabs | `.v2-tabs` with a sliding `.v2-tabs-pill` (equal-width tabs; set `--v2-tabs` to the count if it isn't 3) |
| Toast | `.v2-toasts` > `.v2-toast-slot` > `.v2-toast-clip` > `.v2-toast`, stacked bottom-right, newest nearest the corner. They slide up in and slide out right, and the stack closes the gap. The Alpine that fires them is in the kit for now |
| Modal | `.v2-modal-backdrop` + `.v2-modal` with `x-trap.noscroll`: it traps Tab, locks page scroll and focuses the first button, so put the safe one first. Close on Escape and backdrop click. In Svelte, use a native `<dialog class="v2-modal">` opened with `showModal()` instead (it traps focus, closes on Escape and gets the backdrop from `v2.css`); close it on a click on the dialog itself |
| Whole-row link | `.v2-row` on the `<tr>`, `.v2-row-link` on its link, `.v2-over-row` on anything else clickable in it (see Patterns) |

## Patterns

- **Fixtures sheet** (`.f-sheet` in `home.blade.php`): one table in date
  order, with recent results above, a teal "Today" line, today's live
  competitions on it, and upcoming ones below.
  - **Past rows:** muted name and date, a Final/Provisional badge and "Results".
  - **Upcoming rows:** an "In N days" countdown and "Details".
  - **Live row:** the name takes the date column (the Today line already gives
    the date), with "Live now", "Details" and a primary **Follow live** button.
  - **On phones:** the venue moves under the name, and the logo and status
    columns hide.
- **Whole-row links:** `.v2-row` on the row, and `.v2-row-link` on its own
  link, whose `::after` covers the row. Anything else clickable in the row
  (like **Follow live**) gets `.v2-over-row` to sit above it. Add `sr-only` text so the link says what it's for ("Results for Cardiff
  Classic"). Hover and `:focus-within` tint the row teal and underline the link.
- **Live:** a pulsing teal dot plus the words "Live now". Never colour alone.
- **Empty states** say what will appear and when ("Results appear here once
  an organiser publishes them"), in a dashed-border box or a muted line.
- **Dates** read as people say them: "Tomorrow", "In 5 days", `Sunday 25
  October`. Results use `j M`.

## Behaviour

- **Interactivity is Alpine** on Blade pages, and Svelte in the Management
  app. The Blade layout loads Alpine with the focus plugin (`x-trap`), but not
  mask, collapse or persist. Add a plugin's `/vendor` script to the layout,
  before Alpine's, if a page needs it.
- **Pointer cursor:** Tailwind's base styles give buttons the default cursor,
  so every clickable control sets `cursor: pointer` (`.v2-btn` already does).
- **Focus:** `:focus-visible` shows a 2px teal outline (from the layout).
  Fields use a teal border and ring instead.
- **Motion:** keep animations to 150–500ms, use `transform`, `opacity` or
  `clip-path`, and turn them off under `prefers-reduced-motion`. Alpine's
  `x-for` removes items before a leave transition can run, so mark an item as
  leaving, then remove it (see the toasts).

## Writing

Sentence case, plain verbs, and buttons say what happens ("Follow live",
"Create an account", "Keep event"). Errors say what to do next ("Enter a full
email address, like name@club.org"). Use the glossary's terms (Event, Entity,
Official, Referee, Announcement) and avoid the words it lists as ones to avoid.

## Redesigning a page

1. Extend `landing.v2.layout`, not `layouts.core` or `layouts.guest`.
2. Rebuild the markup with the components above. Don't carry `se-` classes or
   Tailwind colour utilities (`text-gray-600`, `bg-white`) across: they don't
   follow dark mode. (In the Management app, use the `v2-` colour utilities
   instead; see Tailwind.)
3. Lead with what's happening now, and make list rows whole-row links.
4. Write any dark-only rule twice (OS block and `data-theme` block).
5. Check the page at 1440px and 390px, in light and dark, with live and empty
   data. The `demo-landing` competitions in a local database cover the live case.
6. Keep `noindex` until the page replaces its old version. Then delete the old
   view and its route.
7. If you add or change a shared component, token or pattern, put it in
   `v2.css`, add it to the kit, and update this guide in the same change.
