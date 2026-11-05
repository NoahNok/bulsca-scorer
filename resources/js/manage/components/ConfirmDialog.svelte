<!--
    Answers `confirm()` from lib/confirm.ts with a v2 modal (a native <dialog>). ManageLayout mounts it once;
    pages just `await confirm({ ... })`. Cancel comes first, so it has focus when the dialog opens.
-->
<script lang="ts">
    import confirmStore from "@/lib/confirm";

    let dialog: HTMLDialogElement;

    $effect(() => {
        if ($confirmStore.open && !dialog.open) dialog.showModal();
        if (!$confirmStore.open && dialog.open) dialog.close();
    });

    function answer(value: boolean) {
        $confirmStore.resolve?.(value);
        confirmStore.set({ open: false });
    }
</script>

<!-- Escape fires `close`, as does closing it here; either way an unanswered dialog is a no -->
<dialog
    bind:this={dialog}
    class="v2-modal"
    aria-labelledby="confirm-title"
    onclose={() => $confirmStore.open && answer(false)}
    onclick={(event) => event.target === dialog && answer(false)}
>
    <h2 id="confirm-title">{$confirmStore.title ?? "Are you sure?"}</h2>
    {#if $confirmStore.description}
        <p>{$confirmStore.description}</p>
    {/if}
    <div class="v2-modal-actions">
        <button type="button" class="v2-btn" onclick={() => answer(false)}>{$confirmStore.cancelLabel ?? "Cancel"}</button>
        <button
            type="button"
            class={["v2-btn", $confirmStore.confirmVariant === "danger" ? "v2-btn-danger" : "v2-btn-primary"]}
            onclick={() => answer(true)}>{$confirmStore.confirmLabel ?? "Confirm"}</button
        >
    </div>
</dialog>
