<!--
    A form's submit button (.v2-btn). While `processing` it shows a spinner and is disabled, so the form
    can't be sent twice.
-->
<script lang="ts">
    import type { Snippet } from "svelte";

    let {
        processing = false,
        variant = "primary",
        children,
    }: { processing?: boolean; variant?: "primary" | "accent"; children: Snippet } = $props();
</script>

<button type="submit" class="v2-btn v2-btn-{variant} s-submit" disabled={processing} aria-busy={processing}>
    {#if processing}
        <span class="s-spinner" aria-hidden="true"></span>
    {/if}
    {@render children()}
</button>

<style>
    .s-submit {
        justify-content: center;
        width: 100%;
        height: 2.75rem;
    }

    /* still reads as busy, not unavailable */
    .s-submit:disabled {
        opacity: 0.8;
        cursor: progress;
    }

    .s-spinner {
        width: 1rem;
        height: 1rem;
        border: 2px solid currentColor;
        border-right-color: transparent;
        border-radius: 9999px;
        animation: s-spin 700ms linear infinite;
    }

    @keyframes s-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .s-spinner {
            animation: none;
        }
    }
</style>
