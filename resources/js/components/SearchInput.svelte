<script lang="ts">
    import { cn } from "@/utils/utils";
    import { Search } from "@lucide/svelte";
    import type { HTMLInputAttributes } from "svelte/elements";

    let {
        value = $bindable(""),
        placeholder = "Search...",
        class: className = "",
        ...restProps
    }: {
        value?: string;
        placeholder?: string;
        class?: string;
    } & Omit<HTMLInputAttributes, "value" | "type"> = $props();
</script>

<div class={cn("relative", className)}>
    <Search
        size={16}
        class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"
    />
    <input
        type="search"
        {placeholder}
        {...restProps}
        class="w-full rounded-lg border py-2 pr-3 pl-9 text-sm transition-all focus:border-se focus:ring-1 focus:ring-se/10 focus:outline-none"
        bind:value
    />
</div>
