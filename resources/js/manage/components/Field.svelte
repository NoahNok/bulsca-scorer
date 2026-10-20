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
    <div class="f-control">
        <input
            {id}
            class={["v2-input", error && "is-invalid", type === "password" && "f-has-toggle"]}
            type={type === "password" && shown ? "text" : type}
            {value}
            aria-invalid={error ? "true" : undefined}
            aria-describedby={describedBy}
            {...rest}
        />
        {#if type === "password"}
            <button
                type="button"
                class="f-toggle"
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

<style>
    .f-control {
        position: relative;
        display: flex;
    }

    .f-control input {
        flex: 1;
        min-width: 0;
    }

    .f-control input:read-only {
        color: var(--v2-muted);
        cursor: default;
    }

    .f-has-toggle {
        padding-right: 2.75rem;
    }

    .f-toggle {
        position: absolute;
        top: 0.375rem;
        right: 0.375rem;
        display: grid;
        place-items: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.375rem;
        color: var(--v2-muted);
        cursor: pointer;
    }

    .f-toggle:hover,
    .f-toggle[aria-pressed="true"] {
        color: var(--v2-ink);
    }
</style>
