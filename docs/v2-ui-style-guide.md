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
| Root view | `resources/views/manage.blade.php` | The theme script, fonts, `app.css` + `v2.css` and `manage.ts` |
| Entry | `resources/js/manage.ts` | The Inertia app. Resolves only `pages/Manage/**` and `pages/Admin/**` (`app.ts` leaves them out) |
| Shell | `resources/js/manage/layout/ManageLayout.svelte` | The same `.v2-header` and `.v2-footer` as the Blade layout, with Explore, Admin (for Admins), Sign out and the theme toggle, and a `.v2-container` for the page |
| Signed-out frame | `resources/js/manage/layout/AuthLayout.svelte` | Used instead of the shell for `pages/Manage/Auth/*` (sign in, register, forgot and reset password, Invites). The `PoolBand` (one lane with its big ghosted number; the mouse picks the lane by height and swims a top-down swimmer along it by width, with bubbles and ripples on a canvas behind it. It treads water when the mouse stops, rarely dives for a few strokes, and stays under while a password field has focus) beside the form on desktop (phones get the brand mark and name in the top bar instead), the theme toggle, the session flashes as alerts, and the legal links. It holds each page's band line and the `.a-title`, `.a-lead`, `.a-form` and `.a-aside` classes the pages use |
| Components | `resources/js/manage/components/` | `BrandMark` (the Svelte copy of `mark.blade.php`), `ThemeToggle`, `Field` (a `.v2-field` with its error and hint wired up, and show/hide on passwords), `Alert` (a `.v2-alert` with its icon), `SubmitButton` (full-width, spinner and disabled while processing), `LegalNotice` (the policy line under forms that sign in or create an account), `PoolBand` and `CompetitionTable` (competitions an Organiser can manage, as whole-row "Manage" links: a muted Archivo date, the name (with a muted organisation name when given), and a status of Live now, Today or "In N days" plus Private and Limited access badges beside the link; no venue; past rows are muted). Their prop types are in `resources/js/manage/types.ts`. The app's own components go here |
| Routes | `routes/app.php` | Every signed-in page, Blade or Svelte, on the `app.` subdomain, like the Digital Judge on `judge.`. `HandleInertiaRequests` gives every Inertia page on that domain the `manage` root view, and shares `manage.isAdmin`, `manage.site.home` and `.explore` (the public site's urls) and `manage.flash` (the `status` and `alert-error` session flashes) in place of `judge`. Name new routes `manage.*` (Admin pages `manage.admin.*`). The app's home (`manage.home`, `pages/Manage/Home.svelte`) is at `/`: Recent cards, then one `CompetitionTable` of everything you can manage with a pill filter per organisation (yours, then ones you were invited into) and Personal, showing upcoming and the 3 latest past until "Show all". `/dashboard` redirects to it |

Links from `app.` to public pages use `RouteHelpers::mainRoute()` in Blade
and `manage.site` in Svelte. `route()` would build them on `app.`. Link to a
Blade page with a plain `<a>`, not Inertia's `<Link>`, which can only load
Inertia pages.

A Svelte page uses the same markup and classes as the kit. Put styles only it
uses in its `<style>` block, and move them into `v2.css` once a second page,
Blade or Svelte, needs them.

Forms use Inertia's `<Form>` with `Field` and `SubmitButton`. A form that can
land on a Blade page or another domain (signing in, accepting an Invite)
answers with `Inertia::location()`, which does a full visit. A plain redirect
would try to load the Blade page as an Inertia response. Errors that aren't
about one field (wrong credentials, throttling) go on their own key (`auth`)
and show as a danger `Alert` above the fields.

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
| Dates and numerals | Anton | 400. Short dates like `27 Sep`, tabular |

The global `h1`–`h4` rules in `app.css` are unlayered, so they beat Tailwind
utilities. Style headings with a page class, not `normal-case`.

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
   follow dark mode.
3. Lead with what's happening now, and make list rows whole-row links.
4. Write any dark-only rule twice (OS block and `data-theme` block).
5. Check the page at 1440px and 390px, in light and dark, with live and empty
   data. The `demo-landing` competitions in a local database cover the live case.
6. Keep `noindex` until the page replaces its old version. Then delete the old
   view and its route.
7. If you add or change a shared component, token or pattern, put it in
   `v2.css`, add it to the kit, and update this guide in the same change.
