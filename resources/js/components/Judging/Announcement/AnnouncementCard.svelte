<script lang="ts">
    import { withdraw } from "@/actions/App/Http/Controllers/DigitalJudge/AnnouncementController";
    import Avatar from "@/components/Avatar.svelte";
    import { announcements } from "@/lib/announcements.svelte";
    import { confirm } from "@/lib/confirm";
    import { timeAgo } from "@/lib/timeAgo.svelte";
    import { toastError, toastSuccess } from "@/lib/toast.svelte";
    import {
        announcementCardClass,
        announcementTextClass,
        isWithdrawn,
        type Announcement,
    } from "@/types/announcement";
    import type { Competition } from "@/types/base";
    import { useHttp } from "@inertiajs/svelte";
    import { Undo2 } from "@lucide/svelte";

    let {
        competition,
        announcement,
        isReferee = false,
        unread = false,
    }: {
        competition: Competition;
        announcement: Announcement;
        // Referees can withdraw it, and see who withdrew it
        isReferee?: boolean;
        // new since the sheet was last opened
        unread?: boolean;
    } = $props();

    const http = useHttp<{}, Announcement>();
    const withdrawn = $derived(isWithdrawn(announcement));
    const senderName = $derived(announcement.sender?.name ?? "Unknown");

    function time(iso: string): string {
        return new Date(iso).toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    async function withdrawIt() {
        const ok = await confirm({
            title: "Withdraw announcement?",
            description: "It disappears for every official. You can't undo this.",
            confirmLabel: "Withdraw",
            confirmVariant: "danger",
        });
        if (!ok) return;

        http.post(
            withdraw({
                competition: competition.id,
                announcement: announcement.id,
            }).url,
            {
                onSuccess(response) {
                    announcements.withdrawn(response);
                    toastSuccess("Announcement withdrawn");
                },
                onError() {
                    toastError(
                        "Couldn't withdraw the announcement. Please try again.",
                    );
                },
            },
        );
    }
</script>

<div
    class="flex items-start gap-3 rounded-xl border p-3 {announcementCardClass(
        announcement,
        unread,
    )}"
>
    <Avatar
        name={senderName}
        photo={announcement.sender?.photo}
        dimmed={withdrawn}
        class="size-8 shrink-0 text-xs"
    />
    <div class="flex min-w-0 flex-1 flex-col gap-1">
        <p
            class="text-sm break-words {announcementTextClass(announcement)}"
        >
            {announcement.text}
        </p>
        <p class="flex items-center gap-2 text-xs text-gray-500">
            {#if unread}
                <span class="size-1.5 rounded-full bg-se" aria-label="New"
                ></span>
            {/if}
            <span class="truncate">
                {senderName} · {timeAgo(
                    announcement.at,
                )}
            </span>
        </p>
        {#if announcement.withdrawn}
            <p class="text-xs text-gray-500">
                Withdrawn{announcement.withdrawn.by
                    ? ` by ${announcement.withdrawn.by.name}`
                    : ""} · {time(announcement.withdrawn.at)}
            </p>
        {:else if isReferee}
            <button
                type="button"
                class="flex w-fit cursor-pointer items-center gap-1 text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se disabled:opacity-50"
                disabled={http.processing}
                onclick={withdrawIt}
            >
                <Undo2 size={14} />
                Withdraw
            </button>
        {/if}
    </div>
</div>
