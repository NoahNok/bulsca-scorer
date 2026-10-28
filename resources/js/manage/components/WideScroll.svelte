<!--
    A sideways-scrolling box for something wider than the page, like a heats table. Its scrollbar sticks to
    the bottom of the screen while the box is in view, rather than sitting under the last row, and a mouse
    can drag it sideways (a drag doesn't count as a click on what it started on). Touch scrolls as usual.
    An ancestor with overflow: hidden stops the scrollbar sticking, so clip instead (as SmoothHeight does).
-->
<script lang="ts">
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    let {
        children,
        class: className,
        element = $bindable(),
        ...rest
    }: HTMLAttributes<HTMLDivElement> & {
        children: Snippet;
        /** The scrolling box, to scroll it from outside */
        element?: HTMLDivElement;
    } = $props();

    let box = $state<HTMLDivElement>();
    $effect(() => {
        element = box;
    });
    let bar = $state<HTMLDivElement>();
    let width = $state(0);
    let overflowing = $state(false);
    let dragging = $state(false);

    // Follow the content's width, as rows load or another Pool is picked
    $effect(() => {
        if (!box) return;
        const scroller = box;
        const measure = () => {
            width = scroller.scrollWidth;
            overflowing = scroller.scrollWidth > scroller.clientWidth + 1;
        };
        const observer = new ResizeObserver(measure);
        observer.observe(scroller);
        for (const child of scroller.children) observer.observe(child);
        measure();
        return () => observer.disconnect();
    });

    // The box and its bar follow each other. Each remembers where the other last put it, so the scroll event
    // that comes back a frame later is let go: copying it back would stop a smooth scroll (a search jumping
    // to a lane) where it had got to
    const placed = new WeakMap<HTMLDivElement, number>();

    function follow(from: HTMLDivElement | undefined, to: HTMLDivElement | undefined) {
        if (!from || !to) return;
        const echo = placed.get(from);
        placed.delete(from);
        if (echo !== undefined && Math.abs(from.scrollLeft - echo) < 1) return;
        if (Math.abs(to.scrollLeft - from.scrollLeft) < 1) return;
        to.scrollLeft = from.scrollLeft;
        placed.set(to, to.scrollLeft);
    }

    // A mouse drag: past a few pixels it scrolls, and the click it ends with is swallowed
    let start: { x: number; left: number; id: number } | null = null;
    let moved = false;

    function onpointerdown(event: PointerEvent) {
        if (event.pointerType !== "mouse" || event.button !== 0 || !overflowing || !box) return;
        start = { x: event.clientX, left: box.scrollLeft, id: event.pointerId };
        moved = false;
    }

    function onpointermove(event: PointerEvent) {
        if (!start || !box || event.pointerId !== start.id) return;
        const distance = event.clientX - start.x;
        if (!moved && Math.abs(distance) < 5) return;
        if (!moved) {
            moved = true;
            dragging = true;
            box.setPointerCapture(start.id);
            window.getSelection()?.removeAllRanges();
        }
        box.scrollLeft = start.left - distance;
    }

    function onpointerup() {
        start = null;
        dragging = false;
    }

    function onclickcapture(event: MouseEvent) {
        if (!moved) return;
        moved = false;
        event.preventDefault();
        event.stopPropagation();
    }
</script>

<div
    {...rest}
    bind:this={box}
    class={[
        "overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden",
        overflowing && "cursor-grab",
        dragging && "cursor-grabbing select-none [&_*]:cursor-grabbing",
        className,
    ]}
    onscroll={() => follow(box, bar)}
    {onpointerdown}
    {onpointermove}
    {onpointerup}
    onpointercancel={onpointerup}
    {onclickcapture}
>
    {@render children()}
</div>
{#if overflowing}
    <div bind:this={bar} class="sticky bottom-0 z-20 overflow-x-auto bg-v2-bg" aria-hidden="true" onscroll={() => follow(bar, box)}>
        <div class="h-px" style:width="{width}px"></div>
    </div>
{/if}
