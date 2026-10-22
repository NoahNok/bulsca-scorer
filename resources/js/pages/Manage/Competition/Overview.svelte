<!--
    A competition's Overview (comps.view), in CompetitionLayout: its events and leagues, each by the access
    you have, and for its Organisers the Digital Judge panel (PIN, QR code, releasing the SERCs) and links
    to Settings and the activity log.
-->
<script lang="ts">
    import { confirm } from "@/lib/confirm";
    import type { CompetitionEvent, CompetitionHeader } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        events,
        addEventUrl,
        leagues,
        addLeagueUrl,
        digitalJudge,
        links,
    }: {
        competition: CompetitionHeader;
        /** Null without access to any events */
        events: (CompetitionEvent & { url: string; digitalJudge: boolean })[] | null;
        addEventUrl: string;
        /** Null without access to entries */
        leagues: { id: number; name: string; url: string }[] | null;
        addLeagueUrl: string;
        /** Organisers only */
        digitalJudge: {
            enabled: boolean;
            pin: string | null;
            judgeUrl: string;
            officialsUrl: string;
            qrUrl: string;
            toggleUrl: string;
            releaseUrl: string;
            /** ISO, null while they're locked */
            sercsReleasedAt: string | null;
        } | null;
        /** Organisers only */
        links: { settingsUrl: string; activityLogUrl: string } | null;
    } = $props();

    const MANUAL_URL = "https://docs.google.com/document/d/1HKTR9HUzgTKadyE7vyVqDWXeaheK4XFmzlw9Hrn1Q1s/edit?usp=sharing";

    let busy = $state(false);

    const judgeHost = $derived(digitalJudge ? new URL(digitalJudge.judgeUrl).host : "");
    const releasedAt = $derived(
        digitalJudge?.sercsReleasedAt
            ? new Date(digitalJudge.sercsReleasedAt).toLocaleString("en-GB", {
                  hour: "2-digit",
                  minute: "2-digit",
                  day: "numeric",
                  month: "short",
              })
            : null,
    );

    function post(url: string, data: Record<string, string | number> = {}) {
        router.post(url, data, {
            preserveScroll: true,
            onStart: () => (busy = true),
            onFinish: () => (busy = false),
        });
    }

    // A list row that leads somewhere: the whole row is the link
    const row =
        "flex items-center gap-3 px-2 py-[0.8rem] transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover motion-reduce:transition-none";
    // Links in the Digital Judge panel: plain ink, underlined
    const judgeLink = "text-v2-ink underline underline-offset-2";

    async function lockSercs() {
        const ok = await confirm({
            title: "Lock the SERCs?",
            description: "Officials won't be able to see or judge them until you release them again.",
            confirmLabel: "Lock SERCs",
            confirmVariant: "danger",
        });
        if (ok && digitalJudge) post(digitalJudge.releaseUrl, { released: 0 });
    }
</script>

<svelte:head>
    <title>{competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet chevron()}
    <svg class="ml-auto size-4 shrink-0 text-v2-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="m9 6 6 6-6 6" />
    </svg>
{/snippet}

