<script lang="ts">
    import { toggleReferee } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import Button from "@/components/Button.svelte";
    import AlertsSheet from "@/components/AlertsSheet.svelte";
    import type { DialogControls } from "@/components/GenericDialog.svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import JudgeTabBar from "@/components/JudgeTabBar.svelte";
    import OfficialsSheet from "@/components/OfficialsSheet.svelte";
    import PokeAnimation from "@/components/PokeAnimation.svelte";
    import ToastViewport from "@/components/Toast/ToastViewport.svelte";
    import { alerts } from "@/lib/alerts.svelte";
    import confirmStore from "@/lib/confirm";
    import { presence } from "@/lib/presence.svelte";
    import { toast } from "@/lib/toast.svelte";
    import type { Competition } from "@/types/base";

    import { Link, page, router } from "@inertiajs/svelte";
    import { untrack, type Snippet } from "svelte";

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

    // stay in the competition's presence channel while on any of its pages;
    // the layout persists across visits so this survives navigation
    // Depend on the id and name, not the user object: every reload (e.g. a
    // page's usePoll) brings a new object, which would leave and rejoin the
    // channels and close the sheets
    const presenceCompetitionId = $derived(competition?.id);
    const userId = $derived(page.props.auth.user?.id);
    const userName = $derived(page.props.auth.user?.name);
    $effect(() => {
        if (!presenceCompetitionId || !userId || !userName) return;
        const me = { id: userId, name: userName };
        untrack(() => presence.join(presenceCompetitionId, me));
        return () => presence.leave();
    });

    // DQ/Penalty alerts for the competition: your own submissions changing
    // status, plus new ones to review if you're the referee
    const isReferee = $derived(!!page.props.judge?.isHeadRef);
    $effect(() => {
        if (!presenceCompetitionId || !userId || !userName) return;
        const me = { id: userId, name: userName };
        const referee = isReferee;
        untrack(() => alerts.start(presenceCompetitionId, me, referee));
        return () => alerts.stop();
    });

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

{#if competition}
    <OfficialsSheet {competition} />
    <AlertsSheet {competition} />
{/if}
<PokeAnimation />

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
