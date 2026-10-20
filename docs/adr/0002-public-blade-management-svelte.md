---
status: accepted
---

# Public pages stay Blade; Management and Admin move to Svelte as an app separate from the Digital Judge

With the v2 redesign under way we had to pick a frontend stack for the rest of the site. We split it by audience. Public pages (landing, explore, public results, live pages) stay Blade + Alpine on `landing.v2.layout`. Management (where Organisers edit competitions, Events, Entities, Officials and results) and Admin (the site-wide `/admin` area) move to Svelte 5 + Inertia, one page at a time, as each page gets its v2 redesign. They form one app, separate from the Digital Judge in everything but the backend: it has its own entry point, root view, layouts, components and style guide. Officials judging poolside on a phone and Organisers or Admins working at a desk have different needs, and a change to Management or Admin must never be able to break judging on the day.

Admin is a section of the Management app, not a third app. It's used in the same desk setting with the same kind of tables and forms, and it's small (seasons, users, speed records, creating competitions). A separate app would duplicate Management's components for little gain. It keeps its own `/admin` routes, `isAdmin` gate, nav and page tree, so the two areas stay clearly apart without separate code.

## Considered Options

- **Everything in Blade + Alpine.** Rejected for Management and Admin. The pages there are full of state (editable tables, modals, multi-step forms) and have outgrown Alpine: large `x-data` blobs and page-specific workarounds layered over `editable-table.js`.
- **Everything in Svelte.** Rejected for public pages. They're mostly read-only, need to load fast on a phone and should be indexable. Server-rendered Blade does that with no client bundle. Inertia would need SSR to match, which isn't worth the extra moving parts.
- **Management in Svelte, sharing the Digital Judge's shell and components.** Rejected. Judge components are built for one-handed phone use poolside: big targets, one task per screen, flaky Wi-Fi. Management needs dense tables and bulk editing on a laptop. Shared components would end up as compromises with a flag for each context. Shared code would also let a Management change break the Digital Judge in the middle of a competition.
- **One sign-in shared across subdomains (`SESSION_DOMAIN=.<base>`).** Rejected. It would tie the Digital Judge's sign-in to the main site's, and cookies on `.localhost` are unreliable in local dev. Instead every signed-in page moved to `app.`, so the main domain needs no session at all.
- **Admin as its own app, separate from Management.** Rejected for the reasons above. Revisit this if Admin grows its own kind of work that Management's components don't fit.

## Consequences

- **Two Inertia apps in one Laravel codebase, on their own subdomains.** The Digital Judge is `resources/js/app.ts` on `judge.`. Management has its own entry (`resources/js/manage.ts`), Blade root view and layouts, with pages under `pages/Manage/` and `pages/Admin/` and routes in `routes/app.php` on `app.`. `HandleInertiaRequests` chooses the root view and shared props by domain. Neither app imports from the other's `components/` or `layout/`.
- **Every signed-in page lives on `app.`, and the main domain is public only.** Sign in, register, password reset, account, invites, the dashboard (`/` on `app.`) and all Management and Admin pages are in `routes/app.php`, Blade or Svelte. Pages are ported to Svelte in place, so their URLs only changed once. The main domain sends the old paths (`/comps/…`, `/login`, `/admin/…` and so on) to the same path on `app.` with a 301 (`web.php`). Its `/` is always the public home page. Sign-out goes back to it. Public pages linked from `app.` use `RouteHelpers::mainRoute()`, because `route()` builds domain-less routes on the current host.
- **"Admin" in the code doesn't always mean Admin.** `can:access,comp,"admin"` and `can:access,organisation,"admin"` are Organiser permissions in Management. Only the `isAdmin` middleware means a site Admin. New code and props should say Organiser for the first and Admin only for the second.
- **Only the backend and the brand are shared.** Models, scoring, policies and Wayfinder routes stay common. The brand ("S." mark, ink/teal palette) stays recognisable, but the Management app takes its tokens from `docs/v2-ui-style-guide.md`, not the judge guide. The Digital Judge keeps `docs/judge-ui-style-guide.md`. Management gets its own Svelte guide when its first page lands.
- **Small primitives may be duplicated on purpose.** If a piece really is context-free (a date formatter, a type), put it in `resources/js/lib/`, not in either app's components.
- **Moving between apps is a full page load.** Someone who is both an Organiser and an Official switches apps with a full page load, the same way they leave Blade for either app. Accepted.
- **Blade stays permanent for public pages, PDFs and mail.** The guidance that "the judge guide doesn't apply to Blade" now also covers Management and Admin Svelte pages.
- **Live pages are the one open question.** They're public but real-time through Echo. They stay Blade unless their client-side state outgrows Alpine. If it does, they would be the only public pages to move.
