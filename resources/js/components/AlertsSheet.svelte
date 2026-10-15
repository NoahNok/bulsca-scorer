<script lang="ts">
    import EmptyState from "@/components/EmptyState.svelte";
    import { alertKey, alerts, alertText } from "@/lib/alerts.svelte";
    import { submissions } from "@/routes/judge/competition/violation";
    import type { Competition } from "@/types/base";
    import {
        statusBadgeClass,
        statusLabels,
        vtypeTileClass,
        type ViolationAlert,
    } from "@/types/violation";
    import { Link } from "@inertiajs/svelte";
    import { Bell, X } from "@lucide/svelte";
    import { Dialog } from "bits-ui";
    import { untrack } from "svelte";
    import { fade, fly } from "svelte/transition";

    let { competition }: { competition: Competition } = $props();

    // tick so "2 min ago" stays current while the sheet is open
    let now = $state(Date.now());
    $effect(() => {
        if (!alerts.sheetOpen) return;
        now = Date.now();
        const timer = setInterval(() => (now = Date.now()), 30_000);
        return () => clearInterval(timer);
    });

    // remember what was unread when the sheet opened, so the dots show for
    // this viewing, then mark everything read
    let unreadOnOpen = $state(new Set<ViolationAlert>());
    $effect(() => {
        if (!alerts.sheetOpen) return;
        untrack(() => {
            unreadOnOpen = new Set(alerts.items.filter((a) => !a.read));
            alerts.markAllRead();
        });
    });

    function timeAgo(iso: string): string {
        const seconds = Math.max(0, (now - new Date(iso).getTime()) / 1000);
        if (seconds < 60) return "Just now";
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} min ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} hr ago`;
        return new Date(iso).toLocaleDateString();
    }

    function close() {
        alerts.sheetOpen = false;
    }
</script>

<!-- Slide-out history of DQ/Penalty alerts, newest first, opened from the Alerts tab -->
<Dialog.Root bind:open={alerts.sheetOpen}>
    <Dialog.Portal>
        {#if alerts.sheetOpen}
            <div
                transition:fade={{ duration: 150 }}
                class="fixed inset-0 z-50 bg-black/50"
            ></div>
            <div
                transition:fly={{ x: 320, duration: 220 }}
                class="fixed inset-y-0 right-0 z-60 flex w-[min(85vw,22rem)] flex-col bg-white pb-[env(safe-area-inset-bottom)] shadow-lg"
            >
                <Dialog.Content class="flex min-h-0 flex-1 flex-col">
                    <div class="flex items-center justify-between border-b p-4">
                        <Dialog.Title
                            class="font-archivo flex items-center gap-2 text-base font-semibold text-gray-900"
                        >
                            <Bell size={18} class="text-se" />
                            Alerts
                        </Dialog.Title>
                        <div class="flex items-center gap-2">
                            {#if alerts.items.length}
                                <button
                                    type="button"
                                    class="cursor-pointer text-sm text-gray-600 hover:text-se"
                                    onclick={() => alerts.clear()}
                                >
                                    Clear
                                </button>
                            {/if}
                            <Dialog.Close
                                class="cursor-pointer rounded-full p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                                aria-label="Close"
                            >
                                <X size={18} />
                            </Dialog.Close>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto p-4">
                        {#if !alerts.items.length}
                            <EmptyState
                                icon={Bell}
                                title="No alerts yet"
                                description="Updates on DQs and penalties show up here while you're online."
                            >
                                <Link
                                    href={submissions(competition)}
                                    class="mt-2 text-sm font-semibold text-teal-700 hover:underline"
                                    onclick={close}
                                >
                                    Go to submissions
                                </Link>
                            </EmptyState>
                        {:else}
                            <div
                                class="divide-y overflow-hidden rounded-lg border"
                            >
                                {#each alerts.items as alert (alertKey(alert))}
                                    <Link
                                        href={alerts.href(alert)}
                                        onclick={close}
                                        class="flex w-full items-start gap-3 px-3 py-2 text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset {unreadOnOpen.has(
                                            alert,
                                        )
                                            ? 'bg-se/5'
                                            : 'bg-white'}"
                                    >
                                        <span
                                            class="font-archivo flex h-9 min-w-9 shrink-0 items-center justify-center rounded-md px-1 text-xs font-semibold {vtypeTileClass(
                                                alert.vtype,
                                            )}"
                                        >
                                            {alert.code}
                                        </span>
                                        <span
                                            class="flex min-w-0 flex-1 flex-col gap-0.5"
                                        >
                                            <span class="text-sm text-gray-900"
                                                >{alertText(alert)}</span
                                            >
                                            <span
                                                class="flex items-center gap-2 text-xs text-gray-500"
                                            >
                                                {#if unreadOnOpen.has(alert)}
                                                    <span
                                                        class="size-1.5 rounded-full bg-se"
                                                        aria-label="New"
                                                    ></span>
                                                {/if}
                                                {timeAgo(alert.at)}
                                            </span>
                                        </span>
                                        <span
                                            class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {statusBadgeClass(
                                                alert.status,
                                            )}"
                                        >
                                            {statusLabels[alert.status]}
                                        </span>
                                    </Link>
                                {/each}
                            </div>
                        {/if}
                    </div>
                </Dialog.Content>
            </div>
        {/if}
    </Dialog.Portal>
</Dialog.Root>
