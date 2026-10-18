<script lang="ts">
    import { cn } from "@/utils/utils";

    let {
        code,
        size = "sm",
        voided = false,
        class: className = "",
    }: {
        code: string;
        size?: "sm" | "md" | "lg";
        voided?: boolean;
        // colour classes, e.g. from statusTileClass()
        class?: string;
    } = $props();

    const sizeClasses = {
        sm: "size-12",
        md: "size-14 text-lg",
        lg: "size-16 text-xl",
    };
</script>

<span
    class={cn(
        "flex shrink-0 items-center justify-center rounded-lg font-archivo font-bold transition-colors",
        sizeClasses[size],
        className,
    )}
    class:line-through={voided}
>
    {code}
</span>
