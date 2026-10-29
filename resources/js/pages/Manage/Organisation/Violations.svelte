<!--
    An organisation's Violations (manage.organisations.violations, Organisers only), in OrganisationLayout:
    its DQ codes and penalty codes side by side, by code. A row opens a dialog with its description and,
    for each speed event and SERC, the kind of call it is there (N/A for none), with Delete (confirmed) and
    Save. New DQ code and New penalty code open the same dialog with a code number. Saving or deleting
    keeps the page where it was.
-->
<script lang="ts">
    import { destroy, store, update } from "@/actions/App/Http/Controllers/Manage/ViolationCodesController";
    import { confirm } from "@/lib/confirm";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import type { OrganisationHeader } from "@/manage/types";
    import { closeOnBackdrop } from "@/manage/ui";
    import { router, useForm } from "@inertiajs/svelte";

    type CodeType = "dq" | "pen";
    type Code = { id: number; code: number; description: string; events: Record<string, string | null> };

    let {
        organisation,
        dqs,
        penalties,
        events,
        kinds,
    }: {
        organisation: OrganisationHeader;
        /** By code */
        dqs: Code[];
        /** By code */
        penalties: Code[];
        /** Every speed event's name, then SERC */
        events: string[];
        /** The kinds of call a code can be for an event */
        kinds: { value: string; label: string }[];
    } = $props();

    const PREFIX: Record<CodeType, string> = { dq: "DQ", pen: "P" };
    const NAME: Record<CodeType, string> = { dq: "DQ code", pen: "penalty code" };

    let dialog: HTMLDialogElement;
    // The code open in the dialog, null for a new one
    let editing = $state<Code | null>(null);

    const form = useForm({
        type: "dq" as CodeType,
        code: null as number | null,
        description: "",
        events: {} as Record<string, string | null>,
    });

    function open(type: CodeType, code: Code | null) {
        editing = code;
        form.clearErrors();
        form.type = type;
        form.code = code?.code ?? null;
        form.description = code?.description ?? "";
        form.events = Object.fromEntries(events.map((event) => [event, code?.events[event] ?? null]));
        dialog.showModal();
    }

    const options = { preserveScroll: true, preserveState: true, onSuccess: () => dialog.close() };

    function save(submit: SubmitEvent) {
        submit.preventDefault();
        const ids = { organisation: organisation.id };
        if (editing) form.patch(update({ ...ids, type: form.type, code: editing.id }).url, options);
        else form.post(store(ids).url, options);
    }

    async function remove() {
        if (!editing) return;
        const ok = await confirm({
            title: `Delete ${PREFIX[form.type]}${editing.code}?`,
            description: "Officials can't pick it any more. It can't be undone.",
            confirmLabel: "Delete code",
            confirmVariant: "danger",
        });
        if (ok) router.delete(destroy({ organisation: organisation.id, type: form.type, code: editing.id }).url, options);
    }

    const title = $derived(editing ? `${PREFIX[form.type]}${editing.code}` : `New ${NAME[form.type]}`);
</script>

<svelte:head>
    <title>Violations - {organisation.name} - Scoring.Events</title>
</svelte:head>

