<script lang="ts">
    import {
        home,
        toggleReferee,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import Button from "@/components/Button.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import type { DialogControls } from "@/components/GenericDialog.svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import ToastViewport from "@/components/Toast/ToastViewport.svelte";
    import confirmStore from "@/lib/confirm";
    import { toast } from "@/lib/toast.svelte";
    import { index } from "@/routes/judge";

    import { event } from "@/routes/live/dqs";
    import type { Competition, SERC, Event } from "@/types/base";

    import { Link, page, router } from "@inertiajs/svelte";
    import { House } from "@lucide/svelte";
    import type { Snippet } from "svelte";

    let {
        competition,
        nav,
        children,
    }: {
        competition?: Competition;
        nav?: Snippet;
        children?: Snippet;
    } = $props();

    router.on("flash", (event) => {
        if (event.detail.flash.toast) {
            toast({
                ...event.detail.flash.toast,
                manualClose: false,
            });
        }
    });

    const handleStoreConfirm = (dialog: DialogControls) => {
        $confirmStore?.resolve?.(true);
        dialog.close();
        confirmStore.set({ open: false });
    };

    const handleStoreCancel = () => {
        $confirmStore?.resolve?.(false);
        confirmStore.set({ open: false });
    };
</script>

{#if page.props.judge.isHeadRef}
    <div
        class="fixed top-0 left-0 w-screen bg-se text-white font-archivo text-center"
    >
        <p>Referee Mode</p>
    </div>
{/if}

{#if page.props.env_local}
    <div class="fixed top-60 -left-12 bg-red-500 p-1 rotate-90 text-sm">
        <Link href={toggleReferee({ competition: competition ?? -1 })}
            >TOGGLE REFEREE</Link
        >
    </div>
{/if}

<!--
    With the bottom nav showing, the page grows with its content and gets
    extra bottom padding so nothing ends up hidden behind the nav. Without it
    (e.g. login) keep the fixed height so pages can centre themselves.
-->
<div
    class="p-6 sm:max-w-[70%] xl:max-w-[50%] 2xl:max-w-[40%] sm:mx-auto {competition
        ? 'min-h-screen pb-28'
        : 'h-screen'}"
>
    {@render children?.()}
</div>

{#if competition}
    <div class="fixed bottom-0 left-0 w-full flex items-center p-4 z-99">
        {#if nav}
            {@render nav?.()}
        {:else}
            <Link href={home({ competition: competition })} class="mr-auto">
                <Button
                    label="Home"
                    variant="white"
                    class="w-full py-1 border border-black/10"
                />
            </Link>

            <Link herf="?" class="mx-auto">
                <Button
                    label="DQ/Penalty"
                    variant="white"
                    class="w-full py-1 border border-black/10"
                />
            </Link>

            <Link href="?" class="ml-auto">
                <Button
                    label="Help"
                    variant="white"
                    class="w-full py-1 border border-black/10"
                />
            </Link>
        {/if}
    </div>
{/if}

<ToastViewport />

{#if $confirmStore?.open}
    <GenericDialog
        title={$confirmStore.title}
        cancelLabel={$confirmStore.cancelLabel ?? "Cancel"}
        triggerLabel={undefined}
        triggerVariant={undefined}
        triggerClass={undefined}
        triggerType={"button"}
        triggerIcon={undefined}
        onCancel={handleStoreCancel}
        bind:open={$confirmStore.open}
    >
        <p>{$confirmStore.description}</p>
        {#snippet footer(dialog)}
            <Button
                variant={$confirmStore.confirmVariant ?? "danger"}
                label={$confirmStore.confirmLabel ?? "Confirm"}
                type="button"
                onclick={() => handleStoreConfirm(dialog)}
            />
        {/snippet}
    </GenericDialog>
{/if}
