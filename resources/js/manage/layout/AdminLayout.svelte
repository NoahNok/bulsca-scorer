<!--
    Every Admin page (pages/Admin/*), inside ManageLayout: the "Admin" title and Admin's page tabs. Every
    tab is a Svelte page, so the tabs live here rather than in a PHP helper like CompetitionTabs; a page's
    tab is the one whose pages its component name starts with (Admin/User is under Users).
-->
<script lang="ts">
    import PageTabs from "@/manage/components/PageTabs.svelte";
    import type { PageTab } from "@/manage/types";
    import { index as competitions } from "@/routes/manage/admin/competitions";
    import { page } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    let { children }: { children?: Snippet } = $props();

    const sections: { label: string; url: string; pages: string[] }[] = [
        { label: "Competitions", url: competitions().url, pages: ["Admin/Competition"] },
    ];

    const tabs: PageTab[] = $derived(
        sections.map((section) => ({
            label: section.label,
            url: section.url,
            current: section.pages.some((name) => page.component.startsWith(name)),
            svelte: true,
        })),
    );
</script>

<h1 class="text-[clamp(1.75rem,3.5vw,2.5rem)] leading-[1.1] font-extrabold uppercase">Admin</h1>

<PageTabs {tabs} label="Admin" />

<div class="mt-8">
    {@render children?.()}
</div>
