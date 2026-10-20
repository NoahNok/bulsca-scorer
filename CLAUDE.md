# CLAUDE.md

## Digital Judge frontend (Svelte)

The judge app lives in `resources/js` (Svelte 5 + Inertia 3, Tailwind 4).

- **Follow `docs/judge-ui-style-guide.md` for all Digital Judge Svelte UI
  work** (only `resources/js`). That covers new pages, new components, and any page you
  touch. Reuse the shared
  components it lists before writing new markup. When an older page is in
  scope, migrate it using the checklist at the end of the guide.
- If you add or change a shared component, pattern or token, update the guide
  in the same change.
- Page chrome comes from the layouts (`layout/JudgeLayout.svelte`,
  `layout/JudgeHeaderLayout.svelte`), wired up in `app.ts`. Don't add the
  "Digital Judge" header or bottom nav to pages.
- Type-check with `npx svelte-check --threshold error`.

## Management and Admin (Svelte, separate app)

Management (Organisers editing competitions, events, officials) and Admin
(site-wide `/admin`) are moving from Blade to Svelte + Inertia as one app,
separate from the Digital Judge. See
`docs/adr/0002-public-blade-management-svelte.md`.

- Don't import judge components or layouts into Management or Admin pages,
  and don't import Management code into the judge app. Only `resources/js/lib/`
  and the backend are shared.
- Use the v2 tokens (`docs/v2-ui-style-guide.md`, "The Management app"), not
  the judge guide. Entry `resources/js/manage.ts`, root view
  `resources/views/manage.blade.php`, pages in `resources/js/pages/Manage/` and
  `pages/Admin/`, app components in `resources/js/manage/`.
- Every signed-in page (Blade or Svelte, sign-in included) lives in
  `routes/app.php` on the `app.` subdomain, like the Digital Judge on
  `judge.`. The main domain is public only. Don't add signed-in routes to
  `web.php`, and add a new top-level path to the 301 redirect list in
  `web.php` if one moves. Every Inertia page on `app.` gets the Management
  root view and shared props. Name new routes `manage.*` (Admin
  `manage.admin.*`).
- Link from `app.` to public pages with `RouteHelpers::mainRoute()`, not
  `route()`, which builds domain-less routes on the current host.
- Tests that hit signed-in pages by path use `$this->appUrl('/path')`.
- Port a page only as part of its v2 redesign. Public pages stay Blade.
- Say Organiser for competition/organisation `"admin"` access, and Admin only
  for `isAdmin`. See `GLOSSARY.md`.

## Main site (Blade)

Organiser pages (competitions, events, entries, results, officials) live in
`resources/views` (Blade + Alpine, global classes in `resources/css/app.css`).

- **Follow `docs/main-site-ui-style-guide.md` for Blade UI work.** The judge
  guide doesn't apply here, and judge components and patterns don't belong in
  Blade views.
- If you add or change a shared Blade component, layout tab or global class,
  update that guide in the same change.

### v2 redesign

Pages being redesigned onto `landing.v2.layout` (starting with `/v2`,
`resources/views/landing/v2/`) follow `docs/v2-ui-style-guide.md` instead of
the main-site guide: light/dark tokens, no `se-` classes. Update that guide
when you add or change a v2 component, token or pattern.

## frontend-design plugin

- The `frontend-design` plugin is secondary to the style guides: use it only
  for decisions the guides don't cover, and never introduce new fonts, colours
  or components outside the guide's tokens.

## Agent skills

### Issue tracker

Issues live in GitHub Issues on `NoahNok/bulsca-scorer`, managed with the `gh` CLI. See `docs/agents/issue-tracker.md`.

### Triage labels

The five default triage labels (`needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`). See `docs/agents/triage-labels.md`.

### Domain docs

Single-context: one `GLOSSARY.md` and `docs/adr/` at the repo root. See `docs/agents/domain.md`.
