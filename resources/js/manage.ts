import { createInertiaApp, type ResolvedComponent } from "@inertiajs/svelte";
import AuthLayout from "./manage/layout/AuthLayout.svelte";
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
    // Sign in, register and invites get the split-screen frame instead of the header and footer
    layout: (name) => (name.startsWith("Manage/Auth/") ? [AuthLayout] : [ManageLayout]),
});
