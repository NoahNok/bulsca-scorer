<!--
    The "Decline this invite?" modal both Invite pages ask before declining: declining deletes the invite, so
    only an Organiser sending a new one undoes it. Open it with `open()` through bind:this.
-->
<script lang="ts">
    import type { RouteDefinition } from "@/wayfinder";
    import { router } from "@inertiajs/svelte";

    let { action }: { action: RouteDefinition<"post"> } = $props();

    let dialog = $state<HTMLDialogElement>();
    let declining = $state(false);

    export function open() {
        dialog?.showModal();
    }

    function decline() {
        router.post(action, {}, {
            onStart: () => (declining = true),
            onFinish: () => (declining = false),
        });
    }
</script>

<!-- A native dialog: it traps focus, closes on Escape and focuses the first button, the safe one -->
<dialog
    bind:this={dialog}
    class="v2-modal animate-rise-fast motion-reduce:animate-none"
    aria-labelledby="decline-title"
    onclick={(e) => e.target === dialog && dialog.close()}
>
    <h2 id="decline-title">Decline this invite?</h2>
    <p>This deletes the invite. The Organiser will need to send a new one if you change your mind.</p>
    <div class="v2-modal-actions">
        <button type="button" class="v2-btn" onclick={() => dialog?.close()}>Keep invite</button>
        <button type="button" class="v2-btn v2-btn-danger" disabled={declining} onclick={decline}>Decline invite</button>
    </div>
</dialog>
