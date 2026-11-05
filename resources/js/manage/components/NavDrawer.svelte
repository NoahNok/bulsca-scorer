<!--
    The header's burger on phones (below `sm`) and the panel it slides in from the right (.v2-drawer), a native
    <dialog> holding the links it's given as children. It closes on its ×, Escape, the backdrop or any link; a link
    closes it before ManageLayout's unsaved changes prompt opens, so keeping your edits leaves you on the form.
-->
<script lang="ts">
    import type { Snippet } from "svelte";

    let { children }: { children: Snippet } = $props();

    let dialog: HTMLDialogElement;

    // The panel's content fills the dialog, so a click on the dialog itself is on its backdrop
    function onclick(event: MouseEvent) {
        const target = event.target as Element;
        if (target === dialog || target.closest("a")) dialog.close();
    }
</script>

<button type="button" class="v2-nav-icon sm:hidden" aria-label="Open menu" aria-haspopup="dialog" onclick={() => dialog.showModal()}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
        <path d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<dialog bind:this={dialog} class="v2-drawer" aria-label="Menu" {onclick}>
    <div class="flex h-full flex-col overflow-y-auto">
        <div class="flex justify-end p-4">
            <button type="button" class="v2-nav-icon" aria-label="Close menu" onclick={() => dialog.close()}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
        <div class="v2-drawer-links">
            {@render children()}
        </div>
    </div>
</dialog>
