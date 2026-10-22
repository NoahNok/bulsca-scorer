<!--
    Every competition page in the Management app (pages/Manage/Competition/*), inside ManageLayout: the
    competition's name, date, venue and badges, its page tabs (.v2-pagetabs) and the Organisers' status
    message. It reads the page's `competition` prop (App\Helpers\CompetitionHeader::for), so a page
    only has to send that.
-->
<script lang="ts">
    import { shortDate } from "@/lib/dates";
    import type { CompetitionHeader } from "@/manage/types";
    import { Link, page } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    let { children }: { children?: Snippet } = $props();

    const comp = $derived(page.props.competition as CompetitionHeader);

    // On phones the tabs scroll sideways, so start with the current one in view
    function showCurrent(nav: HTMLElement) {
        const current = nav.querySelector<HTMLElement>("[aria-current='page']");
        if (current) nav.scrollLeft = current.offsetLeft - (nav.clientWidth - current.offsetWidth) / 2;
    }
</script>

<header class="flex items-start justify-between gap-6">
    <div class="min-w-0">
        <h1 class="text-[clamp(1.75rem,3.5vw,2.5rem)] leading-[1.1] font-extrabold wrap-anywhere uppercase">{comp.name}</h1>
        <p class="mt-[0.6rem] flex flex-wrap items-center gap-x-[0.9rem] gap-y-[0.35rem] text-[0.9375rem] text-v2-muted">
            <span class="font-semibold text-v2-ink">{shortDate(comp.when)}</span>
            <span>{comp.where}</span>
            {#if comp.championship}
                <a href={comp.championship.url} class="v2-link-muted">{comp.championship.name}</a>
            {/if}
            {#if comp.live}
                <span class="v2-badge is-live"><span class="v2-dot is-live" aria-hidden="true"></span>Live now</span>
            {/if}
            {#if comp.private}
                <span class="v2-badge" title="Hidden from Explore and search">Private</span>
            {/if}
        </p>
    </div>
    {#if comp.organisation}
        <a href={comp.organisation.url} class="shrink-0" title={comp.organisation.name}>
            {#if comp.organisation.logo}
                <img
                    src={comp.organisation.logo}
                    alt="{comp.organisation.name}'s logo"
                    class="size-14 rounded-full border border-v2-border object-cover max-sm:size-11"
                />
            {:else}
                <span class="font-semibold text-v2-muted">{comp.organisation.name}</span>
            {/if}
        </a>
    {/if}
</header>

{#key comp.id}
    <nav class="v2-pagetabs" aria-label="Competition" use:showCurrent>
        {#each comp.tabs as tab (tab.url)}
            {#if tab.svelte}
                <Link href={tab.url} aria-current={tab.current ? "page" : undefined}>{tab.label}</Link>
            {:else}
                <a href={tab.url} aria-current={tab.current ? "page" : undefined}>{tab.label}</a>
            {/if}
        {/each}
    </nav>
{/key}

{#if comp.statusMessage}
    <div class="v2-alert is-info mt-5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="9" />
            <path d="M12 11v5M12 8h.01" />
        </svg>
        <!-- Written by this competition's Organisers in Settings, and only shown to them -->
        <div class="[&_a]:text-v2-teal-text [&_a]:underline">{@html comp.statusMessage}</div>
    </div>
{/if}

<div class="mt-8">
    {@render children?.()}
</div>
