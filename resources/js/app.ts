
import { createInertiaApp, type ResolvedComponent } from "@inertiajs/svelte";
import JudgeHeaderLayout from "./layout/JudgeHeaderLayout.svelte";
import JudgeLayout from "./layout/JudgeLayout.svelte";
//import "./bootstrap";

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();

const appName = "DigitalJudge";

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Leave out the Management app's pages (manage.ts), so they don't end up in this bundle
    resolve: async (name) => {
        const pages = import.meta.glob<ResolvedComponent>(["./pages/**/*.svelte", "!./pages/Manage/**", "!./pages/Admin/**"]);
        const module = await pages[`./pages/${name}.svelte`]?.();
        if (!module) throw new Error(`Page not found: ${name}`);
        return module;
    },
    layout: (name) => {
        if (name.startsWith("Judge/")) {
            return [JudgeLayout, JudgeHeaderLayout];
        }

        return [JudgeLayout];
    },
})





