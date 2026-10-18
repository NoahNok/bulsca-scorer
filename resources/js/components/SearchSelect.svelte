<script lang="ts">
    import { cn } from "@/utils/utils";
    import { Check, ChevronDown, Search } from "@lucide/svelte";
    import { Combobox, Select, useId } from "bits-ui";

    export type SearchSelectOption = {
        value: string;
        label: string;
        // secondary line under the label, also matched when searching
        description?: string;
    };

    let {
        options,
        value = $bindable(""),
        label = null,
        placeholder = "Select...",
        hint = null,
        required = false,
        disabled = false,
        // the search box only shows once there are more options than this
        searchThreshold = 8,
        searchPlaceholder = "Search...",
        emptyText = "No matches",
        // adds a "None" row that clears the value
        allowClear = false,
        clearLabel = "None",
        class: className = "",
    }: {
        options: SearchSelectOption[];
        value?: string;
        label?: string | null;
        placeholder?: string;
        hint?: string | null;
        required?: boolean;
        disabled?: boolean;
        searchThreshold?: number;
        searchPlaceholder?: string;
        emptyText?: string;
        allowClear?: boolean;
        clearLabel?: string;
        class?: string;
    } = $props();

    const id = useId("search-select");

    let open = $state(false);
    let search = $state("");
    let triggerRef = $state<HTMLButtonElement | null>(null);

    let searchable = $derived(options.length > searchThreshold);
    let selected = $derived(options.find((o) => o.value === value));

    let filtered = $derived.by(() => {
        const term = search.trim().toLowerCase();
        if (!searchable || !term) return options;

        return options.filter(
            (o) =>
                o.label.toLowerCase().includes(term) ||
                o.description?.toLowerCase().includes(term),
        );
    });

    let items = $derived(
        allowClear && !search.trim()
            ? [{ value: "", label: clearLabel }, ...filtered]
            : filtered,
    );

    function onOpenChange(isOpen: boolean) {
        if (isOpen) return;

        search = "";
        // the search input lives in the dropdown, so give focus back to the trigger once it's gone
        if (searchable) triggerRef?.focus();
    }

    // Focus the search box as soon as the dropdown opens, but only with a real keyboard:
    // on a phone it would pop the on-screen keyboard over the list straight away
    let searchRef = $state<HTMLInputElement | null>(null);
    $effect(() => {
        if (window.matchMedia("(pointer: fine)").matches) searchRef?.focus();
    });

    const triggerClass =
        "flex w-full cursor-pointer items-center justify-between gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-left text-sm text-gray-900 shadow-xs transition-all hover:border-gray-400 focus:outline-none focus-visible:border-se focus-visible:ring-2 focus-visible:ring-se/20 data-[state=open]:border-se disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400";
    const contentClass =
        "z-50 flex max-h-[min(18rem,var(--bits-floating-available-height))] w-max max-w-[calc(100vw-2rem)] min-w-(--bits-floating-anchor-width) flex-col overflow-hidden rounded-xl border bg-white shadow-md";
    const itemClass =
        "flex w-full cursor-pointer items-center gap-3 px-3 py-3 text-left text-sm transition-colors select-none data-highlighted:bg-gray-50 data-selected:bg-se/5";
</script>

{#snippet triggerContent()}
    <span class={cn("truncate", !selected && "text-gray-400")}>
        {selected?.label ?? placeholder}
    </span>
    <ChevronDown
        size={16}
        class="shrink-0 text-gray-400 transition-transform {open
            ? 'rotate-180'
            : ''}"
    />
{/snippet}

{#snippet itemContent(option: SearchSelectOption, isSelected: boolean)}
    <span class="flex min-w-0 flex-1 flex-col">
        <span
            class={cn(
                "truncate font-medium",
                option.value === "" && "font-normal text-gray-500",
            )}
        >
            {option.label}
        </span>
        {#if option.description}
            <span class="truncate text-xs text-gray-500">
                {option.description}
            </span>
        {/if}
    </span>
    {#if isSelected}
        <Check size={16} class="shrink-0 text-se" />
    {/if}
{/snippet}

<div class={className}>
    {#if label}
        <label for={id} class="mb-1 block text-xs font-semibold text-gray-600">
            {label}
            {#if required}
                <span class="text-red-500">*</span>
            {/if}
        </label>
    {/if}

    {#if searchable}
        <Combobox.Root
            type="single"
            bind:value
            bind:open
            {onOpenChange}
            {disabled}
            {required}
            items={items.map((o) => ({ value: o.value, label: o.label }))}
        >
            <Combobox.Trigger {id} class={triggerClass} bind:ref={triggerRef}>
                {@render triggerContent()}
            </Combobox.Trigger>
            <Combobox.Portal>
                <Combobox.Content
                    class={contentClass}
                    sideOffset={4}
                    collisionPadding={16}
                    customAnchor={triggerRef}
                >
                    <div class="relative border-b p-2">
                        <Search
                            size={16}
                            class="pointer-events-none absolute top-1/2 left-5 -translate-y-1/2 text-gray-400"
                        />
                        <!-- Our own input so the search text is ours to reset, rather than bits-ui's (which becomes the picked label) -->
                        <Combobox.Input
                            bind:ref={searchRef}
                            oninput={(e) => (search = e.currentTarget.value)}
                        >
                            {#snippet child({ props })}
                                <input
                                    {...props}
                                    value={search}
                                    type="search"
                                    placeholder={searchPlaceholder}
                                    aria-label={searchPlaceholder}
                                    class="w-full rounded-lg border py-2 pr-3 pl-9 text-base transition-all sm:text-sm focus:border-se focus:ring-1 focus:ring-se/10 focus:outline-none"
                                />
                            {/snippet}
                        </Combobox.Input>
                    </div>
                    <div class="min-h-0 overflow-y-auto py-1">
                        {#each items as option (option.value)}
                            <Combobox.Item
                                value={option.value}
                                label={option.label}
                                class={itemClass}
                            >
                                {#snippet children({ selected })}
                                    {@render itemContent(option, selected)}
                                {/snippet}
                            </Combobox.Item>
                        {:else}
                            <p class="px-3 py-2 text-sm text-gray-400">
                                {emptyText}
                            </p>
                        {/each}
                    </div>
                </Combobox.Content>
            </Combobox.Portal>
        </Combobox.Root>
    {:else}
        <Select.Root
            type="single"
            bind:value
            bind:open
            {onOpenChange}
            {disabled}
            {required}
            items={items.map((o) => ({ value: o.value, label: o.label }))}
        >
            <Select.Trigger {id} class={triggerClass} bind:ref={triggerRef}>
                {@render triggerContent()}
            </Select.Trigger>
            <Select.Portal>
                <Select.Content
                    class={contentClass}
                    sideOffset={4}
                    collisionPadding={16}
                >
                    <Select.Viewport class="min-h-0 py-1">
                        {#each items as option (option.value)}
                            <Select.Item
                                value={option.value}
                                label={option.label}
                                class={itemClass}
                            >
                                {#snippet children({ selected })}
                                    {@render itemContent(option, selected)}
                                {/snippet}
                            </Select.Item>
                        {/each}
                    </Select.Viewport>
                </Select.Content>
            </Select.Portal>
        </Select.Root>
    {/if}

    {#if hint}
        <p class="mt-1 text-xs text-gray-400">{hint}</p>
    {/if}
</div>
