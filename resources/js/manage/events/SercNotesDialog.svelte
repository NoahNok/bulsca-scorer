<!--
    The notes a SERC's judges left in Digital Judge, in a v2 modal (a native <dialog>). Call `open()` to show
    it. With `overall`, their overall notes come first; then a section per Entity, each note under the
    Objective it was left on. With one Entity and no overall notes, the Entity's name is the subtitle rather
    than a heading. `loading` shows while the notes are fetched.
-->
<script lang="ts">
    import type { SercNote } from "@/manage/types";
    import { closeOnBackdrop } from "@/manage/ui";

    let {
        title = "Judges' notes",
        overall = [],
        entities,
        loading = false,
    }: {
        title?: string;
        overall?: SercNote[];
        entities: { key: string; name: string; draw?: string | null; notes: SercNote[] }[];
        loading?: boolean;
    } = $props();

    let dialog: HTMLDialogElement;

    export function open() {
        dialog.showModal();
    }

    const single = $derived(entities.length === 1 && overall.length === 0);
    const empty = $derived(overall.length === 0 && entities.every((entity) => entity.notes.length === 0));
</script>

{#snippet list(notes: SercNote[])}
    <ul class="mt-2 border-t border-v2-border *:border-b *:border-v2-border">
        {#each notes as note (note.id)}
            <li class="py-2">
                <p class="text-sm font-semibold text-v2-muted">{note.objective ?? "Removed Objective"}</p>
                <p class="mt-0.5 whitespace-pre-line text-v2-ink! wrap-anywhere">{note.note}</p>
            </li>
        {/each}
    </ul>
{/snippet}

<dialog bind:this={dialog} class="v2-modal max-w-xl!" aria-labelledby="serc-notes-title" onclick={closeOnBackdrop}>
    <h2 id="serc-notes-title">{title}</h2>
    {#if single}<p>{entities[0].name}</p>{/if}

    <div class="mt-4 max-h-[60vh] overflow-y-auto">
        {#if loading}
            <p>Loading notes…</p>
        {:else if empty}
            <p>No notes yet. Judges leave them in Digital Judge.</p>
        {:else}
            {#if overall.length > 0}
                <section aria-labelledby="serc-notes-overall">
                    <h3 id="serc-notes-overall" class="mb-0! text-base!">Overall</h3>
                    {@render list(overall)}
                </section>
            {/if}
            {#each entities as entity (entity.key)}
                {#if single}
                    {@render list(entity.notes)}
                {:else}
                    <section aria-labelledby="serc-notes-{entity.key}" class="mt-5 first:mt-0">
                        <h3 id="serc-notes-{entity.key}" class="mb-0! text-base! wrap-anywhere">
                            {entity.name}
                            {#if entity.draw}<span class="ml-1 text-sm font-semibold text-v2-muted normal-case">{entity.draw.startsWith("Tank") ? entity.draw : `Draw ${entity.draw}`}</span>{/if}
                        </h3>
                        {@render list(entity.notes)}
                    </section>
                {/if}
            {/each}
        {/if}
    </div>

    <div class="v2-modal-actions">
        <button type="button" class="v2-btn" onclick={() => dialog.close()}>Close</button>
    </div>
</dialog>
