<!--
    Deleting a Results sheet or Master sheet: a dialog asking for its name typed out, naming the Master
    sheets a Results sheet is taken out of. Bind it and call `open()`.
-->
<script lang="ts">
    import Field from "@/manage/components/Field.svelte";
    import { closeOnBackdrop } from "@/manage/ui";
    import { Form } from "@inertiajs/svelte";
    import { tick } from "svelte";

    let {
        name,
        action,
        masters = [],
    }: {
        name: string;
        /** The delete route (a Wayfinder action) */
        action: { url: string; method: "delete" };
        /** Master sheets that use it */
        masters?: string[];
    } = $props();

    let dialog: HTMLDialogElement;
    // Remounts the form each time it opens, so it starts empty
    let opened = $state(0);
    let typed = $state("");

    export async function open() {
        opened++;
        typed = "";
        await tick();
        dialog.showModal();
    }
</script>

<dialog bind:this={dialog} class="v2-modal" aria-labelledby="delete-sheet-title" onclick={closeOnBackdrop}>
    <h2 id="delete-sheet-title">Delete {name}?</h2>
    <p>
        This can't be undone.
        {#if masters.length > 0}It's also taken out of {masters.length === 1 ? "the Master sheet" : "the Master sheets"} {masters.join(", ")}.{/if}
    </p>
    {#key opened}
        <Form {action} class="mt-5">
            {#snippet children({ errors, processing })}
                <Field
                    id="delete-sheet-name"
                    name="name"
                    label="Type its name to confirm"
                    placeholder={name}
                    autocomplete="off"
                    error={errors.name}
                    oninput={(input) => (typed = input.currentTarget.value)}
                />
                <div class="v2-modal-actions mt-6">
                    <button type="button" class="v2-btn" onclick={() => dialog.close()}>Keep sheet</button>
                    <button type="submit" class="v2-btn v2-btn-danger" disabled={processing || typed !== name}>Delete sheet</button>
                </div>
            {/snippet}
        </Form>
    {/key}
</dialog>
