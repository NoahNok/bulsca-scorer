<script lang="ts">
    import { ChevronDown, ChevronUp } from "@lucide/svelte";
    import { Collapsible, type WithoutChild } from "bits-ui";

    type Props = WithoutChild<Collapsible.RootProps> & {
        buttonText: string;
    };

    let {
        open = $bindable(false),
        ref = $bindable(null),
        buttonText,
        children,
        ...restProps
    }: Props = $props();
</script>

<Collapsible.Root bind:open bind:ref {...restProps}>
    <Collapsible.Trigger class="w-full flex items-center justify-between"
        >{buttonText}
        <ChevronUp class="{open ? '' : '-rotate-180'} transition-transform" />
    </Collapsible.Trigger>
    <Collapsible.Content>
        {@render children?.()}
    </Collapsible.Content>
</Collapsible.Root>
