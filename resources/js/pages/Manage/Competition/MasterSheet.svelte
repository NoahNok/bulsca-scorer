<!--
    A Master sheet's page (manage.competitions.results.master.show), in CompetitionLayout: "← Results", its
    name, then each Club's value from every Results sheet in it, its total and its place. The sticky
    actions column has Edit, the "Shown in results" switch and Delete, then how the sheet is built.
-->
<script lang="ts">
    import { destroy, edit, visibility } from "@/actions/App/Http/Controllers/Manage/MasterSheetsController";
    import { index } from "@/actions/App/Http/Controllers/Manage/ResultSheetsController";
    import ActionSwitch from "@/manage/components/ActionSwitch.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import SidebarPage, { sidebarRow } from "@/manage/components/SidebarPage.svelte";
    import DeleteSheetDialog from "@/manage/results/DeleteSheetDialog.svelte";
    import UnconfirmedAlert from "@/manage/results/UnconfirmedAlert.svelte";
    import type { CompetitionHeader, UnconfirmedEvent } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        master,
        results,
        canCreate,
        unconfirmed,
    }: {
        competition: CompetitionHeader;
        master: {
            id: number;
            name: string;
            viewable: boolean;
            /** Position or Points */
            sumOver: string;
            /** What a Club missing from a sheet gets */
            defaultValue: number;
            sheets: { id: number; name: string }[];
            /** Club names */
            excluded: string[];
        };
        /** `sheetResults` follows `master.sheets` */
        results: { key: string; name: string; sheetResults: number[]; total: number; position: number }[];
        /** False while a Digitally Judged Event waits to be confirmed */
        canCreate: boolean;
        unconfirmed: UnconfirmedEvent[];
    } = $props();

    const comp = $derived(competition.id);

    function setViewable(viewable: boolean) {
        router.post(visibility({ comp, master: master.id }).url, { viewable }, { preserveScroll: true });
    }

    let deleteDialog: DeleteSheetDialog;

    const tableHead = "text-sm text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold";
</script>

<svelte:head>
    <title>{master.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

<SidebarPage sticky>
    <PageHeading back={{ href: index(comp).url, label: "Results" }} title={master.name} lead="Clubs, by {master.sumOver.toLowerCase()} summed across its Results sheets">
        {#snippet badges()}
            {#if !master.viewable}<span class="v2-badge" title="Left out of the public results">Hidden</span>{/if}
        {/snippet}
    </PageHeading>

    <div class="mt-5 overflow-x-auto">
        <table class="w-full border-t border-v2-border text-left">
            <thead>
                <tr class={tableHead}>
                    <th scope="col" class="w-px">#</th>
                    <th scope="col">Club</th>
                    {#each master.sheets as sheet (sheet.id)}
                        <th scope="col" class="text-right">{sheet.name}</th>
                    {/each}
                    <th scope="col" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                {#each results as row (row.key)}
                    <tr class="*:border-b *:border-v2-border *:px-2 *:py-3">
                        <td class="font-semibold tabular-nums">{row.position}</td>
                        <th scope="row" class="font-bold wrap-anywhere">{row.name}</th>
                        {#each row.sheetResults as value, i (i)}
                            <td class="text-right text-v2-muted tabular-nums">{value}</td>
                        {/each}
                        <td class="text-right font-semibold tabular-nums">{row.total}</td>
                    </tr>
                {/each}
            </tbody>
        </table>
        {#if results.length === 0}
            <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No Clubs to rank yet.</p>
        {/if}
    </div>

    {#snippet sidebar()}
        <nav aria-label={master.name}>
            {#if canCreate}
                <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                    <li><Link href={edit({ comp, master: master.id }).url} class={sidebarRow}>Edit sheet<RowIcon /></Link></li>
                </ul>
            {:else}
                <UnconfirmedAlert {unconfirmed} />
            {/if}

            <ActionSwitch class="mt-5" label="Shown in results" checked={master.viewable} onchange={setViewable} />

            <ul class="mt-5 border-t border-v2-border *:border-b *:border-v2-border">
                <li>
                    <button type="button" class="{sidebarRow} text-v2-danger-text" onclick={() => deleteDialog.open()}>Delete sheet</button>
                </li>
            </ul>

            <section aria-labelledby="ms-built" class="mt-8">
                <h2 id="ms-built" class="text-sm font-extrabold uppercase">How it's built</h2>
                <dl class="mt-3 flex flex-col gap-3 text-[0.9375rem]/[1.5] *:flex *:flex-col">
                    <div>
                        <dt class="text-sm text-v2-muted">Sums</dt>
                        <dd class="font-semibold">{master.sumOver}, {master.sumOver === "Position" ? "lowest" : "highest"} wins</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-v2-muted">Missing from a sheet</dt>
                        <dd>Counts as {master.defaultValue}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-v2-muted">Left out</dt>
                        <dd>{master.excluded.length > 0 ? master.excluded.join(", ") : "No Clubs"}</dd>
                    </div>
                </dl>
            </section>
        </nav>
    {/snippet}
</SidebarPage>

<DeleteSheetDialog bind:this={deleteDialog} name={master.name} action={destroy({ comp, master: master.id })} />
