<!--
    Printables (comps.printables), in CompetitionLayout: a row per Pack opening its PDF in a new tab, under
    Speed events and SERCs, with each SERC's rough mark sheets after the SERC Packs. A row that needs a
    choice first (the Chief Timekeeper Pack's layout, or which SERC's rough mark sheets) opens a dialog of
    options. One with nothing to print yet is muted and says why, linking to where that's fixed.
-->
<script lang="ts">
    import LinkPicker from "@/manage/components/LinkPicker.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import type { CompetitionHeader } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

    /** Why there's nothing to print yet, and the page that fixes it (null without access to it) */
    type Unavailable = { reason: string; url: string | null };
    type Pack = { url: string | null; unavailable: Unavailable | null };

    let {
        competition,
        packs,
        roughMarkSheets,
    }: {
        competition: CompetitionHeader;
        packs: {
            chiefTimekeeper: Pack & { packedUrl: string | null };
            speedMarshalling: Pack;
            sercMarking: Pack;
            sercMarshalling: Pack;
        };
        roughMarkSheets: { sercs: { id: number; name: string; url: string }[]; unavailable: Unavailable | null };
    } = $props();

    let picker: LinkPicker;

    const sercs = $derived(roughMarkSheets.sercs);

    function pickLayout() {
        const { url, packedUrl } = packs.chiefTimekeeper;
        if (!url || !packedUrl) return;
        picker.open("Chief Timekeeper Pack", "How should the heats be laid out?", [
            { name: "One heat per page", description: "A sheet per heat, to hand out heat by heat", url },
            { name: "Packed", description: "Several heats to a page, to save paper", url: packedUrl },
        ]);
    }

    function pickSerc() {
        picker.open("Rough mark sheets", "Which SERC are they for?", sercs.map((serc) => ({ name: serc.name, url: serc.url })));
    }

    const heading = "text-lg font-extrabold uppercase";
    const list = "border-t border-v2-border *:border-b *:border-v2-border";
    const row =
        "flex w-full cursor-pointer items-center gap-3 px-2 py-[0.8rem] text-left transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover motion-reduce:transition-none";
</script>

<svelte:head>
    <title>Printables - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet label(name: string, description?: string)}
    <span class="min-w-0">
        <span class="block font-bold wrap-anywhere">{name}</span>
        {#if description}<span class="block text-sm text-v2-muted">{description}</span>{/if}
    </span>
{/snippet}

<!-- A row that opens its PDF, or a dialog of options when `onpick` is given -->
{#snippet printable(name: string, description: string, pack: Pack, onpick?: () => void)}
    <li>
        {#if pack.unavailable}
            <div class="flex items-center gap-3 px-2 py-[0.8rem] text-v2-muted">
                {@render label(name, description)}
                <span class="ml-auto shrink-0 text-sm font-semibold">
                    {#if pack.unavailable.url}
                        <Link href={pack.unavailable.url} class="v2-link-inline">{pack.unavailable.reason}</Link>
                    {:else}
                        {pack.unavailable.reason}
                    {/if}
                </span>
            </div>
        {:else if onpick}
            <button type="button" class={row} aria-haspopup="dialog" onclick={onpick}>
                {@render label(name, description)}
                <RowIcon />
            </button>
        {:else}
            <a href={pack.url} target="_blank" rel="noopener" class={row}>
                {@render label(name, description)}
                <RowIcon icon="external" />
            </a>
        {/if}
    </li>
{/snippet}

<h2 class="text-2xl font-extrabold uppercase">Printables</h2>
<p class="mt-[0.35rem] mb-6 text-[0.9375rem]/[1.6] text-v2-muted">
    Packs for the officials on the day, filled in from your heats and draw. Each opens as a PDF in a new tab.
</p>

<div class="grid gap-10 min-[56rem]:grid-cols-2 min-[56rem]:items-start">
    <section aria-labelledby="pr-speeds">
        <h3 id="pr-speeds" class="mb-2 {heading}">Speed events</h3>
        <ul class={list}>
            {@render printable(
                "Chief Timekeeper Pack",
                "Heat sheets for every speed event, filled in with each lane's Entity",
                packs.chiefTimekeeper,
                pickLayout,
            )}
            {@render printable("Marshalling Pack", "Entity names grouped and ordered by heat", packs.speedMarshalling)}
        </ul>
    </section>

    <section aria-labelledby="pr-sercs">
        <h3 id="pr-sercs" class="mb-2 {heading}">SERCs</h3>
        <ul class={list}>
            {@render printable("Marking Pack", "Marking sheets for every Entity in every SERC, in draw order", packs.sercMarking)}
            {@render printable("Marshalling Pack", "Entity names grouped and ordered by draw", packs.sercMarshalling)}
            {@render printable(
                "Rough mark sheets",
                "Blank tables for judges to rough-mark against one SERC's Objectives",
                { url: sercs.length === 1 ? sercs[0].url : null, unavailable: roughMarkSheets.unavailable },
                sercs.length > 1 ? pickSerc : undefined,
            )}
        </ul>
    </section>
</div>

<LinkPicker bind:this={picker} />
