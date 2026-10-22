import { createInertiaApp, type ResolvedComponent } from "@inertiajs/svelte";
import AuthLayout from "./manage/layout/AuthLayout.svelte";
import CompetitionLayout from "./manage/layout/CompetitionLayout.svelte";
import ManageLayout from "./manage/layout/ManageLayout.svelte";

// The Management app (Management and Admin), separate from the Digital Judge in app.ts.
// See docs/adr/0002-public-blade-management-svelte.md.

const appName = "Scoring.Events";

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Only this app's pages, so none of the Digital Judge ends up in its bundle
    resolve: async (name) => {
        const pages = import.meta.glob<ResolvedComponent>(["./pages/Manage/**/*.svelte", "./pages/Admin/**/*.svelte"]);
        const module = await pages[`./pages/${name}.svelte`]?.();
        if (!module) throw new Error(`Page not found: ${name}`);
        return module;
    },
    // Sign in, register and invites get the split-screen frame instead of the header and footer, and a
    // competition's pages get its header and tabs inside the shell
    layout: (name) =>
        name.startsWith("Manage/Auth/")
            ? [AuthLayout]
            : name.startsWith("Manage/Competition/")
              ? [ManageLayout, CompetitionLayout]
              : [ManageLayout],
    defaults: {
        // Page changes animate (v2.css). Not form posts: an error answer is the same page, and shouldn't fade
        visitOptions: (_href, options) => ({ viewTransition: (options.method ?? "get") === "get" }),
    },
});
