<script lang="ts">
    import { cn } from "@/utils/utils";
    import type { HTMLButtonAttributes } from "svelte/elements";

    let {
        label,
        variant: variant = "primary",
        icon: Icon = null,

        class: className = "",
        ...restProps
    }: {
        label?: string;
        variant?: "primary" | "secondary" | "success" | "danger" | "white";
        icon?: any;
        class?: string;
    } & HTMLButtonAttributes = $props();

    // get variant classes based on the variant prop
    let variantClasses = $derived.by(() => {
        switch (variant) {
            case "primary":
                return "bg-se text-white py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-se/10 hover:bg-se/10 group-hover:text-se hover:text-se transition-all group cursor-pointer shadow-md justify-between";

            case "secondary":
                return "bg-se/10 text-se py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-se hover:bg-se group-hover:text-white hover:text-white transition-all group cursor-pointer shadow-md justify-between";

            case "success":
                return "bg-green-500 text-white py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-white hover:bg-white group-hover:text-green-500 hover:text-green-500 border border-green-500 transition-all group cursor-pointer shadow-md justify-between";

            case "danger":
                return "bg-red-500 text-white py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-white hover:bg-white group-hover:text-red-500 hover:text-red-500 border border-red-500 transition-all group cursor-pointer shadow-md justify-between";

            case "white":
                return "bg-white text-black py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-black/80 hover:bg-black/80 group-hover:text-white hover:text-white transition-all group cursor-pointer shadow-md justify-between";

            default:
                return "bg-se/10 text-se hover:bg-se hover:text-white";
        }
    });
</script>

<button {...restProps} class={cn(variantClasses, className)}>
    {#if label}
        {label}
    {/if}

    {#if Icon}
        <Icon size={16} />
    {/if}
</button>
