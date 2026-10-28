<!--
    The dashed box a list or table shows before it has anything in it: what goes there, and how to add
    the first one. It can hold that action's button too.
-->
<script lang="ts">
    import type { Snippet } from "svelte";
    import type { ClassValue } from "svelte/elements";

    let { children, class: className }: { children: Snippet; class?: ClassValue } = $props();
</script>

<div class={["rounded-xl border border-dashed border-v2-border px-5 py-8 text-center text-[0.9375rem]/[1.6] text-v2-muted", className]}>
    {@render children()}
</div>
