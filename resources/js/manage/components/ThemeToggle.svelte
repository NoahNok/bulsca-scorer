<!--
    The header's light/dark toggle, the Svelte copy of the one in landing/v2/layout.blade.php.
    Both use resources/js/lib/theme.ts.
-->
<script lang="ts">
    import { flipTheme, isDark } from "@/lib/theme";

    let dark = $state(isDark());
</script>

<button
    type="button"
    class="v2-theme-toggle"
    aria-label={dark ? "Switch to light mode" : "Switch to dark mode"}
    onclick={(e) => flipTheme(e.currentTarget, (d) => (dark = d))}
>
    <!-- Shows the mode you'd switch to -->
    {#if dark}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
        </svg>
    {:else}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" />
        </svg>
    {/if}
</button>
