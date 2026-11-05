<!--
    An event's DQs and penalties (Violations): a row each with the Entity and its code, opening to where it
    happened, who called it and the judge's details. An appealed one was overturned, so its code is struck
    through. Inside LiveLayout.
-->
<script lang="ts">
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import SmoothHeight from "@/manage/components/SmoothHeight.svelte";
    import type { LiveHeader } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";
    import { ChevronDown } from "@lucide/svelte";

    type Dq = {
        id: number;
        entity: string | null;
        code: string | null;
        description: string;
        appealed: boolean;
        /** Pool/Heat/Lane for a speed event, Tank/Draw for a SERC */
        placement: Record<string, string | number | null> | [];
        turn: number | null;
        length: number | null;
        /** [name, position] */
        reporter: [string | null, string | null];
        seconder: [string | null, string | null];
        details: string | null;
    };

    let { competition, event, dqs, back }: { competition: LiveHeader; event: { name: string }; dqs: Dq[]; back: string } = $props();

    let open = $state<Record<number, boolean>>({});

    function person([name, position]: [string | null, string | null]): string {
        return `${name ?? "–"} (${position ?? "–"})`;
    }
</script>

<svelte:head><title>{event.name} DQs & Penalties - {competition.name} - Scoring.Events</title></svelte:head>

<div class="mb-6">
    <Link href={back} class="v2-link-muted text-sm">← All events</Link>
    <h2 class="mt-1 text-2xl font-extrabold wrap-anywhere uppercase">{event.name} · DQs & Penalties</h2>
</div>

{#if dqs.length == 0}
    <EmptyState>No DQs or penalties for this event.</EmptyState>
{:else}
    <ul class="border-b border-v2-border">
        {#each dqs as dq (dq.id)}
            <li class="border-t border-v2-border">
                <button
                    type="button"
                    class="flex w-full cursor-pointer items-center justify-between gap-3 px-3 py-3.5 text-left hover:bg-v2-row-hover focus-visible:bg-v2-row-hover"
                    aria-expanded={!!open[dq.id]}
                    aria-controls="dq-{dq.id}"
                    onclick={() => (open[dq.id] = !open[dq.id])}
                >
                    <span class="flex flex-wrap items-center gap-3">
                        <span class="font-bold">{dq.entity ?? "–"}</span>
                        <span class={["v2-badge is-danger", dq.appealed && "line-through"]}>{dq.code ?? "–"}</span>
                        {#if dq.appealed}<span class="v2-badge is-success">Appealed</span>{/if}
                    </span>
                    <ChevronDown size={20} aria-hidden="true" class={["shrink-0 transition-transform motion-reduce:transition-none", open[dq.id] && "rotate-180"]} />
                </button>

                <SmoothHeight>
                    {#if open[dq.id]}
                        <div id="dq-{dq.id}" class="flex flex-col gap-4 px-3 pb-4 text-[0.9375rem]/[1.6]">
                            <p>
                                <strong class="block">{dq.code ?? "–"}</strong>
                                {dq.description}
                            </p>

                            <dl class="flex flex-col gap-x-8 gap-y-1 md:flex-row md:flex-wrap">
                                {#each Object.entries(dq.placement) as [label, value] (label)}
                                    <div><dt class="inline font-semibold">{label}:</dt> <dd class="inline">{value ?? "–"}</dd></div>
                                {/each}
                                <div><dt class="inline font-semibold">Turn:</dt> <dd class="inline">{dq.turn ?? "–"}</dd></div>
                                <div><dt class="inline font-semibold">Length:</dt> <dd class="inline">{dq.length ?? "–"}</dd></div>
                            </dl>

                            <dl class="flex flex-col gap-x-8 gap-y-1 md:flex-row">
                                <div><dt class="inline font-semibold">Reporter:</dt> <dd class="inline">{person(dq.reporter)}</dd></div>
                                <div><dt class="inline font-semibold">Seconder:</dt> <dd class="inline">{person(dq.seconder)}</dd></div>
                            </dl>

                            <div>
                                <p class="font-semibold">Additional judge details</p>
                                <p class="text-v2-muted">{dq.details ?? "–"}</p>
                            </div>
                        </div>
                    {/if}
                </SmoothHeight>
            </li>
        {/each}
    </ul>
{/if}
