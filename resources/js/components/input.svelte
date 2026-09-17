<script lang="ts">
    import { cn } from "@/utils/utils";
    import { useId } from "bits-ui";

    let {
        value = $bindable(),
        class: className = "",
        type = "text",
        label = null,
        ...rest
    } = $props();

    const id = useId("input");
</script>

{#if label}
    <div>
        <label for={id}>{label}</label>
        <input
            {type}
            class={cn(
                "border  rounded-lg shadow-md   py-2 px-4 focus:border-se focus:ring-se/10 focus:ring-1 focus:outline-none transition-all w-full",
                className,
            )}
            {...rest}
            bind:value
            {id}
        />
    </div>
{:else}
    <input
        {type}
        class={cn(
            "border  rounded-lg shadow-md   py-2 px-4 focus:border-se focus:ring-se/10 focus:ring-1 focus:outline-none transition-all w-full",
            className,
        )}
        {...rest}
        bind:value
        {id}
    />
{/if}
