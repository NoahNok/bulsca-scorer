<script lang="ts">
    import { cn } from "@/utils/utils";
    import { useId } from "bits-ui";

    let {
        value = $bindable(),
        class: className = "",
        type = "text",
        label = null,
        hint = null,
        // "default" is the original bordered/shadowed style, "soft" is a
        // lighter, compact style for denser forms
        variant = "default",
        // only used when type is "textarea"
        rows = 4,
        ...rest
    } = $props();

    const id = useId("input");

    let inputClass = $derived(
        variant === "soft"
            ? "w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 shadow-xs transition-all hover:border-gray-400 focus:border-se focus:ring-2 focus:ring-se/20 focus:outline-none"
            : "border  rounded-lg shadow-md   py-2 px-4 focus:border-se focus:ring-se/10 focus:ring-1 focus:outline-none transition-all w-full",
    );
</script>

{#snippet field()}
    {#if type === "textarea"}
        <textarea
            class={cn(inputClass, "resize-y", className)}
            {rows}
            {...rest}
            bind:value
            {id}
        ></textarea>
    {:else}
        <input
            {type}
            class={cn(inputClass, className)}
            {...rest}
            bind:value
            {id}
        />
    {/if}
{/snippet}

{#if label}
    <div>
        <label
            for={id}
            class={variant === "soft"
                ? "mb-1 block text-xs font-semibold text-gray-600"
                : ""}
        >
            {label}
            {#if variant === "soft" && rest.required}
                <span class="text-red-500">*</span>
            {/if}
        </label>
        {@render field()}
        {#if hint}
            <p class="mt-1 text-xs text-gray-400">{hint}</p>
        {/if}
    </div>
{:else}
    {@render field()}
{/if}
