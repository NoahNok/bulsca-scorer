<script lang="ts">
    import { ChevronDown, ChevronUp } from "@lucide/svelte";
    import { Collapsible, type WithoutChild } from "bits-ui";
    import type { Snippet } from "svelte";
    import { slide } from "svelte/transition";

    type Props = WithoutChild<Collapsible.RootProps> & {
        header: Snippet;
    };

    let {
        open = $bindable(false),
        ref = $bindable(null),
        header,
        children,
        ...restProps
    }: Props = $props();
</script>

<Collapsible.Root bind:open bind:ref {...restProps}>
    <Collapsible.Trigger class="w-full flex items-center justify-between">
        {@render header()}
        <ChevronUp class="{open ? '' : '-rotate-180'} transition-transform" />
    </Collapsible.Trigger>

    <Collapsible.Content forceMount>
        {#snippet child({ props, open })}
            {#if open}
                {@render children?.()}
            {/if}
        {/snippet}
    </Collapsible.Content>
</Collapsible.Root>