{#snippet heading(id: string, text: string)}
    <h2 {id} class="text-lg font-extrabold uppercase">{text}</h2>
{/snippet}

<div class="grid gap-10 min-[56rem]:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] min-[56rem]:items-start">
    <div class="flex min-w-0 flex-col gap-10">
        {#if events}
            <section aria-labelledby="o-events">
                <div class="mb-2 flex items-baseline justify-between gap-4">
                    {@render heading("o-events", "Events")}
                    <a href={addEventUrl} class="v2-link">Add event</a>
                </div>
                {#if events.length > 0}
                    <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                        {#each events as event (event.key)}
                            <li>
                                <a href={event.url} class={row}>
                                    <span class="font-bold wrap-anywhere">{event.name}</span>
                                    {#if event.serc}<span class="v2-badge">SERC</span>{/if}
                                    {#if event.digitalJudge}
                                        <span
                                            class="inline-flex items-center gap-[0.4rem] text-[0.8125rem] font-semibold whitespace-nowrap text-v2-teal-text"
                                            ><span class="v2-dot" aria-hidden="true"></span>Digital Judge</span
                                        >
                                    {/if}
                                    {@render chevron()}
                                </a>
                            </li>
                        {/each}
                    </ul>
                {:else}
                    <p class="pt-1 text-[0.9375rem]/[1.6] text-v2-muted">
                        No events yet. Add speed events and SERCs from <a href={addEventUrl} class="v2-link-inline">Events</a>.
                    </p>
                {/if}
            </section>
        {/if}

        {#if leagues}
            <section aria-labelledby="o-leagues">
                <div class="mb-2 flex items-baseline justify-between gap-4">
                    {@render heading("o-leagues", "Leagues")}
                    <a href={addLeagueUrl} class="v2-link">Add league</a>
                </div>
                {#if leagues.length > 0}
                    <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                        {#each leagues as league (league.id)}
                            <li>
                                <a href={league.url} class={row}>
                                    <span class="font-bold wrap-anywhere">{league.name}</span>
                                    {@render chevron()}
                                </a>
                            </li>
                        {/each}
                    </ul>
                {:else}
                    <p class="pt-1 text-[0.9375rem]/[1.6] text-v2-muted">No leagues yet.</p>
                {/if}
            </section>
        {/if}
    </div>

    {#if digitalJudge || links}
        <div class="flex min-w-0 flex-col gap-10">
            {#if digitalJudge}
                <!-- The page's one bold panel: teal-tinted in light, the dark --v2-tile band in dark -->
                <section
                    class="rounded-2xl border border-v2-teal/35 bg-v2-teal-soft p-6 dark:border-transparent dark:bg-v2-tile"
                    aria-labelledby="o-judge"
                >
                    {@render heading("o-judge", "Digital Judge")}

                    {#if digitalJudge.enabled}
                        <p class="mt-5 text-[0.8125rem] font-semibold text-v2-muted">PIN</p>
                        <p
                            class="font-anton text-[clamp(2.75rem,7vw,3.5rem)] leading-none tracking-[0.08em] text-v2-teal-text tabular-nums select-all dark:text-v2-teal"
                        >{digitalJudge.pin}</p>
                        <p class="mt-4 text-sm/[1.6] text-v2-muted dark:text-[#d1d5db]">
                            Officials sign in at <a href={digitalJudge.judgeUrl} target="_blank" rel="noopener" class={judgeLink}
                                >{judgeHost}</a
                            >
                            and enter it, or scan the
                            <a href={digitalJudge.qrUrl} target="_blank" rel="noopener" class={judgeLink}>printable QR code</a>.
                            Referees don't need it: give them the Referee role on
                            <a href={digitalJudge.officialsUrl} class={judgeLink}>Officials</a>.
                        </p>

                        <div
                            class="mt-5 border-t border-v2-teal/25 pt-5 text-sm/normal text-v2-muted dark:border-[#374151] dark:text-[#d1d5db]"
                        >
                            <p>
                                <strong class="block text-v2-ink">SERCs {releasedAt ? "released" : "locked"}</strong>
                                {releasedAt ? `Officials can judge them, since ${releasedAt}.` : "Only referees can see them until you release them."}
                            </p>
                            {#if releasedAt}
                                <!-- On the panel, not a card -->
                                <button
                                    type="button"
                                    class="v2-btn mt-3.5 w-full justify-center border-v2-border bg-transparent text-v2-ink hover:border-v2-muted dark:border-[#4b5563] dark:hover:border-v2-muted"
                                    disabled={busy}
                                    onclick={lockSercs}>Lock SERCs</button
                                >
                            {:else}
                                <button
                                    type="button"
                                    class="v2-btn v2-btn-accent mt-3.5 w-full justify-center"
                                    disabled={busy}
                                    onclick={() => post(digitalJudge.releaseUrl, { released: 1 })}>Release SERCs</button
                                >
                            {/if}
                        </div>

                        <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold">
                            {#if links}
                                <Link href="{links.settingsUrl}#digital-judge" class={judgeLink}>Choose events</Link>
                            {/if}
                            <a href={MANUAL_URL} target="_blank" rel="noopener" class={judgeLink}>Manual</a>
                            <button
                                type="button"
                                class="ml-auto cursor-pointer text-v2-danger-text underline underline-offset-2 disabled:opacity-50"
                                disabled={busy}
                                onclick={() => post(digitalJudge.toggleUrl)}>Turn off</button
                            >
                        </div>
                    {:else}
                        <p class="mt-4 text-sm/[1.6] text-v2-muted dark:text-[#d1d5db]">Officials enter SERC marks on their own phones.</p>
                        <button
                            type="button"
                            class="v2-btn v2-btn-accent mt-3.5 w-full justify-center"
                            disabled={busy}
                            onclick={() => post(digitalJudge.toggleUrl)}>Turn on Digital Judge</button
                        >
                    {/if}
                </section>
            {/if}

            {#if links}
                <section aria-labelledby="o-comp">
                    {@render heading("o-comp", "Competition")}
                    <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                        <li>
                            <Link href={links.settingsUrl} class={row}>
                                <span class="font-bold wrap-anywhere">Settings</span>
                                {@render chevron()}
                            </Link>
                        </li>
                        <li>
                            <a href={links.activityLogUrl} class={row}>
                                <span class="font-bold wrap-anywhere">Activity log</span>
                                {@render chevron()}
                            </a>
                        </li>
                    </ul>
                </section>
            {/if}
        </div>
    {/if}
</div>
