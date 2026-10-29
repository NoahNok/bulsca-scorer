<!--
    An organisation's Championships (manage.organisations.championships, Organisers only), in
    OrganisationLayout: each with its dates and how many competitions are in it, a whole-row link to its
    page. New championship opens a dialog for its name and dates, and lands on its page.
-->
<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/Manage/ChampionshipController";
    import { shortDate } from "@/lib/dates";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import Field from "@/manage/components/Field.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import type { OrganisationHeader } from "@/manage/types";
    import { closeOnBackdrop } from "@/manage/ui";
    import { Form, Link } from "@inertiajs/svelte";

    let {
        organisation,
        championships,
    }: {
        organisation: OrganisationHeader;
        /** Latest first */
        championships: { id: number; name: string; start: string; end: string; competitions: number; url: string }[];
    } = $props();

    let newDialog: HTMLDialogElement;
</script>

<svelte:head>
    <title>Championships - {organisation.name} - Scoring.Events</title>
</svelte:head>

<section aria-labelledby="c-heading" class="max-w-3xl">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 id="c-heading" class="text-lg font-extrabold uppercase">Championships</h2>
        {#if championships.length > 0}
            <button type="button" class="v2-btn v2-btn-sm" onclick={() => newDialog.showModal()}>New championship</button>
        {/if}
    </div>
    <p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">
        {organisation.name}'s competitions grouped over a season, each a Regional, Qualifier or Final.
    </p>

    {#if championships.length > 0}
        <table class="w-full border-collapse">
            <thead class="sr-only">
                <tr>
                    <th>Championship</th>
                    <th>Dates</th>
                    <th>Competitions</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {#each championships as championship (championship.id)}
                    <tr class="v2-row group *:border-t *:border-v2-border *:py-3.5 *:pr-4 *:align-middle *:first:pl-3 *:last:pr-3">
                        <td>
                            <Link href={championship.url} class="v2-row-link font-bold group-hover:text-v2-teal-text group-focus-within:text-v2-teal-text">
                                {championship.name}
                            </Link>
                        </td>
                        <td class="text-sm whitespace-nowrap text-v2-muted max-sm:hidden">{shortDate(championship.start)} – {shortDate(championship.end)}</td>
                        <td class="text-right text-sm whitespace-nowrap text-v2-muted">
                            {championship.competitions} competition{championship.competitions === 1 ? "" : "s"}
                        </td>
                        <td class="w-px"><RowIcon /></td>
                    </tr>
                {/each}
            </tbody>
        </table>
    {:else}
        <EmptyState>
            <p>No championships yet. Start one, then add {organisation.name}'s competitions to it.</p>
            <button type="button" class="v2-btn mt-4" onclick={() => newDialog.showModal()}>New championship</button>
        </EmptyState>
    {/if}
</section>

<dialog bind:this={newDialog} class="v2-modal" aria-labelledby="c-new-title" onclick={closeOnBackdrop}>
    <h2 id="c-new-title">New championship</h2>
    <p>You can add competitions to it once it's made.</p>

    <Form action={store(organisation.id)} resetOnSuccess onSuccess={() => newDialog.close()}>
        {#snippet children({ errors, processing })}
            <Field id="c-new-name" name="name" label="Name" required maxlength={255} error={errors.name} />
            <div class="grid gap-4 *:mt-0 sm:grid-cols-2">
                <Field id="c-new-start" name="start_date" type="date" label="Starts" required error={errors.start_date} />
                <Field id="c-new-end" name="end_date" type="date" label="Ends" required error={errors.end_date} />
            </div>
            <div class="v2-modal-actions">
                <button type="button" class="v2-btn" onclick={() => newDialog.close()}>Cancel</button>
                <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Create championship</button>
            </div>
        {/snippet}
    </Form>
</dialog>
