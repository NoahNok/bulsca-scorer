<script lang="ts">
    import { cn } from "@/utils/utils";

    let {
        label,
        variant: variant = "primary",
        icon: Icon = null,

        class: className = "",
    }: {
        label?: string;
        variant?: "primary" | "secondary" | "success" | "danger";
        icon?: any;
        class?: string;
    } = $props();

    // get variant classes based on the variant prop
    let variantClasses = "";
    switch (variant) {
        case "primary":
            variantClasses =
                "bg-se text-white py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-se/10 hover:bg-se/10 group-hover:text-se hover:text-se transition-all group cursor-pointer shadow-md justify-between";
            break;
        case "secondary":
            variantClasses =
                "bg-se/10 text-se py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-se hover:bg-se group-hover:text-white hover:text-white transition-all group cursor-pointer shadow-md justify-between";
            break;
        case "success":
            variantClasses =
                "bg-green-500 text-white py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-white hover:bg-white group-hover:text-green-500 hover:text-green-500 border border-green-500 transition-all group cursor-pointer shadow-md justify-between";
            break;
        case "danger":
            variantClasses =
                "bg-red-500 text-white py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto group-hover:bg-white hover:bg-white group-hover:text-red-500 hover:text-red-500 border border-red-500 transition-all group cursor-pointer shadow-md justify-between";
            break;
        default:
            variantClasses = "bg-se/10 text-se hover:bg-se hover:text-white";
    }
</script>

<button class={cn(variantClasses, className)}>
    {#if label}
        {label}
    {/if}

    {#if Icon}
        <Icon size={16} />
    {/if}
</button>
