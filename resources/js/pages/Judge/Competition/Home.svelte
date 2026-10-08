<script module lang="ts">
    export const layout = {
        title: "Competition",
    };
</script>

<script lang="ts">
    import {
        selectOOFHeat,
        selectTimeHeat,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Event/EventJudgeController";

    import { confirmJudge } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";
    import {
        event as confirmEventResults,
        serc as confirmSercResults,
    } from "@/actions/App/Http/Controllers/DigitalJudge/ConfirmResultsController";

    import AppHead from "@/components/AppHead.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";

    import { appState } from "@/lib/stores/appState";
    import { index } from "@/routes/judge";
    import { submissions } from "@/routes/judge/competition/violation";

    import { EventType, type Competition, type Event, type SERC } from "@/types/base";
    import { page, Link, router, setLayoutProps } from "@inertiajs/svelte";
    import {
        CalendarX,
        Check,
        ChevronRight,
        CircleCheck,
        CircleX,
        ClipboardList,
        Flag,
        ListOrdered,
        Shield,
        ShieldCheck,
        Timer,
    } from "@lucide/svelte";

    let {
        competition,
        sercs,
        speeds,
    }: {
        competition: Competition;
        sercs: SERC[];
        speeds: Event[];
    } = $props();

    $effect(() => {
        $appState.activeCompetition = competition;
    });

    $effect(() => {
        setLayoutProps({ nav: nav });
    });

    const isHead = $derived(page.props.judge.isHeadRef);

    type EventLink = { href: string; icon: any; label: string };

    function sercLinks(serc: SERC): EventLink[] {
        return [
            ...serc.judges.map((judge) => ({
                href: confirmJudge({ competition, serc, judge }).url,
                icon: ClipboardList,
                label: judge.name,
            })),
            {
                href: submissions(competition, {
                    query: { event: `se-${serc.id}` },
                }).url,
                icon: Flag,
                label: "Issue DQ/Penalty",
            },
        ];
    }

    function speedLinks(speed: Event): EventLink[] {
        return [
            {
                href: selectTimeHeat({ competition, event: speed }).url,
                icon: Timer,
                label: "Times",
            },
            {
                href: selectOOFHeat({ competition, event: speed }).url,
                icon: ListOrdered,
                label: "Order of Finish",
            },
            {
                href: submissions(competition, {
                    query: { event: `sp-${speed.id}` },
                }).url,
                icon: Flag,
                label: "Issue DQ/Penalty",
            },
        ];
    }
</script>

<AppHead title={competition.name} />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">Welcome to,</p>
    <h2>{competition.name}</h2>

    {#if sercs.length === 0 && speeds.length === 0}
        <EmptyState
            icon={CalendarX}
            title="No events yet"
            description="Events will show up here once they've been set up."
            class="mt-4"
        />
    {/if}

    {#if sercs.length > 0}
        <SectionLabel class="mt-4 mb-2">SERCs</SectionLabel>
        <div class="flex flex-col gap-5">
            {#each sercs as serc (serc.id)}
                {@render eventCard(serc, sercLinks(serc))}
            {/each}
        </div>
    {/if}

    {#if speeds.length > 0}
        <SectionLabel class="mt-6 mb-2">Speed events</SectionLabel>
        <div class="flex flex-col gap-5">
            {#each speeds as speed (speed.id)}
                {@render eventCard(speed, speedLinks(speed))}
            {/each}
        </div>
    {/if}
</section>

{#snippet eventCard(event: Event, links: EventLink[])}
    <div>
        <div class="mb-2 flex items-center justify-between gap-3">
            <h3 class="min-w-0 truncate text-base!">{event.name}</h3>
            {#if isHead}
                {#if event.completed}
                    <span title="Event complete">
                        <CircleCheck size={20} class="shrink-0 text-green-500" />
                    </span>
                {:else}
                    <span title="Event incomplete">
                        <CircleX size={20} class="shrink-0 text-red-500" />
                    </span>
                {/if}
            {/if}
        </div>

        <div class="divide-y overflow-hidden rounded-xl border bg-white shadow-sm">
            {#each links as link (link.href)}
                {@render row(link)}
            {/each}

            {#if isHead}
                {#if event.confirmed}
                    {@render confirmedRow()}
                {:else}
                    {@render confirmRow(event)}
                {/if}
            {/if}
        </div>
    </div>
{/snippet}

{#snippet row({ href, icon: Icon, label }: EventLink)}
    <Link
        {href}
        class="group flex w-full items-center gap-3 px-3 py-2 text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset"
    >
        <span
            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-600 transition-colors group-hover:bg-se/20 group-hover:text-teal-700"
        >
            <Icon size={16} />
        </span>
        <span class="font-archivo min-w-0 flex-1 truncate text-sm">{label}</span>
        <ChevronRight
            size={18}
            class="shrink-0 text-gray-300 transition-all group-hover:translate-x-0.5 group-hover:text-se"
        />
    </Link>
{/snippet}

{#snippet confirmRow(event: Event)}
    <Link
        href={event.type === EventType.SERC
            ? confirmSercResults({ competition, serc: event })
            : confirmEventResults({ competition, event })}
        class="group flex w-full items-center gap-3 bg-se/5 px-3 py-2 text-left transition-colors hover:bg-se/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset"
    >
        <span
            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-se/20 text-teal-700"
        >
            <Shield size={16} />
        </span>
        <span class="font-archivo min-w-0 flex-1 truncate text-sm">Confirm Results</span>
        <ChevronRight
            size={18}
            class="shrink-0 text-gray-300 transition-all group-hover:translate-x-0.5 group-hover:text-se"
        />
    </Link>
{/snippet}

{#snippet confirmedRow()}
    <div class="flex items-center gap-3 bg-green-50 px-3 py-2">
        <span
            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-green-100 text-green-700"
        >
            <ShieldCheck size={16} />
        </span>
        <span class="font-archivo min-w-0 flex-1 truncate text-sm text-green-800">
            Results Confirmed
        </span>
        <Check size={18} class="shrink-0 text-green-600" />
    </div>
{/snippet}

{#snippet nav()}
    <div class="mr-auto">
        <ConfirmDialog
            triggerLabel="Exit"
            triggerClass=" py-1! border! border-black/10!"
            triggerVariant="white"
            title="Exit Competition"
            description="Exit this competition? You can always come back later."
            onConfirm={() => {
                router.visit(index());
            }}
        />
    </div>
{/snippet}
