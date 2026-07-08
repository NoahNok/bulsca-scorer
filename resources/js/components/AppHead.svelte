<script lang="ts">
    import type { Snippet } from "svelte";

    let {
        title = "",
        children,
    }: {
        title?: string;
        children?: Snippet;
    } = $props();

    const appName = "DigitalJudge";
    const fullTitle = $derived(title ? `${title} - ${appName}` : appName);
</script>

<svelte:head>
    <title>{fullTitle}</title>
    {@render children?.()}
</svelte:head>
