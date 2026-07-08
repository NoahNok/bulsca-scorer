<script lang="ts">
    import { cn } from "@/utils/utils";

    let {
        value = $bindable(""),
        class: className = "",
        type = "text",
        ...rest
    } = $props();
</script>

<input
    {type}
    class={cn(
        "border border-se/20 rounded-lg shadow-md   py-2 px-4 focus:border-se focus:ring-se/10 focus:ring-1 focus:outline-none transition-all w-full",
        className,
    )}
    {...rest}
    bind:value
/>
