<!--
    A dialog for picking one of a few links that open in a new tab, like a Pack's layout or which SERC's
    rough mark sheets to print: a title and a lead, the options as whole rows (a name, an optional muted
    line and an external icon), then Cancel, focused first. Picking one opens it and closes the dialog.
    Bind the component and call `open` with what to pick from.
-->
<script lang="ts" module>
    export type LinkOption = { name: string; description?: string; url: string };
</script>

<script lang="ts">
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import { closeOnBackdrop } from "@/manage/ui";
    import { tick } from "svelte";

    let dialog: HTMLDialogElement;
    let picking = $state<{ title: string; lead: string; options: LinkOption[] } | null>(null);

    export async function open(title: string, lead: string, options: LinkOption[]) {
        picking = { title, lead, options };
        await tick();
        dialog.showModal();
    }

    const row =
        "flex w-full cursor-pointer items-center gap-3 px-2 py-[0.8rem] text-left transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover motion-reduce:transition-none";
</script>

<dialog bind:this={dialog} class="v2-modal" aria-labelledby="link-picker-title" onclick={closeOnBackdrop} onclose={() => (picking = null)}>
    {#if picking}
        <h2 id="link-picker-title">{picking.title}</h2>
        <p>{picking.lead}</p>
        <ul class="mt-4 border-t border-v2-border *:border-b *:border-v2-border">
            {#each picking.options as option (option.url)}
                <li>
                    <a href={option.url} target="_blank" rel="noopener" class={row} onclick={() => dialog.close()}>
                        <span class="min-w-0">
                            <span class="block font-bold wrap-anywhere">{option.name}</span>
                            {#if option.description}<span class="block text-sm text-v2-muted">{option.description}</span>{/if}
                        </span>
                        <RowIcon icon="external" />
                    </a>
                </li>
            {/each}
        </ul>
        <div class="v2-modal-actions mt-6">
            <!-- First focus goes to the safe choice, as in every v2 modal -->
            <!-- svelte-ignore a11y_autofocus -->
            <button type="button" class="v2-btn" autofocus onclick={() => dialog.close()}>Cancel</button>
        </div>
    {/if}
</dialog>
