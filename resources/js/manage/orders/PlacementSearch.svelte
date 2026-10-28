<!--
    A search over the Heats and/or the Draw a page shows (Heats & Draws, the heat and draw editors): under
    it, the first three Entities it finds, each with its heat and lane and its draw place, or Unassigned.
    The page lights up what matches with `matcher` and `matchClass`, dimming the rest with `dimClass`, and
    marks those in its heats table data-match: given that table's `scroll` box, the first one is scrolled
    into view sideways.

    Each place in the box is a button that jumps to it: `onpick` lets the page get it ready (open its Tank,
    select it), then the element marked with that place's `data-place` (`placeKey`) is scrolled to the
    middle of the screen, ringed for a moment and focused when it can be.
-->
<script module lang="ts">
    /** Whether a name matches the search; nothing does while it's empty */
    export function matcher(query: string) {
        const needle = query.trim().toLowerCase();
        return (name: string | null | undefined) => needle !== "" && !!name?.toLowerCase().includes(needle);
    }

    export const matchClass = "bg-v2-teal-soft text-v2-teal-text";
    export const dimClass = "opacity-35";

    /** Where a search found an Entity */
    export type Place =
        | { kind: "heat"; entity: number; heat: number; lane: number }
        | { kind: "draw"; entity: number; tank: number; id: number }
        | { kind: "unassigned"; entity: number };

    /** What to put in data-place on the element a place jumps to */
    export function placeKey(place: Place) {
        return place.kind === "heat" ? `heat:${place.heat}:${place.lane}` : place.kind === "draw" ? `draw:${place.id}` : `entity:${place.entity}`;
    }
</script>

<script lang="ts">
    import type { DrawTank } from "@/manage/types";
    import { tick } from "svelte";

    type Named = { entity: number; name: string };

    let {
        query = $bindable(""),
        heats,
        draw,
        unassigned = [],
        scroll,
        onpick,
    }: {
        query?: string;
        /** The Heats shown, and what to call them ("Heats (Main Pool)") */
        heats?: { list: { number: number; lanes: (Named | null)[] }[]; label: string };
        draw?: { tanks: DrawTank[]; useTanks: boolean };
        /** Entities waiting to be placed in whichever of the two the page edits */
        unassigned?: { id: number; name: string }[];
        /** The heats table's scrolling box */
        scroll?: HTMLElement;
        /** Gets the page ready before jumping to a place */
        onpick?: (place: Place) => void;
    } = $props();

    const matches = $derived(matcher(query));

    type Spot = { label: string; place: Place };
    type Found = { key: string; name: string; heats: Spot[]; draws: Spot[]; waiting: Place | null };

    /** Each Entity matched, in the heats' order, then the draw's, then Unassigned, with where it is */
    const found = $derived.by(() => {
        const byEntity = new Map<string, Found>();
        const of = (entity: number, name: string) => {
            const key = `${entity}:${name}`;
            if (!byEntity.has(key)) byEntity.set(key, { key, name, heats: [], draws: [], waiting: null });
            return byEntity.get(key)!;
        };
        for (const heat of heats?.list ?? []) {
            heat.lanes.forEach((cell, index) => {
                if (!cell || !matches(cell.name)) return;
                const place: Place = { kind: "heat", entity: cell.entity, heat: heat.number, lane: index + 1 };
                of(cell.entity, cell.name).heats.push({ label: `Heat ${heat.number}, Lane ${index + 1}`, place });
            });
        }
        for (const tank of draw?.tanks ?? []) {
            for (const entry of tank.draws) {
                if (!matches(entry.name)) continue;
                const place: Place = { kind: "draw", entity: entry.entity, tank: tank.number, id: entry.id };
                of(entry.entity, entry.name).draws.push({ label: draw!.useTanks ? `Tank ${tank.number}, Draw ${entry.draw}` : `Draw ${entry.draw}`, place });
            }
        }
        for (const entity of unassigned) {
            if (matches(entity.name)) of(entity.id, entity.name).waiting = { kind: "unassigned", entity: entity.id };
        }
        return [...byEntity.values()];
    });

    // Scroll the heats sideways to the first match, should it be out of view
    $effect(() => {
        if (found.length === 0 || !scroll) return;
        const cell = scroll.querySelector<HTMLElement>("[data-match]");
        if (!cell) return;
        const at = cell.getBoundingClientRect();
        const within = scroll.getBoundingClientRect();
        // past the sticky Lane column
        if (at.left >= within.left + 64 && at.right <= within.right) return;
        scroll.scrollTo({ left: scroll.scrollLeft + at.left - within.left - (scroll.clientWidth - at.width) / 2, behavior: reduceMotion() ? "auto" : "smooth" });
    });

    function reduceMotion() {
        return window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    }

    async function jump(place: Place) {
        onpick?.(place);
        await tick();
        const target = document.querySelector<HTMLElement>(`[data-place="${placeKey(place)}"]`);
        if (!target) return;
        target.scrollIntoView({ block: "center", inline: "center", behavior: reduceMotion() ? "auto" : "smooth" });
        if (target.matches("button, a, input, [tabindex]")) target.focus({ preventScroll: true });
        if (reduceMotion()) return;
        // Ring it again on a second jump: the animation restarts only once the class has been off
        target.classList.remove("animate-located");
        void target.offsetWidth;
        target.classList.add("animate-located");
        target.addEventListener("animationend", () => target.classList.remove("animate-located"), { once: true });
    }

    const placeholder = $derived(heats && draw ? "Search heats and draw" : heats ? "Search heats" : "Search the draw");
    const placeButton = "cursor-pointer rounded-sm text-left text-v2-teal-text underline underline-offset-2 hover:text-v2-ink";
</script>

{#snippet place(label: string, spots: Spot[], waiting: Place | null, missing: string)}
    <span class={[spots.length === 0 && !waiting && "text-v2-muted"]}>
        <span class="text-v2-muted">{label}:</span>
        {#each spots as spot, index (index)}
            {index > 0 ? "; " : ""}<button type="button" class={placeButton} onclick={() => jump(spot.place)}>{spot.label}</button>
        {:else}
            {#if waiting}
                <button type="button" class={placeButton} onclick={() => jump(waiting)}>Unassigned</button>
            {:else}
                {missing}
            {/if}
        {/each}
    </span>
{/snippet}

<div>
    <input type="search" class="v2-input w-full" {placeholder} aria-label={placeholder} bind:value={query} />
    <div aria-live="polite">
        {#if query.trim() !== "" && found.length === 0}
            <p class="mt-2 text-sm text-v2-muted">Nothing matches.</p>
        {:else if found.length > 0}
            <ul class="mt-2 divide-y divide-v2-border rounded-md border border-v2-border bg-v2-card text-sm">
                {#each found.slice(0, 3) as match (match.key)}
                    <li class="flex flex-col gap-x-6 gap-y-1 px-3 py-2 sm:flex-row sm:items-baseline">
                        <span class="font-semibold wrap-anywhere sm:mr-auto">{match.name}</span>
                        {#if heats && (heats.list.length > 0 || match.waiting)}{@render place(heats.label, match.heats, match.waiting, "not in these heats")}{/if}
                        {#if draw}{@render place("Draw", match.draws, heats ? null : match.waiting, "not in the draw")}{/if}
                    </li>
                {/each}
                {#if found.length > 3}
                    <li class="px-3 py-2 text-v2-muted">and {found.length - 3} more, lit up below</li>
                {/if}
            </ul>
        {/if}
    </div>
</div>
