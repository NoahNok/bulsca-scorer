<script lang="ts">
    import { cn } from "@/utils/utils";
    import type { LinkComponentBaseProps } from "@inertiajs/core";
    import { Link } from "@inertiajs/svelte";
    import { ArrowLeft } from "@lucide/svelte";

    let {
        href,
        label,
        class: className = "",
    }: {
        href: LinkComponentBaseProps["href"];
        label: string;
        class?: string;
    } = $props();
</script>

<Link
    {href}
    class={cn(
        "inline-flex w-fit items-center gap-1 text-sm text-gray-600 hover:text-se",
        className,
    )}><ArrowLeft size={14} /> {label}</Link
>
