<!--
    A competition's Results (manage.competitions.results), in CompetitionLayout: its Results sheets, then
    its Master sheets, each a whole-row link to its page. The sticky actions column makes new sheets,
    prints them all, and publishes and finalises the results. While a Digitally Judged Event waits for the
    Referee, a danger alert names it and no sheet can be made or edited.
-->
<script lang="ts">
    import { create, finalise, publish, show } from "@/actions/App/Http/Controllers/Manage/ResultSheetsController";
    import { create as createMaster, show as showMaster } from "@/actions/App/Http/Controllers/Manage/MasterSheetsController";
    import ActionSwitch from "@/manage/components/ActionSwitch.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import SidebarPage, { sidebarRow } from "@/manage/components/SidebarPage.svelte";
    import UnconfirmedAlert from "@/manage/results/UnconfirmedAlert.svelte";
    import type { CompetitionHeader, ResultSheetRow, UnconfirmedEvent } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        sheets,
        masters,
        published,
        provisional,
        private: isPrivate,
        publicUrl,
        printAllUrl,
        canCreate,
        unconfirmed,
    }: {
        competition: CompetitionHeader;
        sheets: ResultSheetRow[];
        masters: { id: number; name: string; viewable: boolean }[];
        published: boolean;
        provisional: boolean;
        /** Hidden from the public site, so its results are only reached by link */
        private: boolean;
        publicUrl: string;
        printAllUrl: string;
        /** False while a Digitally Judged Event waits to be confirmed */
        canCreate: boolean;
        unconfirmed: UnconfirmedEvent[];
    } = $props();

    const comp = $derived(competition.id);

    function setPublished(value: boolean) {
        router.post(publish(comp).url, { published: value }, { preserveScroll: true });
    }

    function setFinal(value: boolean) {
        router.post(finalise(comp).url, { provisional: !value }, { preserveScroll: true });
    }

    const heading = "text-lg font-extrabold uppercase";
    const list = "border-t border-v2-border *:border-b *:border-v2-border";
</script>

<svelte:head>
    <title>Results - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet sheetRow(href: string, name: string, detail: string | null, viewable: boolean)}
    <li>
        <Link {href} class={sidebarRow}>
            <span class="min-w-0 wrap-anywhere">{name}</span>
            {#if detail}<span class="text-sm font-normal text-v2-muted">{detail}</span>{/if}
            {#if !viewable}<span class="v2-badge" title="Left out of the public results">Hidden</span>{/if}
            <RowIcon />
        </Link>
    </li>
{/snippet}

<SidebarPage sticky>
    <div class="flex flex-col gap-10">
        {#if !canCreate}
            <UnconfirmedAlert {unconfirmed} />
        {/if}

        <section aria-labelledby="rs-sheets">
            <h2 id="rs-sheets" class="mb-1 {heading}">Results sheets</h2>
            <p class="mb-4 text-[0.9375rem]/[1.6] text-v2-muted">Overall rankings, for the whole competition or a League, from the events you pick.</p>
            {#if sheets.length > 0}
                <ul class={list}>
                    {#each sheets as sheet (sheet.id)}
                        {@render sheetRow(show({ comp, sheet: sheet.id }).url, sheet.name, sheet.league ?? "Whole competition", sheet.viewable)}
                    {/each}
                </ul>
            {:else}
                <EmptyState>
                    No Results sheets yet. Make one to rank Entities across your events.
                    {#if canCreate}
                        <div class="mt-4"><Link href={create(comp).url} class="v2-btn">New Results sheet</Link></div>
                    {/if}
                </EmptyState>
            {/if}
        </section>

        <section aria-labelledby="rs-masters">
            <h2 id="rs-masters" class="mb-1 {heading}">Master sheets</h2>
            <p class="mb-4 text-[0.9375rem]/[1.6] text-v2-muted">Rank Clubs by combining Results sheets, like an overall from each League's sheet.</p>
            {#if masters.length > 0}
                <ul class={list}>
                    {#each masters as master (master.id)}
                        {@render sheetRow(showMaster({ comp, master: master.id }).url, master.name, null, master.viewable)}
                    {/each}
                </ul>
            {:else}
                <p class="text-[0.9375rem]/[1.6] text-v2-muted">No Master sheets yet.</p>
            {/if}
        </section>
    </div>

    {#snippet sidebar()}
        <nav aria-label="Results">
            <ul class={list}>
                {#if canCreate}
                    <li>
                        <Link href={create(comp).url} class={sidebarRow}>New Results sheet<RowIcon /></Link>
                    </li>
                    <li>
                        <Link href={createMaster(comp).url} class={sidebarRow}>New Master sheet<RowIcon /></Link>
                    </li>
                {/if}
                <li>
                    <a href={printAllUrl} target="_blank" rel="noopener" class={sidebarRow}>Print all sheets<RowIcon icon="external" /></a>
                </li>
            </ul>

            <ActionSwitch class="mt-5" label="Results published" checked={published} onchange={setPublished} />
            {#if published}
                <a href={publicUrl} target="_blank" rel="noopener" class="v2-link mt-2 ml-1 inline-block text-sm">View public results</a>
            {/if}
            {#if isPrivate}
                <p class="mt-2 text-sm/[1.6] text-v2-muted">This competition is private, so its results can only be reached by link once published.</p>
            {/if}

            <ActionSwitch class="mt-5" label="Results final" checked={!provisional} onchange={setFinal} />
            <p class="mt-2 text-sm/[1.6] text-v2-muted">
                {provisional ? "Results say they're provisional until you finalise them. Finalising also lets people download them as CSVs." : "Results no longer say they're provisional, and can be downloaded as CSVs."}
            </p>
        </nav>
    {/snippet}
</SidebarPage>
