<!--
    The entries not in any Heat, or not in the Draw, under the editor: select one, then where to put it.
    Selecting it again lets it go. `detail` adds a line under a name, like a seed time.
-->
<script lang="ts" generics="T extends { id: number; name: string }">
    import { changedClass, selectedClass } from "@/manage/orders.svelte";
    import type { Snippet } from "svelte";

    let {
        id,
        entries,
        selected,
        onselect,
        lead,
        done,
        changed = () => false,
        detail,
    }: {
        /** The heading's id */
        id: string;
        entries: T[];
        selected: number | null;
        onselect: (id: number) => void;
        /** How to place one */
        lead: string;
        /** Said when every entry is placed */
        done: string;
        /** Whether to light one up, just put back */
        changed?: (id: number) => boolean;
        detail?: Snippet<[T]>;
    } = $props();
</script>

<section aria-labelledby={id} class="mt-8">
    <div class="flex items-center gap-3">
        <h3 {id} class="text-lg font-extrabold uppercase">Unassigned</h3>
        <span class="v2-badge">{entries.length}</span>
    </div>
    {#if entries.length === 0}
        <p class="mt-2 text-[0.9375rem] text-v2-muted">{done}</p>
    {:else}
        <p class="mt-1 text-[0.9375rem] text-v2-muted">{lead}</p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
            {#each entries as entry (entry.id)}
                <button
                    type="button"
                    class={[
                        "flex cursor-pointer flex-col rounded-md border border-v2-border bg-v2-card px-3 py-2 text-left text-sm hover:border-v2-teal",
                        selected === entry.id && selectedClass,
                        changed(entry.id) && changedClass,
                    ]}
                    aria-pressed={selected === entry.id}
                    data-place="entity:{entry.id}"
                    onclick={() => onselect(entry.id)}
                >
                    <span class="font-semibold wrap-anywhere">{entry.name}</span>
                    {@render detail?.(entry)}
                </button>
            {/each}
        </div>
    {/if}
</section>
