<script lang="ts">
    import { Dialog } from "bits-ui";
    import { fade, fly } from "svelte/transition";
    import Button from "@/components/Button.svelte";
    import type { Snippet } from "svelte";
    import { X } from "@lucide/svelte";

    export type DialogControls = {
        close: () => void;
        setTitle: (title: string) => void;
    };

    let {
        open = $bindable(false),
        title = "",
        cancelLabel = "Close",
        cancelVariant = "white",
        triggerLabel,
        triggerVariant = "primary",
        triggerClass = "",
        triggerType = "button",
        triggerIcon,
        allowManualClose = true,
        withX = false,
        onCancel,
        children,
        footer,
    }: {
        open?: boolean;
        title?: string;
        cancelLabel?: string;
        cancelVariant?:
            | "primary"
            | "secondary"
            | "success"
            | "danger"
            | "white";
        triggerLabel?: string;
        triggerVariant?: "primary" | "secondary" | "success" | "danger";
        triggerClass?: string;
        triggerType?: "button" | "submit" | "reset";
        triggerIcon?: any;
        allowManualClose?: boolean;
        withX?: boolean;
        onCancel?: () => void;
        children?: Snippet;
        footer?: Snippet<[DialogControls]>;
    } = $props();

    const handleCancel = () => {
        onCancel?.();
        open = false;
    };

    const close = () => {
        open = false;
    };

    const setTitle = (newTitle: string) => {
        title = newTitle;
    };
</script>

<Dialog.Root bind:open>
    {#if triggerLabel}
        <Button
            variant={triggerVariant}
            label={triggerLabel}
            class={triggerClass}
            type={triggerType}
            icon={triggerIcon}
            onclick={(event) => {
                event.preventDefault();
                event.stopPropagation();
                open = true;
            }}
        />
    {/if}

    <Dialog.Portal>
        {#if open}
            <div
                transition:fade={{ duration: 150 }}
                class="fixed inset-0 z-50 bg-black/50"
            ></div>
        {/if}
        {#if open}
            <div
                transition:fly={{ y: 12, duration: 180 }}
                class="fixed left-1/2 top-1/2 z-60 w-[min(90vw,28rem)] -translate-x-1/2 -translate-y-1/2 rounded-xl bg-white p-6 shadow-lg max-h-[80vh] overflow-y-auto"
            >
                <Dialog.Content
                    escapeKeydownBehavior={allowManualClose
                        ? "close"
                        : "ignore"}
                    interactOutsideBehavior={allowManualClose
                        ? "close"
                        : "ignore"}
                    trapFocus={true}
                >
                    {#if title}
                        <Dialog.Title
                            class="text-lg font-semibold text-gray-900 flex items-center justify-between"
                        >
                            {title}
                            {#if withX}
                                <X
                                    onclick={handleCancel}
                                    class="cursor-pointer hover:animate-pulse"
                                />
                            {/if}
                        </Dialog.Title>
                    {/if}

                    <Dialog.Description class="mt-2 text-sm text-gray-600   ">
                        {@render children?.()}
                    </Dialog.Description>

                    {#if allowManualClose || footer}
                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex">
                                {#if allowManualClose}
                                    <Dialog.Close>
                                        <Button
                                            variant={cancelVariant}
                                            label={cancelLabel}
                                            type="button"
                                            onclick={handleCancel}
                                        />
                                    </Dialog.Close>
                                {/if}
                            </div>

                            <div class="flex items-center gap-3">
                                {@render footer?.({ close, setTitle })}
                            </div>
                        </div>
                    {/if}
                </Dialog.Content>
            </div>
        {/if}
    </Dialog.Portal>
</Dialog.Root>
