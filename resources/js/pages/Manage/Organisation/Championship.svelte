<!--
    A Championship's page (manage.organisations.championships.show, Organisers only), in OrganisationLayout:
    its name and dates under a link back to Championships, then its competitions in date order, each with
    its type and Remove. Add competition opens a dialog to pick one of the organisation's competitions
    not in a championship yet, and its type. Delete (confirmed) keeps its competitions, as standalone ones.
-->
<script lang="ts">
    import { addCompetition, destroy, index, removeCompetition } from "@/actions/App/Http/Controllers/Manage/ChampionshipController";
    import { confirm } from "@/lib/confirm";
    import { shortDate } from "@/lib/dates";
    import CompetitionTable from "@/manage/components/CompetitionTable.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import type { ManagedCompetition, OrganisationHeader } from "@/manage/types";
    import { closeOnBackdrop } from "@/manage/ui";
    import { Form, router } from "@inertiajs/svelte";

    type Entry = ManagedCompetition & { type: string };

    let {
        organisation,
        championship,
        competitions,
        available,
        types,
    }: {
        organisation: OrganisationHeader;
        championship: { id: number; name: string; start: string; end: string };
        competitions: Entry[];
        /** The organisation's competitions not in a championship, soonest first */
        available: { id: number; name: string; when: string }[];
        /** Type key to its name: Regional, Qualifier, Final */
        types: Record<string, string>;
    } = $props();

    const ids = $derived({ organisation: organisation.id, championship: championship.id });

    let addDialog: HTMLDialogElement;

    async function remove(comp: Entry) {
        const ok = await confirm({
            title: `Remove ${comp.name}?`,
            description: `It leaves ${championship.name} and goes back to being a standalone competition. Nothing in it is deleted.`,
            confirmLabel: "Remove",
            confirmVariant: "danger",
        });
        if (ok) router.delete(removeCompetition({ ...ids, competition: comp.id }).url, { preserveScroll: true });
    }

    async function deleteChampionship() {
        const ok = await confirm({
            title: `Delete ${championship.name}?`,
            description:
                competitions.length > 0
                    ? `Its ${competitions.length === 1 ? "competition stays" : `${competitions.length} competitions stay`}, as standalone ones. It can't be undone.`
                    : "It can't be undone.",
            confirmLabel: "Delete championship",
            confirmVariant: "danger",
        });
        if (ok) router.delete(destroy(ids).url);
    }
</script>

<svelte:head>
    <title>{championship.name} - Championships - {organisation.name} - Scoring.Events</title>
</svelte:head>

<PageHeading
    back={{ href: index(organisation.id).url, label: "Championships" }}
    title={championship.name}
    lead="{shortDate(championship.start)} – {shortDate(championship.end)}"
>
    {#snippet actions()}
        <div class="flex flex-wrap gap-2">
            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm text-v2-danger-text" onclick={deleteChampionship}>Delete</button>
            {#if available.length > 0 && competitions.length > 0}
                <button type="button" class="v2-btn v2-btn-sm" onclick={() => addDialog.showModal()}>Add competition</button>
            {/if}
        </div>
    {/snippet}
</PageHeading>

{#if competitions.length > 0}
    <CompetitionTable {competitions}>
        {#snippet actions(comp)}
            <span class="v2-badge">{types[comp.type] ?? comp.type}</span>
            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm v2-over-row text-v2-danger-text" onclick={() => remove(comp)}>
                Remove<span class="sr-only"> {comp.name}</span>
            </button>
        {/snippet}
    </CompetitionTable>
{:else}
    <EmptyState>
        {#if available.length > 0}
            <p>No competitions in {championship.name} yet.</p>
            <button type="button" class="v2-btn mt-4" onclick={() => addDialog.showModal()}>Add competition</button>
        {:else}
            <p>No competitions in {championship.name} yet. Once {organisation.name} has a competition that isn't in a championship, you can add it here.</p>
        {/if}
    </EmptyState>
{/if}

<dialog bind:this={addDialog} class="v2-modal" aria-labelledby="ch-add-title" onclick={closeOnBackdrop}>
    <h2 id="ch-add-title">Add competition</h2>
    <p>Only {organisation.name}'s competitions that aren't in a championship yet.</p>

    <Form action={addCompetition(ids)} options={{ preserveScroll: true }} resetOnSuccess onSuccess={() => addDialog.close()}>
        {#snippet children({ errors, processing })}
            <div class="v2-field">
                <label for="ch-add-competition">Competition</label>
                <select
                    id="ch-add-competition"
                    name="competition_id"
                    class="v2-input w-full"
                    aria-invalid={errors.competition_id ? "true" : undefined}
                    aria-describedby={errors.competition_id ? "ch-add-competition-error" : undefined}
                >
                    {#each available as comp (comp.id)}
                        <option value={comp.id}>{comp.name}, {shortDate(comp.when)}</option>
                    {/each}
                </select>
                {#if errors.competition_id}<span id="ch-add-competition-error" class="v2-error">{errors.competition_id}</span>{/if}
            </div>
            <div class="v2-field">
                <label for="ch-add-type">Type</label>
                <select id="ch-add-type" name="type" class="v2-input w-full">
                    {#each Object.entries(types) as [key, label] (key)}
                        <option value={key}>{label}</option>
                    {/each}
                </select>
                {#if errors.type}<span class="v2-error">{errors.type}</span>{/if}
            </div>
            <div class="v2-modal-actions">
                <button type="button" class="v2-btn" onclick={() => addDialog.close()}>Cancel</button>
                <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Add</button>
            </div>
        {/snippet}
    </Form>
</dialog>
