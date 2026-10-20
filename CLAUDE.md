# CLAUDE.md

## Digital Judge frontend (Svelte)

The judge app lives in `resources/js` (Svelte 5 + Inertia 3, Tailwind 4).

- **Follow `docs/judge-ui-style-guide.md` for all Svelte UI work** (only
  `resources/js`). That covers new pages, new components, and any page you
  touch. Reuse the shared
  components it lists before writing new markup. When an older page is in
  scope, migrate it using the checklist at the end of the guide.
- If you add or change a shared component, pattern or token, update the guide
  in the same change.
- Page chrome comes from the layouts (`layout/JudgeLayout.svelte`,
  `layout/JudgeHeaderLayout.svelte`), wired up in `app.ts`. Don't add the
  "Digital Judge" header or bottom nav to pages.
- Type-check with `npx svelte-check --threshold error`.

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
