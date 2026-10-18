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

    import {
        confirmJudge,
        confirmJudgePost,
    } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";
    import {
        event as confirmEventResults,
        serc as confirmSercResults,
    } from "@/actions/App/Http/Controllers/DigitalJudge/ConfirmResultsController";

    import { setSercsReleased } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";

    import { confirm } from "@/lib/confirm";
    import { appState } from "@/lib/stores/appState";
    import { index } from "@/routes/judge";
    import { submissions } from "@/routes/judge/competition/violation";

    import { EventType, type Competition, type Event, type SERC } from "@/types/base";
    import { page, Link, router } from "@inertiajs/svelte";
    import {
        CalendarX,
        Check,
        ChevronRight,
        CircleCheck,
        CircleX,
        ClipboardList,
        Flag,
        ListOrdered,
        Lock,
        LockOpen,
        Shield,
        ShieldCheck,
        Timer,
    } from "@lucide/svelte";

    let {
        competition,
        sercsReleased,
        sercs,
        speeds,
    }: {
        competition: Competition;
        sercsReleased: boolean;
        sercs: SERC[];
        speeds: Event[];
    } = $props();

    $effect(() => {
        $appState.activeCompetition = competition;
    });

    const isHead = $derived(page.props.judge.isHeadRef);

    async function leaveCompetition() {
        const ok = await confirm({
            title: "Leave Competition",
            description:
                "Leave this competition? You can always come back later.",
            confirmLabel: "Leave",
        });

        if (ok) router.visit(index());
    }

    let releasing = $state(false);

    async function setReleased(released: boolean) {
        const ok = await confirm(
            released
                ? {
                      title: "Release SERCs",
                      description:
                          "Every official at this competition will be able to see and judge the SERCs. Only do this on the day.",
                      confirmLabel: "Release",
                  }
                : {
                      title: "Lock SERCs",
                      description:
                          "Officials won't be able to see or judge the SERCs until you release them again.",
                      confirmLabel: "Lock",
                      confirmVariant: "danger",
                  },
        );

        if (!ok) return;

        router.post(
            setSercsReleased(competition),
            { released },
            {
                preserveScroll: true,
                onStart: () => (releasing = true),
                onFinish: () => (releasing = false),
            },
        );
    }

    type EventLink = { href: string; icon: any; label: string; method?: "post" };

    function sercLinks(serc: SERC): EventLink[] {
        // with League Objectives the Entity's League picks the Objective, so there's one way in
        const judging: EventLink[] = serc.league_objectives
            ? [
                  {
                      href: confirmJudgePost({ competition, serc }).url,
                      icon: ClipboardList,
                      label: "Start judging",
                      method: "post",
                  },
              ]
            : serc.judges.map((judge) => ({
                  href: confirmJudge({ competition, serc, judge }).url,
                  icon: ClipboardList,
                  label: judge.name,
              }));

        return [
            ...judging,
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
        <div class="mt-4 mb-2 flex items-center justify-between gap-3">
            <SectionLabel>SERCs</SectionLabel>
            {#if isHead && sercsReleased}
                <button
                    type="button"
                    class="inline-flex cursor-pointer items-center gap-1 text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                    disabled={releasing}
                    onclick={() => setReleased(false)}
                >
                    <Lock size={14} />
                    Lock SERCs
                </button>
            {/if}
        </div>

        {#if isHead && !sercsReleased}
            <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <Lock size={18} class="mt-0.5 shrink-0 text-amber-700" />
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-amber-900">
                            SERCs are hidden from officials
                        </p>
                        <p class="text-sm text-amber-800">
                            Only referees can see them. Release them on the day when judging is
                            about to start.
                        </p>
                    </div>
                </div>
                <Button
                    label="Release SERCs"
                    icon={LockOpen}
                    class="mt-3 w-full"
                    loading={releasing}
                    disabled={releasing}
                    onclick={() => setReleased(true)}
                />
            </div>
        {/if}

        <div class="flex flex-col gap-5">
            {#each sercs as serc (serc.id)}
                {#if serc.locked}
                    {@render lockedCard(serc)}
                {:else}
                    {@render eventCard(serc, sercLinks(serc))}
                {/if}
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

    <button
        type="button"
        class="mt-8 cursor-pointer self-center text-sm text-gray-500 underline hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
        onclick={leaveCompetition}
    >
        Leave competition
    </button>
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

{#snippet lockedCard(event: Event)}
    <div>
        <div class="mb-2 flex items-center justify-between gap-3">
            <h3 class="min-w-0 truncate text-base!">{event.name}</h3>
        </div>

        <div class="flex items-center gap-3 rounded-xl border bg-white px-3 py-2 shadow-sm">
            <span
                class="flex size-8 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-400"
            >
                <Lock size={16} />
            </span>
            <span class="min-w-0 flex-1 text-sm text-gray-500">
                Locked until the referee releases SERCs
            </span>
        </div>
    </div>
{/snippet}

{#snippet row({ href, icon: Icon, label, method }: EventLink)}
    <Link
        {href}
        method={method ?? "get"}
        as={method === "post" ? "button" : "a"}
        class="group flex w-full cursor-pointer items-center gap-3 px-3 py-2 text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset"
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
