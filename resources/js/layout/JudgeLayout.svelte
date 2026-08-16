<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import Button from "@/components/Button.svelte";
    import type { DialogControls } from "@/components/GenericDialog.svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import ToastViewport from "@/components/Toast/ToastViewport.svelte";
    import confirmStore from "@/lib/confirm";
    import { toast } from "@/lib/toast.svelte";

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

<div
    class=" p-6 sm:max-w-[70%] xl:max-w-[50%] 2xl:max-w-[40%] sm:mx-auto h-screen"
>
    {@render children?.()}
</div>

{#if competition}
    <div class="fixed bottom-0 left-0 w-full flex items-center p-4 z-99">
        {#if nav}
            {@render nav?.()}
        {:else}
            <Link href={home(competition)} class="mr-auto">
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
