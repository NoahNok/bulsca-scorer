<!--
    The sliding-pill tabs (.v2-tabs) over a set of panels, for a choice within a page: the pill slides to the
    chosen tab, arrow keys move between tabs, and the panels' wrapper animates to the chosen one's height
    as it fades in, so what's below glides rather than jumps.

    Only the chosen panel is mounted, so a form sends only its fields (New competition's organisation or
    Personal).
-->
<script lang="ts">
    import { onMount, type Snippet } from "svelte";

    let {
        tabs,
        value = $bindable(),
        id,
        labelledby,
        label,
        panel,
    }: {
        tabs: { id: string; label: string }[];
        value: string;
        /** Prefixes the tab and panel ids */
        id: string;
        /** The id of a visible label for the tabs, or `label` when there isn't one */
        labelledby?: string;
        label?: string;
        panel: Snippet<[string]>;
    } = $props();

    let buttons: HTMLButtonElement[] = $state([]);
    let height = $state(0);
    // Panels fade in when you switch, not when the page loads
    let ready = $state(false);

    const index = $derived(Math.max(0, tabs.findIndex((tab) => tab.id === value)));

    onMount(() => {
        ready = true;
    });

    function onKey(event: KeyboardEvent) {
        const step = event.key === "ArrowRight" ? 1 : event.key === "ArrowLeft" ? -1 : 0;
        if (!step) return;
        event.preventDefault();
        const next = (index + step + tabs.length) % tabs.length;
        value = tabs[next].id;
        buttons[next]?.focus();
    }
</script>

<div class="v2-tabs" role="tablist" aria-labelledby={labelledby} aria-label={label} style="--v2-tabs: {tabs.length}">
    <span class="v2-tabs-pill" aria-hidden="true" style="transform: translateX({index * 100}%)"></span>
    {#each tabs as tab, i (tab.id)}
        <button
            bind:this={buttons[i]}
            type="button"
            role="tab"
            id="{id}-tab-{tab.id}"
            aria-selected={tab.id === value}
            aria-controls="{id}-panel-{tab.id}"
            tabindex={tab.id === value ? 0 : -1}
            onclick={() => (value = tab.id)}
            onkeydown={onKey}>{tab.label}</button
        >
    {/each}
</div>

<!-- Padding out and back in, so overflow doesn't clip the fields' focus rings -->
<div
    class="-mx-1 -mb-1 overflow-hidden transition-[height] duration-250 ease-[cubic-bezier(0.3,0.7,0.4,1)] motion-reduce:transition-none"
    style:height={height ? `${height}px` : undefined}
>
    <div class="px-1 pt-2 pb-1" bind:offsetHeight={height}>
        {#each tabs as tab (tab.id)}
            {#if tab.id === value}
                <div
                    id="{id}-panel-{tab.id}"
                    class={[ready && "animate-fade-in motion-reduce:animate-none"]}
                    role="tabpanel"
                    aria-labelledby="{id}-tab-{tab.id}"
                >
                    {@render panel(tab.id)}
                </div>
            {/if}
        {/each}
    </div>
</div>
