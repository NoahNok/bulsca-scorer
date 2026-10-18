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
</script>

<div
    class={cn("grid gap-1 rounded-lg bg-gray-100 p-1", className)}
    style:grid-template-columns="repeat({options.length}, minmax(0, 1fr))"
    role="radiogroup"
>
    {#each options as option (option.value)}
        <button
            type="button"
            role="radio"
            aria-checked={value === option.value}
            class="cursor-pointer rounded-md py-1.5 text-sm font-semibold transition-all {value ===
            option.value
                ? 'bg-white shadow-sm'
                : 'text-gray-500 hover:text-gray-800'}"
            onclick={() => (value = option.value)}
        >
            {option.label}
        </button>
    {/each}
</div>
