<script lang="ts">
    import { cn } from "@/utils/utils";

    let {
        label = "Live",
        title = "Updates automatically",
        class: className = "",
    }: {
        label?: string;
        title?: string;
        class?: string;
    } = $props();
</script>

<span
    class={cn("inline-flex items-center gap-1.5 text-xs text-gray-500", className)}
    {title}
>
    <span class="relative flex size-2">
        <span
            class="absolute inline-flex size-full animate-ping rounded-full bg-se opacity-60"
        ></span>
        <span class="relative inline-flex size-2 rounded-full bg-se"></span>
    </span>
    {label}
</span>
