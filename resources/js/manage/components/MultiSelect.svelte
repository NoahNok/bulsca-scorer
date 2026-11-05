<!--
    A v2 field for picking several options (bits-ui's Combobox): the chosen ones as chips in the box, each
    with a remove button, and typing (or clicking the box) opens the list and filters it. The form gets one
    hidden `name` input per chosen value, so give it a `name` ending in [] (e.g. leagues[]); choosing none
    sends nothing. Bind `value` to state the page owns.
    Inside a <dialog> the list is put in the dialog, since one under <body> would sit behind it.
-->
<script lang="ts">
    import { Check, ChevronDown, X } from "@lucide/svelte";
    import { Combobox } from "bits-ui";

    export type MultiSelectOption = { value: string; label: string };

    let {
        id,
        label,
        name,
        options,
        value = $bindable([]),
        placeholder = "Choose…",
        emptyText = "No matches",
        hint,
        error,
    }: {
        id: string;
        label: string;
        name: string;
        options: MultiSelectOption[];
        value?: string[];
        placeholder?: string;
        emptyText?: string;
        hint?: string;
        error?: string;
    } = $props();

    let open = $state(false);
    let search = $state("");
    let box = $state<HTMLElement | null>(null);
    let input = $state<HTMLInputElement | null>(null);

    const chosen = $derived(value.map((v) => options.find((o) => o.value === v)).filter((o) => o !== undefined));
    const filtered = $derived.by(() => {
        const term = search.trim().toLowerCase();
        return term ? options.filter((o) => o.label.toLowerCase().includes(term)) : options;
    });
    const dialog = $derived(box?.closest("dialog") ?? undefined);
    const describedBy = $derived([error && `${id}-error`, hint && `${id}-hint`].filter(Boolean).join(" ") || undefined);

    function remove(option: MultiSelectOption) {
        value = value.filter((v) => v !== option.value);
        input?.focus();
    }

    // Backspace in the empty search removes the last chip
    function onkeydown(event: KeyboardEvent) {
        if (event.key === "Backspace" && search === "" && value.length > 0) value = value.slice(0, -1);
    }

    // Escape closes the list, not the dialog around it: the dialog's cancel comes after the list has shut,
    // so note on the way down that it was open
    let escapedList = false;
    function onkeydowncapture(event: KeyboardEvent) {
        if (event.key === "Escape") escapedList = open;
    }
    $effect(() => {
        // Held here: by the time this is torn down the field is gone, and `dialog` with it
        const target = dialog;
        if (!target) return;
        const keepOpen = (event: Event) => {
            if (escapedList) event.preventDefault();
            escapedList = false;
        };
        target.addEventListener("cancel", keepOpen);
        return () => target.removeEventListener("cancel", keepOpen);
    });
</script>

<div class="v2-field">
    <label for={id}>{label}</label>
    <Combobox.Root
        type="multiple"
        bind:value
        bind:open
        onValueChange={() => (search = "")}
        onOpenChange={(isOpen) => !isOpen && (search = "")}
        items={options}
    >
        <!-- svelte-ignore a11y_click_events_have_key_events, a11y_no_static_element_interactions (the input inside takes the keys) -->
        <div
            bind:this={box}
            class={[
                "v2-input relative flex h-auto min-h-11 cursor-text flex-wrap items-center gap-1.5 py-1.5 pr-10 pl-2",
                "focus-within:border-v2-teal focus-within:ring-3 focus-within:ring-v2-teal/25",
                error && "is-invalid",
            ]}
            onclick={(event) => {
                if (event.target !== event.currentTarget) return;
                input?.focus();
                open = true;
            }}
            {onkeydowncapture}
        >
            {#each chosen as option (option.value)}
                <span class="inline-flex items-center gap-1 rounded-md bg-v2-sunk py-0.5 pr-1 pl-2 text-sm font-semibold">
                    {option.label}
                    <button
                        type="button"
                        class="grid size-5 cursor-pointer place-items-center rounded text-v2-muted hover:text-v2-ink"
                        aria-label="Remove {option.label}"
                        onclick={() => remove(option)}
                    >
                        <X size={14} aria-hidden="true" />
                    </button>
                </span>
            {/each}
            <!-- Our own input, so the search text stays ours rather than becoming the picked label -->
            <Combobox.Input
                bind:ref={input}
                oninput={(event) => {
                    search = event.currentTarget.value;
                    open = true;
                }}
                onclick={() => (open = true)}
                {onkeydown}
            >
                {#snippet child({ props })}
                    <input
                        {...props}
                        {id}
                        value={search}
                        placeholder={chosen.length ? "" : placeholder}
                        autocomplete="off"
                        aria-invalid={error ? "true" : undefined}
                        aria-describedby={describedBy}
                        class="h-8 min-w-24 flex-1 bg-transparent px-1 outline-none placeholder:text-v2-muted"
                    />
                {/snippet}
            </Combobox.Input>
            <Combobox.Trigger
                class="absolute top-1/2 right-2 grid size-7 -translate-y-1/2 cursor-pointer place-items-center rounded-md text-v2-muted hover:text-v2-ink"
                aria-label="Show {label.toLowerCase()}"
            >
                <ChevronDown size={16} class={["transition-transform motion-reduce:transition-none", open && "rotate-180"]} />
            </Combobox.Trigger>
        </div>
        <Combobox.Portal to={dialog}>
            <Combobox.Content
                customAnchor={box}
                sideOffset={4}
                collisionPadding={16}
                class="z-50 max-h-[min(16rem,var(--bits-floating-available-height))] min-w-(--bits-floating-anchor-width) overflow-y-auto rounded-lg border border-v2-border bg-v2-card py-1 shadow-lg"
            >
                {#each filtered as option (option.value)}
                    <Combobox.Item
                        value={option.value}
                        label={option.label}
                        class="flex cursor-pointer items-center gap-3 px-3 py-2.5 text-sm select-none data-highlighted:bg-v2-row-hover"
                    >
                        {#snippet children({ selected })}
                            <span class="min-w-0 flex-1 truncate font-medium">{option.label}</span>
                            {#if selected}<Check size={16} class="shrink-0 text-v2-teal-text" aria-hidden="true" />{/if}
                        {/snippet}
                    </Combobox.Item>
                {:else}
                    <p class="px-3 py-2 text-sm text-v2-muted">{emptyText}</p>
                {/each}
            </Combobox.Content>
        </Combobox.Portal>
    </Combobox.Root>
    <!-- Ours rather than bits-ui's, so what's sent is exactly the chips -->
    {#each value as chosenValue (chosenValue)}
        <input type="hidden" {name} value={chosenValue} />
    {/each}
    {#if hint}
        <span id="{id}-hint" class="v2-hint">{hint}</span>
    {/if}
    {#if error}
        <span id="{id}-error" class="v2-error">{error}</span>
    {/if}
</div>
