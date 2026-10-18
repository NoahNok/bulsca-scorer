<script module lang="ts">
    import { home as sercHome } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";
    import { LifeBuoy } from "@lucide/svelte";

    export const layout = (props: Record<string, any>) => ({
        title: "Mark",
        header: {
            icon: LifeBuoy,
            href: sercHome({ competition: props.competition, serc: props.serc }),
        },
    });
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import EmptyState from "@/components/EmptyState.svelte";

    import type { Competition, Entity, SERC } from "@/types/base";
    import { TriangleAlert } from "@lucide/svelte";

    let {
        competition,
        serc,
        entity,
        leagues,
        reason,
    }: {
        competition: Competition;
        serc: SERC;
        entity: Entity;
        leagues: string[];
        reason: "no_objective" | "several_objectives";
    } = $props();

    const description = $derived(
        reason === "several_objectives"
            ? `${entity.name}'s leagues (${leagues.join(", ")}) are assigned to more than one Objective, so there's no single Objective to mark against.`
            : leagues.length === 0
              ? `${entity.name} isn't in a league, so there's no Objective to mark against.`
              : `No Objective has been assigned to ${leagues.join(", ")}, so there's no Objective to mark ${entity.name} against.`,
    );
</script>

<AppHead title="Mark - {serc.name} - {competition.name}" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{serc.name}</h2>

    <BackLink href={home({ competition, serc })} label="Back to {serc.name}" class="mt-2 mb-4" />

    <EmptyState icon={TriangleAlert} title="Can't mark {entity.name} yet" {description}>
        <p class="mt-2 text-sm text-gray-500">
            Ask your organiser to fix this, then refresh this page.
        </p>
    </EmptyState>
</section>
