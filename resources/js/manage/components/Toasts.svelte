<!--
    The v2 toast stack (.v2-toasts, bottom-right), for the toasts fired through lib/toast.svelte.ts.
    ManageLayout mounts it once and toasts the `success` session flash after each visit.
-->
<script lang="ts">
    import { dismissToast, toastState } from "@/lib/toast.svelte";
    import { cubicOut } from "svelte/easing";
    import { prefersReducedMotion } from "svelte/motion";

    // The slot grows from no height as a toast arrives and shrinks as it leaves, so the stack closes the gap
    function slot(node: HTMLElement, { x = 0, y = 0 }: { x?: number; y?: number }) {
        const pad = parseFloat(getComputedStyle(node).paddingTop);
        const height = node.offsetHeight - pad;

        return {
            duration: prefersReducedMotion.current ? 0 : 300,
            easing: cubicOut,
            css: (t: number) =>
                `box-sizing: content-box; height: ${t * height}px; padding-top: ${t * pad}px; overflow: hidden;` +
                `opacity: ${t}; transform: translate(${(1 - t) * x}px, ${(1 - t) * y}px)`,
        };
    }
</script>

<div class="v2-toasts" role="status" aria-live="polite">
    {#each toastState as item (item.id)}
        <div class="v2-toast-slot is-in" in:slot={{ y: 12 }} out:slot={{ x: 32 }}>
            <div class="v2-toast-clip">
                <div class={["v2-toast", item.variant === "error" ? "is-danger" : "is-success"]}>
                    <svg class="v2-toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        {#if item.variant === "error"}
                            <path d="M12 8v5M12 16h.01" />
                            <circle cx="12" cy="12" r="9" />
                        {:else}
                            <path d="m5 12 5 5 9-10" />
                        {/if}
                    </svg>
                    <span>{item.title}</span>
                    <button type="button" onclick={() => dismissToast(item.id)}>Dismiss</button>
                </div>
            </div>
        </div>
    {/each}
</div>
