# CLAUDE.md

## Digital Judge frontend (Svelte)

The judge app lives in `resources/js` (Svelte 5 + Inertia 3, Tailwind 4).

- **Follow `docs/judge-ui-style-guide.md` for all Svelte UI work.** That covers
  new pages, new components, and any page you touch. Reuse the shared
  components it lists before writing new markup. When an older page is in
  scope, migrate it using the checklist at the end of the guide.
- If you add or change a shared component, pattern or token, update the guide
  in the same change.
- Page chrome comes from the layouts (`layout/JudgeLayout.svelte`,
  `layout/JudgeHeaderLayout.svelte`), wired up in `app.ts`. Don't add the
  "Digital Judge" header or bottom nav to pages.
- Type-check with `npx svelte-check --threshold error`.
