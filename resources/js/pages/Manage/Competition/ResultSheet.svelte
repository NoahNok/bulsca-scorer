<!--
    A Results sheet's page (manage.competitions.results.show), in CompetitionLayout: "← Results", its name,
    then its ranking with a search (or what went wrong working it out). The sticky actions column has
    Edit, the "Shown in results" switch, the two print pages, Refresh cache for Admins and Delete (the
    name typed out, in a dialog naming any Master sheets it's taken out of), then how the sheet is built.
-->
<script lang="ts">
    import { destroy, edit, index, visibility } from "@/actions/App/Http/Controllers/Manage/ResultSheetsController";
    import ActionSwitch from "@/manage/components/ActionSwitch.svelte";
    import Alert from "@/manage/components/Alert.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import SidebarPage, { sidebarRow } from "@/manage/components/SidebarPage.svelte";
    import DeleteSheetDialog from "@/manage/results/DeleteSheetDialog.svelte";
    import UnconfirmedAlert from "@/manage/results/UnconfirmedAlert.svelte";
    import type { CompetitionHeader, UnconfirmedEvent } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        sheet,
        results,
        error,
        isAdmin,
        masters,
        printPlacesUrl,
        printDetailedUrl,
        canCreate,
        unconfirmed,
    }: {
        competition: CompetitionHeader;
        sheet: {
            id: number;
            name: string;
            viewable: boolean;
            /** Null for the whole competition */
            league: string | null;
            formula: string;
            events: { name: string; weight: number }[];
            /** Event names, in the order they break ties */
            breakTies: string[];
        };
        results: { key: string; name: string; points: number; position: number }[];
        /** Why it couldn't be worked out, with what the scoring engine knew */
        error: { message: string; context?: Record<string, unknown> } | null;
        isAdmin: boolean;
        /** Master sheets built on it */
        masters: string[];
        printPlacesUrl: string;
        printDetailedUrl: string;
        /** False while a Digitally Judged Event waits to be confirmed */
        canCreate: boolean;
        unconfirmed: UnconfirmedEvent[];
    } = $props();

    const comp = $derived(competition.id);

    let query = $state("");
    const shown = $derived(results.filter((row) => row.name.toLowerCase().includes(query.trim().toLowerCase())));

    function setViewable(viewable: boolean) {
        router.post(visibility({ comp, sheet: sheet.id }).url, { viewable }, { preserveScroll: true });
    }

    function refresh() {
        router.reload({ data: { refresh_cache: 1 } });
    }

    let deleteDialog: DeleteSheetDialog;

    const tableHead = "text-sm text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold";
    const subheading = "text-sm font-extrabold uppercase";
</script>

<svelte:head>
    <title>{sheet.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

<SidebarPage sticky>
    <PageHeading back={{ href: index(comp).url, label: "Results" }} title={sheet.name} lead={sheet.league ?? "Whole competition"}>
        {#snippet badges()}
            {#if !sheet.viewable}<span class="v2-badge" title="Left out of the public results">Hidden</span>{/if}
        {/snippet}
    </PageHeading>

    <section aria-label="Ranking" class="mt-5">
        {#if error}
            <Alert tone="danger" title="This sheet couldn't be worked out">
                <p class="mt-1">{error.message}</p>
                {#if error.context}
                    {#each Object.entries(error.context) as [key, value] (key)}
                        <details class="mt-2">
                            <summary class="cursor-pointer text-sm font-semibold">{key}</summary>
                            <pre class="mt-1 overflow-x-auto text-xs">{JSON.stringify(value, null, 2)}</pre>
                        </details>
                    {/each}
                {/if}
            </Alert>
        {:else}
            {#if results.length > 0}
                <input type="search" class="v2-input w-full" placeholder="Search entries" aria-label="Search the ranking" bind:value={query} />
            {/if}
            <div class="mt-4 overflow-x-auto">
                <table class="w-full border-t border-v2-border text-left">
                    <thead>
                        <tr class={tableHead}>
                            <th scope="col" class="w-px">#</th>
                            <th scope="col">Entry</th>
                            <th scope="col" class="text-right">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each shown as row (row.key)}
                            <tr class="*:border-b *:border-v2-border *:px-2 *:py-3">
                                <td class="font-semibold tabular-nums">{row.position}</td>
                                <th scope="row" class="font-bold wrap-anywhere">{row.name}</th>
                                <td class="text-right font-semibold tabular-nums">{row.points}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
                {#if results.length === 0}
                    <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">Nothing to rank yet. Entities appear here once its events have results.</p>
                {:else if shown.length === 0}
                    <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries match.</p>
                {/if}
            </div>
        {/if}
    </section>

    {#snippet sidebar()}
        <nav aria-label={sheet.name}>
            {#if canCreate}
                <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                    <li><Link href={edit({ comp, sheet: sheet.id }).url} class={sidebarRow}>Edit sheet<RowIcon /></Link></li>
                </ul>
            {:else}
                <UnconfirmedAlert {unconfirmed} />
            {/if}

            <ActionSwitch class="mt-5" label="Shown in results" checked={sheet.viewable} onchange={setViewable} />

            <ul class="mt-5 border-t border-v2-border *:border-b *:border-v2-border">
                <li>
                    <a href={printPlacesUrl} target="_blank" rel="noopener" class={sidebarRow}>
                        <span>Print places<span class="block text-sm font-normal text-v2-muted">Final places and points only</span></span>
                        <RowIcon icon="external" />
                    </a>
                </li>
                <li>
                    <a href={printDetailedUrl} target="_blank" rel="noopener" class={sidebarRow}>
                        <span>Print detailed<span class="block text-sm font-normal text-v2-muted">Every event's places and points, with the totals</span></span>
                        <RowIcon icon="external" />
                    </a>
                </li>
                {#if isAdmin}
                    <li><button type="button" class={sidebarRow} onclick={refresh}>Refresh cache</button></li>
                {/if}
                <li>
                    <button type="button" class="{sidebarRow} text-v2-danger-text" onclick={() => deleteDialog.open()}>Delete sheet</button>
                </li>
            </ul>

            <section aria-labelledby="rs-built" class="mt-8">
                <h2 id="rs-built" class={subheading}>How it's built</h2>
                <dl class="mt-3 flex flex-col gap-3 text-[0.9375rem]/[1.5] *:flex *:flex-col">
                    <div>
                        <dt class="text-sm text-v2-muted">Formula</dt>
                        <dd class="font-semibold">{sheet.formula}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-v2-muted">Events</dt>
                        {#each sheet.events as event, i (i)}
                            <dd class="flex justify-between gap-3"><span class="wrap-anywhere">{event.name}</span><span class="text-v2-muted tabular-nums">×{event.weight}</span></dd>
                        {:else}
                            <dd>None</dd>
                        {/each}
                    </div>
                    <div>
                        <dt class="text-sm text-v2-muted">Ties broken by</dt>
                        <dd>{sheet.breakTies.length > 0 ? sheet.breakTies.join(", then ") : "Nothing, so ties are shared"}</dd>
                    </div>
                </dl>
            </section>
        </nav>
    {/snippet}
</SidebarPage>

<DeleteSheetDialog
    bind:this={deleteDialog}
    name={sheet.name}
    action={destroy({ comp, sheet: sheet.id })}
    {masters}
/>
