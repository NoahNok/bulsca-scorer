<script lang="ts">
    import { CheckCircle2, CircleAlert, Info, X } from "@lucide/svelte";

    import { dismissToast } from "@/lib/toast.svelte";
    import type { ToastItem } from "@/lib/toast.svelte";
    import { fly } from "svelte/transition";

    function iconFor(toast: ToastItem) {
        if (toast.variant === "success") {
            return CheckCircle2;
        }

        if (toast.variant === "error") {
            return CircleAlert;
        }

        return Info;
    }

    function classesFor(toast: ToastItem): string {
        if (toast.variant === "success") {
            return "border-emerald-200 bg-emerald-50 text-emerald-900 ";
        }

        if (toast.variant === "error") {
            return "border-red-200 bg-red-50 text-red-900 ";
        }

        return "border-border bg-card text-card-foreground";
    }

    export let toast: ToastItem;

    const options = {
        defaultProtocol: "https",
        target: "_blank",
        rel: "noopener noreferrer",
        className: "text-blue-500 hover:underline",
    };

    $: html = toast?.description ?? "";

    let Icon = iconFor(toast);
</script>

<div
    class={`pointer-events-auto rounded-lg border p-3 shadow-sm ${classesFor(toast)}`}
    role="status"
    aria-live="polite"
    in:fly={{ x: 100, duration: 300 }}
    out:fly={{ x: 100, duration: 300 }}
>
    <div class="flex items-start gap-3">
        <Icon class="mt-0.5 size-4 shrink-0" />

        <div class="min-w-0 flex-1">
            <p class="text-sm font-medium">{toast.title}</p>
            {#if toast.description}
                <p class="mt-1 text-xs opacity-80">
                    {@html html}
                </p>
            {/if}
        </div>

        <button
            type="button"
            class="opacity-70 transition-opacity hover:opacity-100"
            onclick={() => dismissToast(toast.id)}
            aria-label="Dismiss notification"
        >
            <X class="size-4" />
        </button>
    </div>
</div>
