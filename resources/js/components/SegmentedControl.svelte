<script lang="ts" generics="T extends string">
    import { cn } from "@/utils/utils";

    let {
        options,
        value = $bindable(),
        class: className = "",
    }: {
        options: readonly { value: T; label: string }[];
        value: T;
        class?: string;
    } = $props();

    const selectedIndex = $derived(options.findIndex((option) => option.value === value));
</script>

<div
    class={cn("relative grid gap-1 rounded-lg bg-gray-100 p-1", className)}
    style:grid-template-columns="repeat({options.length}, minmax(0, 1fr))"
    role="radiogroup"
>
    <!-- The pill slides under the selected option. Width is one column (inside the p-1
         padding and gap-1 gaps), and translating by 100% + one gap moves it a column -->
    {#if selectedIndex !== -1}
        <span
            aria-hidden="true"
            class="pointer-events-none absolute inset-y-1 left-1 rounded-md bg-white shadow-sm transition-transform duration-200 ease-out motion-reduce:transition-none"
            style:width="calc((100% - 0.5rem - {options.length - 1} * 0.25rem) / {options.length})"
            style:transform="translateX(calc({selectedIndex} * (100% + 0.25rem)))"
        ></span>
    {/if}

    {#each options as option (option.value)}
        <button
            type="button"
            role="radio"
            aria-checked={value === option.value}
            class="relative cursor-pointer rounded-md py-1.5 text-sm font-semibold transition-colors {value ===
            option.value
                ? 'text-gray-900'
                : 'text-gray-500 hover:text-gray-800'}"
            onclick={() => (value = option.value)}
        >
            {option.label}
        </button>
    {/each}
</div>
