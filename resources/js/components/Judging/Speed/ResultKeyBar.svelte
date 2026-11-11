<script lang="ts">
    /**
     * A bar of result shortcuts (DNF / DNS / OOT) that sits just above the
     * on-screen keyboard while one of the lane inputs inside `scope` is
     * focused. The inputs use `inputmode="numeric"`, so the phone keyboard
     * has no letters; this is how a judge enters a non-time result.
     *
     * Tapping an option writes it into the focused input (through an `input`
     * event, so the mask, binding and validation all run) and moves on to the
     * next lane input, or closes the keyboard after the last one.
     */
    import { fly } from "svelte/transition";

    let {
        scope,
        options = ["DNF", "DNS", "OOT"],
    }: {
        /** The element whose text inputs the bar serves */
        scope: HTMLElement | undefined;
        options?: string[];
    } = $props();

    let active = $state<HTMLInputElement | null>(null);
    let barHeight = $state(0);
    // Top edge of the bar in layout-viewport coordinates. On iOS the keyboard
    // doesn't resize the layout viewport, so `bottom: 0` would sit behind it;
    // the visual viewport tells us where the visible bottom edge really is.
    let top = $state<number | null>(null);

    const selector = "input[type='text']";

    function laneInputs(): HTMLInputElement[] {
        return scope
            ? Array.from(scope.querySelectorAll<HTMLInputElement>(selector))
            : [];
    }

    function isLaneInput(el: EventTarget | null): el is HTMLInputElement {
        return (
            el instanceof HTMLInputElement &&
            el.matches(selector) &&
            !!scope?.contains(el)
        );
    }

    function onFocusIn(e: FocusEvent) {
        if (isLaneInput(e.target)) active = e.target;
    }

    function onFocusOut(e: FocusEvent) {
        if (e.target === active) active = null;
    }

    function pick(value: string) {
        const el = active;
        if (!el) return;

        el.value = value;
        el.dispatchEvent(new Event("input", { bubbles: true }));

        const inputs = laneInputs();
        const next = inputs[inputs.indexOf(el) + 1];
        if (next) {
            next.focus();
            next.select();
        } else {
            el.blur();
        }
    }

    function place() {
        const vv = window.visualViewport;
        top = vv
            ? vv.offsetTop + vv.height - barHeight
            : window.innerHeight - barHeight;
    }

    // Keep the focused input clear of the bar once the keyboard has settled
    function reveal() {
        if (!active || top === null) return;
        const bottom = active.getBoundingClientRect().bottom + 8;
        if (bottom > top) {
            window.scrollBy({ top: bottom - top, behavior: "smooth" });
        }
    }

    $effect(() => {
        if (!scope) return;
        const el = scope;
        el.addEventListener("focusin", onFocusIn);
        el.addEventListener("focusout", onFocusOut);
        return () => {
            el.removeEventListener("focusin", onFocusIn);
            el.removeEventListener("focusout", onFocusOut);
        };
    });

    $effect(() => {
        if (!active) {
            top = null;
            return;
        }

        const vv = window.visualViewport;
        const onResize = () => {
            place();
            reveal();
        };

        place();
        // The keyboard animates in; check again once it is likely up
        const timer = setTimeout(onResize, 350);

        vv?.addEventListener("resize", onResize);
        vv?.addEventListener("scroll", place);
        window.addEventListener("resize", onResize);

        return () => {
            clearTimeout(timer);
            vv?.removeEventListener("resize", onResize);
            vv?.removeEventListener("scroll", place);
            window.removeEventListener("resize", onResize);
        };
    });

    $effect(() => {
        barHeight;
        if (active) place();
    });
</script>

{#if active && top !== null}
    <div
        bind:clientHeight={barHeight}
        class="fixed inset-x-0 z-50 border-t bg-gray-50 p-2 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]"
        style="top: {top}px"
        role="toolbar"
        aria-label="Result shortcuts"
        transition:fly={{ y: 16, duration: 150 }}
    >
        <div class="mx-auto grid max-w-sm grid-cols-3 gap-2">
            {#each options as option (option)}
                <button
                    type="button"
                    tabindex="-1"
                    class="cursor-pointer rounded-lg border bg-white py-2 font-archivo text-sm font-semibold text-gray-900 shadow-xs transition-colors hover:border-gray-400 active:bg-se/10"
                    onpointerdown={(e) => e.preventDefault()}
                    onmousedown={(e) => e.preventDefault()}
                    onclick={() => pick(option)}
                >
                    {option}
                </button>
            {/each}
        </div>
    </div>
{/if}
