<!--
    A SERC's League Objectives (manage.competitions.events.sercs.league-objectives), in CompetitionLayout:
    the Objective each Entity is marked against by its Leagues, flagging those with none or several (judges
    can't mark them until it's fixed), then the Leagues on each Objective.
-->
<script lang="ts">
    import { setup, show } from "@/actions/App/Http/Controllers/Manage/SercEventController";
    import Alert from "@/manage/components/Alert.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import type { CompetitionHeader, SercEventHeader } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

    let {
        competition,
        event,
        entities,
        objectives,
    }: {
        competition: CompetitionHeader;
        event: SercEventHeader;
        entities: {
            key: string;
            name: string;
            leagues: string[];
            /** Null when it has none or several */
            objective: string | null;
            problem: "no_objective" | "several_objectives" | null;
        }[];
        objectives: { id: number; name: string; leagues: string[] }[];
    } = $props();

    const comp = $derived(competition.id);
    const problems = $derived(entities.filter((entity) => entity.problem !== null).length);

    const heading = "text-lg font-extrabold uppercase";
    const tableHead = "text-sm text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold";
    const row = "*:border-b *:border-v2-border *:px-2 *:py-2.5 *:align-top";
</script>

<svelte:head>
    <title>League Objectives - {event.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

<PageHeading
    back={{ href: show({ comp, serc: event.id }).url, label: event.name }}
    title="League Objectives"
    lead="{entities.length - problems} of {entities.length} entries have one Objective to be marked against."
/>

{#if !event.leagueObjectives}
    <div class="mb-6">
        <Alert tone="info" title="League Objectives are off">
            <p class="mt-1">Judges pick the Objective they mark. Turn them on in its <Link href={setup({ comp, serc: event.id }).url} class="v2-link-inline">setup</Link>.</p>
        </Alert>
    </div>
{:else if problems > 0}
    <div class="mb-6">
        <Alert tone="danger" title="{problems} {problems === 1 ? 'entry has' : 'entries have'} no single Objective">
            <p class="mt-1">
                Judges can't mark {problems === 1 ? "it" : "them"} until each League is on one Objective. Change that in its
                <Link href={setup({ comp, serc: event.id }).url} class="v2-link-inline">setup</Link>, or the entry's Leagues on Entries & Leagues.
            </p>
        </Alert>
    </div>
{/if}

<div class="overflow-x-auto">
    <table class="w-full border-t border-v2-border text-left">
        <thead>
            <tr class={tableHead}>
                <th scope="col">Entry</th>
                <th scope="col">Leagues</th>
                <th scope="col">Objective</th>
            </tr>
        </thead>
        <tbody>
            {#each entities as entity (entity.key)}
                <tr class={row}>
                    <th scope="row" class="font-bold wrap-anywhere">{entity.name}</th>
                    <td class={["text-sm", entity.leagues.length === 0 && "text-v2-muted"]}>{entity.leagues.join(", ") || "No League"}</td>
                    <td>
                        {#if entity.problem === "no_objective"}
                            <span class="v2-badge is-danger">No Objective for its Leagues</span>
                        {:else if entity.problem === "several_objectives"}
                            <span class="v2-badge is-danger">Leagues on more than one Objective</span>
                        {:else}
                            <span class="font-semibold">{entity.objective}</span>
                        {/if}
                    </td>
                </tr>
            {/each}
        </tbody>
    </table>
    {#if entities.length === 0}
        <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries yet.</p>
    {/if}
</div>

<section aria-labelledby="lo-objectives" class="mt-10">
    <h3 id="lo-objectives" class={heading}>Objectives</h3>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full border-t border-v2-border text-left">
            <thead>
                <tr class={tableHead}>
                    <th scope="col">Objective</th>
                    <th scope="col">Leagues</th>
                </tr>
            </thead>
            <tbody>
                {#each objectives as objective (objective.id)}
                    <tr class={row}>
                        <th scope="row" class="font-bold wrap-anywhere">{objective.name}</th>
                        <td class={["text-sm", objective.leagues.length === 0 && "text-v2-muted"]}>
                            {objective.leagues.join(", ") || "No Leagues, so no entries are marked against it"}
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
        {#if objectives.length === 0}
            <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No Objectives yet.</p>
        {/if}
    </div>
</section>
