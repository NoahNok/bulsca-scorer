<!--
    The small × that removes a Heat, a tank, or an entry from the Draw. `label` says which, for screen
    readers; `title` is the tooltip.
-->
<script lang="ts">
    import type { ClassValue } from "svelte/elements";

    let { label, title, onclick, class: className }: { label: string; title: string; onclick: () => void; class?: ClassValue } = $props();
</script>

<button
    type="button"
    class={["shrink-0 cursor-pointer rounded px-1.5 py-1 font-semibold text-v2-muted hover:bg-v2-danger-soft hover:text-v2-danger-text", className]}
    aria-label={label}
    {title}
    {onclick}>×</button
>
