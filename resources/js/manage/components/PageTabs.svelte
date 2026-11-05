<!--
    A competition's or organisation's page tabs (.v2-pagetabs), under its header in CompetitionLayout and
    OrganisationLayout (and the live pages' LiveLayout). A Svelte page's tab is an Inertia visit; a Blade
    one's loads in full, and a `newTab` one opens in a new tab. On phones the
    tabs scroll sideways, starting with the current one in view.
-->
<script lang="ts">
    import type { PageTab } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

    let { tabs, label }: { tabs: PageTab[]; /** Names the nav, e.g. "Competition" */ label: string } = $props();

    function showCurrent(nav: HTMLElement) {
        const current = nav.querySelector<HTMLElement>("[aria-current='page']");
        if (current) nav.scrollLeft = current.offsetLeft - (nav.clientWidth - current.offsetWidth) / 2;
    }
</script>

<nav class="v2-pagetabs" aria-label={label} use:showCurrent>
    {#each tabs as tab (tab.url)}
        {#if tab.newTab}
            <a href={tab.url} target="_blank" rel="noopener">{tab.label} <span aria-hidden="true">↗</span><span class="sr-only">(opens in a new tab)</span></a>
        {:else if tab.svelte}
            <Link href={tab.url} aria-current={tab.current ? "page" : undefined}>{tab.label}</Link>
        {:else}
            <a href={tab.url} aria-current={tab.current ? "page" : undefined}>{tab.label}</a>
        {/if}
    {/each}
</nav>
