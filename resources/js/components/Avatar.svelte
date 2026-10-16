<script lang="ts">
    import type { ProfilePhoto } from "@/types/presence";
    import { cn } from "@/utils/utils";
    import type { Snippet } from "svelte";

    let {
        name,
        photo = null,
        dimmed = false,
        class: className = "",
        children,
    }: {
        name: string;
        photo?: ProfilePhoto | null;
        // offline officials: grey initials and a faded photo
        dimmed?: boolean;
        // size and text classes, e.g. "size-20 text-2xl" (defaults to size-9)
        class?: string;
        // overlays such as the presence dot
        children?: Snippet;
    } = $props();

    const initials = $derived(
        name
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map((part) => part[0].toUpperCase())
            .join(""),
    );

    // the photo fades in over the initials once it has downloaded, and is
    // dropped if it fails, so slow pool wifi only ever shows initials
    // keyed by URL so a new photo starts hidden again
    let loadedSrc = $state<string | null>(null);
    let failedSrc = $state<string | null>(null);
    const loaded = $derived(!!photo && loadedSrc === photo.jpg);
    const failed = $derived(!!photo && failedSrc === photo.jpg);
</script>

<span
    class={cn(
        "relative flex size-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold",
        dimmed ? "text-gray-400" : "text-gray-600",
        className,
    )}
>
    {initials}
    {#if photo && !failed}
        <picture>
            <source type="image/webp" srcset={photo.webp} />
            <img
                src={photo.jpg}
                alt=""
                width="192"
                height="192"
                loading="lazy"
                decoding="async"
                fetchpriority="low"
                class="absolute inset-0 size-full rounded-full object-cover transition-opacity duration-200 {loaded
                    ? dimmed
                        ? 'opacity-60'
                        : 'opacity-100'
                    : 'opacity-0'}"
                onload={() => (loadedSrc = photo.jpg)}
                onerror={() => (failedSrc = photo.jpg)}
            />
        </picture>
    {/if}
    {@render children?.()}
</span>
