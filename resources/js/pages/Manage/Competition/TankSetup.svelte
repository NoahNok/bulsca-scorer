<!--
    Tank setup (manage.competitions.heats_and_draws.draws.tank_setup), in CompetitionLayout, full width, with
    tanks only. Select Leagues, then "Put here" on a tank to move them into it; nothing saves until Save,
    which draws every Entity again League by League, each League's Entities shuffled, and replaces the
    Draw. Every League has to be in a tank first. Leaving with changes asks first (UnsavedChanges).
-->
<script lang="ts">
    import { edit as editDraw, saveTankSetup } from "@/actions/App/Http/Controllers/Manage/DrawsController";
    import { index as overview } from "@/actions/App/Http/Controllers/Manage/HeatsAndDrawsController";
    import { confirm } from "@/lib/confirm";
    import { toastError } from "@/lib/toast.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { CompetitionHeader } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        leagues,
        tanks,
    }: {
        competition: CompetitionHeader;
        /** Each League with how many Entities it has; -1 is those without a League */
        leagues: { id: number; name: string; count: number }[];
        /** The League ids in each Tank now */
        tanks: number[][];
    } = $props();

    const comp = $derived(competition.id);

    // svelte-ignore state_referenced_locally
    let assigned = $state(tanks.map((tank) => [...tank]));
    let selected = $state<number[]>([]);
    let saving = $state(false);

    const byId = $derived(new Map(leagues.map((league) => [league.id, league])));
    const unplaced = $derived(leagues.filter((league) => !assigned.some((tank) => tank.includes(league.id))));
    const dirty = $derived(JSON.stringify(assigned) !== JSON.stringify(tanks));
    const entries = (ids: number[]) => ids.reduce((total, id) => total + (byId.get(id)?.count ?? 0), 0);

    function toggle(id: number) {
        selected = selected.includes(id) ? selected.filter((each) => each !== id) : [...selected, id];
    }

    function putIn(index: number) {
        assigned = assigned.map((tank, at) => [...tank.filter((id) => !selected.includes(id)), ...(at === index ? selected : [])]);
        selected = [];
    }

    function removeTank(index: number) {
        assigned = assigned.filter((_, at) => at !== index);
    }

    async function unassignAll() {
        const ok = await confirm({
            title: "Take every League out of the tanks?",
            description: "The tanks stay, empty. Nothing changes until you save.",
            confirmLabel: "Take them all out",
            confirmVariant: "danger",
        });
        if (ok) assigned = assigned.map(() => []);
    }

    async function save() {
        if (unplaced.length > 0) {
            toastError("Put every League in a tank first.");
            return;
        }
        const ok = await confirm({
            title: "Draw again from these tanks?",
            description: "Everyone is drawn again, League by League, and the current draw is replaced.",
            confirmLabel: "Save and draw",
        });
        if (!ok) return;

        router.post(
            saveTankSetup(comp).url,
            { tanks: assigned },
            {
                onStart: () => (saving = true),
                onFinish: () => (saving = false),
                onError: (errors) => toastError(Object.values(errors)[0] ?? "The tanks couldn't be saved."),
            },
        );
    }
</script>

<svelte:head>
    <title>Tank setup - {competition.name} - Scoring.Events</title>
</svelte:head>

<UnsavedChanges dirty={dirty && !saving} />

{#snippet league(id: number)}
    {@const option = byId.get(id)}
    {#if option}
        <button type="button" class="v2-chip" aria-pressed={selected.includes(id)} onclick={() => toggle(id)}>
            {option.name}
            <span class="tabular-nums opacity-70">{option.count}</span>
        </button>
    {/if}
{/snippet}

<div class="mb-6 flex flex-wrap items-end gap-4">
    <div class="min-w-0 flex-1">
        <Link href={overview(comp).url} class="v2-link-muted text-sm">← Heats & Draws</Link>
        <h2 class="mt-1 text-2xl font-extrabold uppercase">Tank setup</h2>
        <p class="mt-[0.35rem] text-[0.9375rem]/[1.6] text-v2-muted">
            Select Leagues, then put them in a tank. Saving draws everyone again, League by League, in the
            <Link href={editDraw(comp).url} class="v2-link-inline">draw</Link>.
        </p>
    </div>
    <button type="button" class="v2-btn v2-btn-primary" disabled={saving || !dirty} onclick={save}>Save</button>
</div>

<section aria-labelledby="ts-leagues">
    <h3 id="ts-leagues" class="text-lg font-extrabold uppercase">Not in a tank</h3>
    {#if unplaced.length === 0}
        <p class="mt-2 text-[0.9375rem] text-v2-muted">Every League is in a tank.</p>
    {:else}
        <div class="mt-3 flex flex-wrap gap-2" role="group" aria-label="Leagues not in a tank">
            {#each unplaced as option (option.id)}{@render league(option.id)}{/each}
        </div>
    {/if}
    <p class="mt-3 text-sm text-v2-muted">
        {selected.length === 0 ? "Nothing selected." : `${entries(selected)} ${entries(selected) === 1 ? "entry" : "entries"} selected.`}
    </p>
</section>

<section aria-labelledby="ts-tanks" class="mt-8">
    <div class="flex flex-wrap items-center gap-3">
        <h3 id="ts-tanks" class="mr-auto text-lg font-extrabold uppercase">Tanks</h3>
        {#if assigned.some((tank) => tank.length > 0)}
            <button type="button" class="v2-btn v2-btn-sm v2-btn-ghost" onclick={unassignAll}>Take every League out</button>
        {/if}
        <button type="button" class="v2-btn v2-btn-sm" onclick={() => (assigned = [...assigned, []])}>Add tank</button>
    </div>

    <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        {#each assigned as tank, index (index)}
            <div class="flex flex-col rounded-md border border-v2-border bg-v2-card p-4">
                <div class="flex items-start gap-2">
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold">Tank {index + 1}</h4>
                        <p class="text-sm text-v2-muted tabular-nums">{entries(tank)} {entries(tank) === 1 ? "entry" : "entries"}</p>
                    </div>
                    <button
                        type="button"
                        class="cursor-pointer rounded px-1.5 py-1 font-semibold text-v2-muted hover:bg-v2-danger-soft hover:text-v2-danger-text"
                        aria-label="Remove Tank {index + 1}"
                        title="Remove tank"
                        onclick={() => removeTank(index)}>×</button
                    >
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    {#each tank as id (id)}{@render league(id)}{/each}
                </div>
                {#if selected.length > 0}
                    <button type="button" class="v2-btn v2-btn-sm mt-3 self-start" onclick={() => putIn(index)}>Put here</button>
                {:else if tank.length === 0}
                    <p class="text-sm text-v2-muted">Empty.</p>
                {/if}
            </div>
        {/each}
    </div>
    {#if assigned.length === 0}
        <p class="mt-3 rounded-md border border-dashed border-v2-border px-4 py-6 text-center text-[0.9375rem] text-v2-muted">No tanks yet. Add one to start.</p>
    {/if}
</section>
