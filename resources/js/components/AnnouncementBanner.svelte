<script lang="ts">
    import { alerts } from "@/lib/alerts.svelte";
    import { announcements } from "@/lib/announcements.svelte";
    import { timeAgo } from "@/lib/timeAgo.svelte";
    import { announcementBannerClass } from "@/types/announcement";
    import { cn } from "@/utils/utils";
    import { Megaphone, X } from "@lucide/svelte";
    import { prefersReducedMotion } from "svelte/motion";
    import { slide } from "svelte/transition";

    let { class: className = "" }: { class?: string } = $props();

    const banner = $derived(announcements.banner);
</script>

<!-- The newest Announcement this device hasn't dismissed, pinned under the header -->
{#if banner}
    {#key banner.id}
        <div
            role="status"
            transition:slide={{
                duration: prefersReducedMotion.current ? 0 : 200,
            }}
            class={cn(
                "sticky top-2 z-20 mb-4 flex items-start gap-3 rounded-xl border p-3 shadow-sm",
                announcementBannerClass,
                className,
            )}
        >
            <button
                type="button"
                class="flex min-w-0 flex-1 cursor-pointer items-start gap-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                onclick={() => (alerts.sheetOpen = true)}
            >
                <Megaphone size={16} class="mt-0.5 shrink-0 text-amber-700" />
                <span class="flex min-w-0 flex-col gap-0.5">
                    <span class="font-archivo truncate text-xs text-amber-800">
                        Announcement{banner.sender
                            ? ` · ${banner.sender.name}`
                            : ""} · {timeAgo(banner.at)}
                    </span>
                    <span class="text-sm break-words text-gray-900"
                        >{banner.text}</span
                    >
                </span>
            </button>
            <button
                type="button"
                class="shrink-0 cursor-pointer rounded-full p-1 text-amber-800 hover:bg-amber-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                aria-label="Dismiss announcement"
                onclick={() => announcements.dismiss(banner)}
            >
                <X size={16} />
            </button>
        </div>
    {/key}
{/if}
