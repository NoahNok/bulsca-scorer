<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import ActionStatusModal from "@/components/ActionStatusModal.svelte";
    import Button from "@/components/Button.svelte";
    import SignOffCheckbox from "@/components/Judging/SignOffCheckbox.svelte";
    import type { Competition } from "@/types/base";
    import { cn } from "@/utils/utils";
    import { Link, useHttp } from "@inertiajs/svelte";
    import { House, ShieldCheck, TriangleAlert } from "@lucide/svelte";

    let {
        competition,
        url,
        eventName,
        hasWarnings = false,
        class: className = "",
    }: {
        competition: Competition;
        // Wayfinder store URL for this event
        url: string;
        eventName: string;
        hasWarnings?: boolean;
        class?: string;
    } = $props();

    let modalRef: ActionStatusModal | null = null;
    let errorMessage: string | null = null;

    const http = useHttp<{}, { confirmed: boolean }>().transform(() => ({
        check_conf: true,
    }));

    async function submit(e: SubmitEvent) {
        e.preventDefault();

        errorMessage = null;

        const req = http.post(url, {
            onHttpException(response) {
                const data = response.data as unknown;
                if (data && typeof data === "object" && "message" in data) {
                    errorMessage = String(data.message);
                }
            },
        });

        const success = await modalRef?.showFor(req);

        if (success) {
            modalRef?.setTitleAndMessage(
                "Results Confirmed",
                `${eventName} results have been confirmed.`,
            );
        } else if (errorMessage) {
            modalRef?.setMessage(errorMessage);
        }
    }
</script>

<form onsubmit={submit} class={cn("flex flex-col gap-4", className)}>
    {#if hasWarnings}
        <p class="flex items-start gap-2 text-sm text-amber-800">
            <TriangleAlert size={16} class="mt-0.5 shrink-0" />
            Some results are missing or have pending DQs/penalties. Check the
            summary at the top before confirming.
        </p>
    {/if}

    <SignOffCheckbox id="check-conf" name="check_conf">
        I have checked every result above for {eventName} and confirm they are
        correct. Confirming acts as signing them off digitally.
    </SignOffCheckbox>

    <Button
        type="submit"
        label="Confirm Results"
        class="w-full"
        icon={ShieldCheck}
        loading={http.processing}
    />
</form>

<ActionStatusModal
    bind:this={modalRef}
    title="Confirming Results"
    message="Confirming {eventName} results..."
>
    {#snippet success()}
        <Link href={home({ competition: competition.id })} class="w-full">
            <Button
                class="mb-0! w-full"
                label="Back to competition"
                type="button"
                icon={House}
            />
        </Link>
    {/snippet}
</ActionStatusModal>