{#snippet list(type: CodeType, codes: Code[], heading: string, lead: string)}
    <section aria-labelledby="v-{type}">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 id="v-{type}" class="text-lg font-extrabold uppercase">{heading}</h2>
            {#if codes.length > 0}
                <button type="button" class="v2-btn v2-btn-sm" onclick={() => open(type, null)}>New {NAME[type]}</button>
            {/if}
        </div>
        <p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">{lead}</p>

        {#if codes.length > 0}
            <table class="w-full border-collapse">
                <thead class="sr-only">
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {#each codes as code (code.id)}
                        <tr class="v2-row group *:border-t *:border-v2-border *:py-3 *:pr-4 *:align-middle *:first:pl-3 *:last:pr-3">
                            <td class="w-px whitespace-nowrap">
                                <button
                                    type="button"
                                    class="v2-row-link cursor-pointer font-bold group-hover:text-v2-teal-text group-focus-within:text-v2-teal-text"
                                    onclick={() => open(type, code)}
                                >
                                    {PREFIX[type]}{code.code}
                                </button>
                            </td>
                            <td class="text-sm text-v2-muted"><span class="line-clamp-2 wrap-anywhere">{code.description}</span></td>
                            <td class="w-px"><RowIcon /></td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        {:else}
            <EmptyState>
                <p>No {NAME[type]}s yet.</p>
                <button type="button" class="v2-btn mt-4" onclick={() => open(type, null)}>New {NAME[type]}</button>
            </EmptyState>
        {/if}
    </section>
{/snippet}

<div class="grid gap-10 lg:grid-cols-2">
    {@render list("dq", dqs, "DQ codes", "What Officials can disqualify for.")}
    {@render list("pen", penalties, "Penalty codes", "What Officials can give a penalty for.")}
</div>

<dialog bind:this={dialog} class="v2-modal max-w-lg!" aria-labelledby="v-dialog-title" onclick={closeOnBackdrop}>
    <h2 id="v-dialog-title">{title}</h2>
    <p>For each event, what kind of call it is, so Officials there see it. N/A leaves it out of that event.</p>

    <form onsubmit={save}>
        {#if !editing}
            <div class={["v2-field", form.errors.code && "is-invalid"]}>
                <div class="flex items-center gap-2">
                    <span class="font-bold" aria-hidden="true">{PREFIX[form.type]}</span>
                    <input
                        id="v-code"
                        aria-label="{PREFIX[form.type]} code number"
                        type="number"
                        min="1"
                        step="1"
                        required
                        class="v2-input w-32 font-mono"
                        bind:value={form.code}
                        aria-invalid={form.errors.code ? "true" : undefined}
                        aria-describedby={form.errors.code ? "v-code-error" : undefined}
                    />
                </div>
                {#if form.errors.code}<span id="v-code-error" class="v2-error">{form.errors.code}</span>{/if}
            </div>
        {/if}

        <div class={["v2-field", form.errors.description && "is-invalid"]}>
            <label for="v-description">Description</label>
            <textarea
                id="v-description"
                class="v2-input w-full"
                rows="2"
                maxlength="255"
                required
                bind:value={form.description}
                aria-invalid={form.errors.description ? "true" : undefined}
                aria-describedby={form.errors.description ? "v-description-error" : undefined}
            ></textarea>
            {#if form.errors.description}<span id="v-description-error" class="v2-error">{form.errors.description}</span>{/if}
        </div>

        <fieldset class="mt-4">
            <legend class="mb-2 text-sm font-semibold">Kind of call</legend>
            <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-2">
                {#each events as event, i (event)}
                    {@const error = (form.errors as Record<string, string>)[`events.${event}`]}
                    <label for="v-event-{i}" class="text-sm wrap-anywhere">{event}</label>
                    <select id="v-event-{i}" class={["v2-input w-40", error && "is-invalid"]} bind:value={form.events[event]}>
                        <option value={null}>N/A</option>
                        {#each kinds as kind (kind.value)}
                            <option value={kind.value}>{kind.label}</option>
                        {/each}
                    </select>
                    {#if error}<span class="v2-error col-span-2">{error}</span>{/if}
                {/each}
            </div>
        </fieldset>

        <div class="v2-modal-actions">
            {#if editing}
                <button type="button" class="v2-btn v2-btn-ghost mr-auto text-v2-danger-text" onclick={remove}>Delete</button>
            {/if}
            <button type="button" class="v2-btn" onclick={() => dialog.close()}>Cancel</button>
            <button type="submit" class="v2-btn v2-btn-primary" disabled={form.processing}>{editing ? "Save" : `Add ${PREFIX[form.type]}${form.code ?? ""}`}</button>
        </div>
    </form>
</dialog>
