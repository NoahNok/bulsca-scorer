<script lang="ts">
    import { toggleReferee } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import Button from "@/components/Button.svelte";
    import type { DialogControls } from "@/components/GenericDialog.svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import JudgeTabBar from "@/components/JudgeTabBar.svelte";
    import ToastViewport from "@/components/Toast/ToastViewport.svelte";
    import confirmStore from "@/lib/confirm";
    import { toast } from "@/lib/toast.svelte";
    import type { Competition } from "@/types/base";

    import { Link, page, router } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    let {
        competition,
        children,
    }: {
        competition?: Competition;
        children?: Snippet;
    } = $props();

    // competition pages get the full tab bar, the dashboard and account
    // pages an "Account" / "Log out" one
    const showTabBar = $derived(
        !!competition ||
            page.component === "Judge/Dashboard" ||
            page.component === "Judge/Account",
    );

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

{#if page.props.env_local}
    <div class="fixed top-60 -left-12 bg-red-500 p-1 rotate-90 text-sm">
        <Link href={toggleReferee({ competition: competition ?? -1 })}
            >TOGGLE REFEREE</Link
        >
    </div>
{/if}

<!--
    With the tab bar showing, the page grows with its content and gets
    extra bottom padding so nothing ends up hidden behind the bar (more for
    the head ref, to cover the "Referee mode" band). Without it
    (e.g. login) keep the fixed height so pages can centre themselves.
-->
<div
    class="p-6 sm:max-w-[70%] xl:max-w-[50%] 2xl:max-w-[40%] sm:mx-auto {showTabBar
        ? competition && page.props.judge.isHeadRef
            ? 'min-h-screen pb-[calc(7.5rem+env(safe-area-inset-bottom))]'
            : 'min-h-screen pb-[calc(6rem+env(safe-area-inset-bottom))]'
        : 'h-screen'}"
>
    {@render children?.()}
</div>

{#if showTabBar}
    <JudgeTabBar {competition} />
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
