<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/DigitalJudge/AnnouncementController";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
    import { announcements } from "@/lib/announcements.svelte";
    import { confirm } from "@/lib/confirm";
    import { toastError, toastSuccess } from "@/lib/toast.svelte";
    import {
        ANNOUNCEMENT_MAX_LENGTH,
        type Announcement,
    } from "@/types/announcement";
    import type { Competition } from "@/types/base";
    import { cn } from "@/utils/utils";
    import { useHttp } from "@inertiajs/svelte";
    import { Megaphone, Send } from "@lucide/svelte";

    let {
        competition,
        class: className = "",
    }: { competition: Competition; class?: string } = $props();

    const http = useHttp<{ text: string }, Announcement>({ text: "" });

    async function send() {
        const text = http.text.trim();
        if (!text) {
            toastError("Write the announcement first.");
            return;
        }

        // there's no editing, so check before it goes to everyone
        const ok = await confirm({
            title: "Send announcement?",
            description: `Every official on ${competition.name} will see "${text}". You can withdraw it, but not edit it.`,
            confirmLabel: "Send",
            confirmVariant: "primary",
        });
        if (!ok) return;

        http.post(store(competition.id).url, {
            onSuccess(response) {
                announcements.sent(response);
                http.reset();
                toastSuccess("Announcement sent");
            },
            onError() {
                toastError("Couldn't send the announcement. Please try again.");
            },
        });
    }
</script>

<!-- Referee-only: send an Announcement to every Official on the competition -->
<div class={cn("rounded-xl border border-se/40 bg-se/5 p-4", className)}>
    <p
        class="font-archivo mb-3 flex items-center gap-2 text-sm font-semibold uppercase text-gray-900"
    >
        <Megaphone size={16} class="text-se" />
        Announce to all officials
    </p>
    <div class="grid gap-3">
        <Input
            type="textarea"
            variant="soft"
            rows={3}
            maxlength={ANNOUNCEMENT_MAX_LENGTH}
            placeholder="e.g. Heat 4 delayed 10 min"
            aria-label="Announcement"
            hint="{http.text.length}/{ANNOUNCEMENT_MAX_LENGTH}"
            bind:value={http.text}
        />
        <Button
            type="button"
            label="Send announcement"
            icon={Send}
            class="w-full"
            loading={http.processing}
            disabled={http.processing}
            onclick={send}
        />
    </div>
</div>
