<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/DigitalJudge/AnnouncementController";
    import Button from "@/components/Button.svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import Input from "@/components/input.svelte";
    import { announcements } from "@/lib/announcements.svelte";
    import { toastError, toastSuccess } from "@/lib/toast.svelte";
    import {
        ANNOUNCEMENT_MAX_LENGTH,
        type Announcement,
    } from "@/types/announcement";
    import type { Competition } from "@/types/base";
    import { useHttp } from "@inertiajs/svelte";
    import { Info, Send } from "@lucide/svelte";

    let {
        competition,
        open = $bindable(false),
    }: { competition: Competition; open?: boolean } = $props();

    const http = useHttp<{ text: string }, Announcement>({ text: "" });

    function send() {
        const text = http.text.trim();
        if (!text) {
            toastError("Write the announcement first.");
            return;
        }

        http.post(store(competition.id).url, {
            onSuccess(response) {
                announcements.sent(response);
                http.reset();
                open = false;
                toastSuccess("Announcement sent");
            },
            onError() {
                toastError("Couldn't send the announcement. Please try again.");
            },
        });
    }
</script>

<!--
    Referee-only modal for sending an Announcement to every Official on the
    competition, opened from the Alerts sheet. Sending is the confirm step:
    the warning sits right above it, since Announcements can't be edited
-->
<GenericDialog
    bind:open
    title="Announce to all officials"
    cancelLabel="Cancel"
    withX
>
    <div class="grid gap-3 text-left">
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
        <p class="flex gap-2 rounded-lg bg-gray-50 p-3 text-sm text-gray-600">
            <Info size={16} class="mt-0.5 shrink-0 text-gray-500" />
            Every official on {competition.name} will see this. You can withdraw
            it, but not edit it.
        </p>
    </div>

    {#snippet footer()}
        <Button
            type="button"
            label="Send"
            icon={Send}
            loading={http.processing}
            disabled={http.processing}
            onclick={send}
        />
    {/snippet}
</GenericDialog>
