<script lang="ts">
    import Button from "@/components/Button.svelte";
    import { install } from "@/lib/pwa.svelte";
    import { cn } from "@/utils/utils";
    import { Smartphone } from "@lucide/svelte";
    import { slide } from "svelte/transition";

    let { class: className = "" }: { class?: string } = $props();

    const show = $derived(
        !install.isStandalone &&
            !install.dismissed &&
            (install.canPrompt || install.isIos),
    );
</script>

{#if show}
    <div
        transition:slide={{ duration: 150 }}
        class={cn("rounded-xl border border-se/40 bg-se/5 p-3 shadow-sm", className)}
    >
        <div class="flex items-center gap-3">
            <span
                class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-se text-white"
            >
                <Smartphone size={18} />
            </span>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-gray-900">Add Digital Judge to your phone</p>
                {#if install.canPrompt}
                    <p class="text-sm text-gray-500">Open it from your home screen, like an app.</p>
                {:else}
                    <p class="text-sm text-gray-500">
                        Tap <strong class="font-semibold text-gray-700">Share</strong>, then
                        <strong class="font-semibold text-gray-700">Add to Home Screen</strong>.
                    </p>
                {/if}
            </div>
        </div>
        <div class="mt-3 flex items-center justify-end gap-3">
            <button
                type="button"
                class="cursor-pointer text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                onclick={() => install.dismiss()}
            >
                Not now
            </button>
            {#if install.canPrompt}
                <Button
                    variant="primary"
                    class="ml-0"
                    label="Install"
                    type="button"
                    onclick={() => install.install()}
                />
            {/if}
        </div>
    </div>
{/if}
