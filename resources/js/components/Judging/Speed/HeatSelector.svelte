<script
    lang="ts"
    generics="THref extends (...args: any[]) => { url: string; method: 'get' }"
>
    import { markTime } from "@/actions/App/Http/Controllers/DigitalJudge/Event/EventJudgeController";

    import {
        index,
        home,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { setTank } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import ConditionalLink from "@/components/ConditionalLink.svelte";

    import { appState } from "@/lib/stores/appState";
    import { toastInfo } from "@/lib/toast.svelte";

    import type { Competition, Event, Heat } from "@/types/base";

    import { page, Link } from "@inertiajs/svelte";
    import { ArrowRight, Check, House } from "@lucide/svelte";

    type HeatRouteParams = Extract<
        Parameters<THref>[0],
        { heat: string | number }
    >;

    let {
        heats,
        href,
        params,
    }: {
        heats: Heat[];
        href: THref;
        params: Omit<HeatRouteParams, "heat">;
    } = $props();

    function canHeatBeSelected(heat: Heat): boolean {
        return !heat.complete || page.props.judge.isHeadRef;
    }
</script>

<div class="flex flex-col space-y-3 w-full">
    {#each heats as heat}
        <ConditionalLink
            condition={canHeatBeSelected(heat)}
            href={href({ ...params, heat: heat.heat })}
            ><Button
                label="Heat {heat.heat}"
                variant={!canHeatBeSelected(heat) ? "success" : "primary"}
                icon={!canHeatBeSelected(heat) ? Check : ArrowRight}
                class="w-full {!canHeatBeSelected(heat) &&
                    'cursor-not-allowed!'} "
                onclick={() => {
                    !canHeatBeSelected(heat) &&
                        toastInfo("This heat is already complete");
                }}
            />
        </ConditionalLink>
    {/each}
</div>
