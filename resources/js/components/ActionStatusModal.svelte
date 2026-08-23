<script lang="ts">
    import { onMount, onDestroy } from "svelte";
    import { tick } from "svelte";
    import type { Snippet } from "svelte";
    import { Check, X as Cross } from "@lucide/svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import Button from "./Button.svelte";

    let {
        open = $bindable(false),
        title = "Processing",
        message = "Please wait...",
        monitorRouter = true,
        autoCloseMs = 1400,
        success,
    }: {
        open?: boolean;
        title?: string;
        message?: string;
        monitorRouter?: boolean;
        autoCloseMs?: number;
        success: Snippet;
    } = $props();

    type Status = "idle" | "loading" | "success" | "error";
    let status = $state<Status>("idle");

    let _currentId = 0;

    export function showLoading() {
        status = "loading";
        open = true;
    }

    export function showSuccess() {
        status = "success";
    }

    export function showError() {
        status = "error";
    }

    function scheduleClose() {
        const id = ++_currentId;
        setTimeout(() => {
            if (id === _currentId) {
                open = false;
                status = "idle";
            }
        }, autoCloseMs);
    }

    // Exposed helper to monitor a Promise (e.g. router.post / visit)
    export async function showFor<T>(p: Promise<T>) {
        try {
            showLoading();
            await p;
            // give small delay so spinner is visible
            await tick();

            // wait .5s so it doesn't feel like a flash
            await new Promise((resolve) => setTimeout(resolve, 500));

            showSuccess();
            return true;
        } catch (e) {
            // wait .5s so it doesn't feel like a flash
            await new Promise((resolve) => setTimeout(resolve, 500));
            showError();
            setMessage("Something went wrong! Click close and try again.");
            return false;
        }
    }

    // Expose helper to change the message dynamically
    export function setMessage(msg: string) {
        message = msg;
    }

    // Expose helper to change the title dynamically
    export function setTitle(t: string) {
        title = t;
    }

    // Expose helper to change both title and message dynamically
    export function setTitleAndMessage(t: string, msg: string) {
        title = t;
        message = msg;
    }

    // Listen for Inertia document events if requested
    function handleInertiaStart() {
        showLoading();
    }

    function handleInertiaFinish(e: any) {
        // e.detail.visit.completed === true indicates success
        const visit = e?.detail?.visit;
        if (visit?.completed) showSuccess();
        else showError();
    }

    // onMount(() => {
    //     if (monitorRouter && typeof document !== "undefined") {
    //         document.addEventListener("inertia:start", handleInertiaStart);
    //         document.addEventListener("inertia:finish", handleInertiaFinish);
    //     }
    // });

    // onDestroy(() => {
    //     if (monitorRouter && typeof document !== "undefined") {
    //         document.removeEventListener("inertia:start", handleInertiaStart);
    //         document.removeEventListener("inertia:finish", handleInertiaFinish);
    //     }
    // });

    function handleCancel() {
        open = false;
        status = "idle";
    }
</script>

<GenericDialog
    bind:open
    {title}
    onCancel={handleCancel}
    allowManualClose={false}
>
    <p class="mt-2 text-sm text-gray-600">{message}</p>

    <div class="mt-6 flex flex-col items-center gap-4">
        <div class="relative w-24 h-24 flex items-center justify-center">
            <!-- Spinner (fades/scales out) -->
            <div
                class="spinner-wrap flex items-center justify-center"
                class:hidden={status !== "loading" && status !== "idle"}
            >
                <div
                    class="spinner-ring w-12 h-12 rounded-full flex items-center justify-center relative"
                    aria-hidden="true"
                ></div>
            </div>

            <!-- Success (draw animation) - keep rounded bg style -->
            <div
                class="result-icon check absolute w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center"
                class:show={status === "success"}
                aria-hidden="true"
            >
                <svg viewBox="0 0 52 52" width="36" height="36">
                    <path
                        class="check-path"
                        fill="none"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14 27 l8 8 l16 -16"
                    />
                </svg>
            </div>

            <!-- Error (draw animation) - keep rounded bg style -->
            <div
                class="result-icon cross absolute w-16 h-16 bg-red-50 rounded-full flex items-center justify-center"
                class:show={status === "error"}
                aria-hidden="true"
            >
                <svg viewBox="0 0 52 52" width="36" height="36">
                    <path
                        class="cross-path"
                        fill="none"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17 17 L35 35 M35 17 L17 35"
                    />
                </svg>
            </div>
        </div>
        {#if status === "success" && success}
            {@render success?.()}
        {/if}

        {#if status === "error"}
            <Button
                label="Close"
                class="w-full"
                variant="danger"
                onclick={handleCancel}
            />
        {/if}
    </div>
</GenericDialog>

<style>
    .animate-spin {
        animation: spin 800ms linear infinite;
    }
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Spinner wrap (layout handled by Tailwind) */
    .spinner-wrap {
        transition:
            opacity 300ms ease,
            transform 300ms ease;
    }
    .spinner-wrap.hidden {
        opacity: 0;
        transform: scale(0.85);
        pointer-events: none;
    }
    .spinner-ring::after {
        content: "";
        width: 100%;
        height: 100%;
        border-radius: 9999px;
        border: 4px solid transparent;
        border-top-color: #3b82f6; /* blue-500 */
        border-left-color: #3b82f6;
        animation: spin 800ms linear infinite;
        box-sizing: border-box;
        display: block;
    }

    /* Result icons */
    .result-icon {
        /* position/size handled by Tailwind utilities */
        transform: scale(0.7);
        opacity: 0;
        transition:
            opacity 240ms ease,
            transform 240ms ease;
    }
    .result-icon.show {
        opacity: 1;
        transform: scale(1);
    }

    /* draw animation */
    .check-path,
    .cross-path {
        stroke: currentColor;
        stroke-dasharray: 100;
        stroke-dashoffset: 100;
        transition:
            stroke-dashoffset 360ms ease 120ms,
            opacity 200ms ease;
        opacity: 0.001;
    }
    .result-icon.show .check-path,
    .result-icon.show .cross-path {
        stroke-dashoffset: 0;
        opacity: 1;
    }

    .result-icon.check {
        color: #059669;
    } /* emerald-600 */
    .result-icon.cross {
        color: #dc2626;
    } /* red-600 */
</style>
