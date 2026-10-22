<!--
    A v2 field (.v2-field): label, input, hint and error, with aria-invalid and aria-describedby wired up.
    A password field gets a show/hide button.
-->
<script lang="ts">
    import { Eye, EyeOff } from "@lucide/svelte";
    import type { HTMLInputAttributes } from "svelte/elements";

    let {
        id,
        label,
        value,
        type = "text",
        error,
        hint,
        ...rest
    }: {
        id: string;
        label: string;
        value?: string;
        type?: string;
        error?: string;
        hint?: string;
    } & Omit<HTMLInputAttributes, "id" | "value" | "type"> = $props();

    let shown = $state(false);

    const describedBy = $derived([error && `${id}-error`, hint && `${id}-hint`].filter(Boolean).join(" ") || undefined);
</script>

<div class="v2-field">
    <label for={id}>{label}</label>
    <div class="relative flex">
        <input
            {id}
            class={[
                "v2-input min-w-0 flex-1 read-only:cursor-default read-only:text-v2-muted",
                error && "is-invalid",
                type === "password" && "pr-11",
            ]}
            type={type === "password" && shown ? "text" : type}
            {value}
            aria-invalid={error ? "true" : undefined}
            aria-describedby={describedBy}
            {...rest}
        />
        {#if type === "password"}
            <button
                type="button"
                class="absolute top-1.5 right-1.5 grid size-8 cursor-pointer place-items-center rounded-md text-v2-muted hover:text-v2-ink aria-pressed:text-v2-ink"
                aria-label="Show password"
                aria-pressed={shown}
                aria-controls={id}
                onclick={() => (shown = !shown)}
            >
                {#if shown}
                    <EyeOff size={18} aria-hidden="true" />
                {:else}
                    <Eye size={18} aria-hidden="true" />
                {/if}
            </button>
        {/if}
    </div>
    {#if hint}
        <span id="{id}-hint" class="v2-hint">{hint}</span>
    {/if}
    {#if error}
        <span id="{id}-error" class="v2-error">{error}</span>
    {/if}
</div>
