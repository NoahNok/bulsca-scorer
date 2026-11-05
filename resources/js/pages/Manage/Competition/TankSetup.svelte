<!--
    Tank setup (manage.competitions.heats_and_draws.draws.tank_setup), in CompetitionLayout, full width, with
    tanks only. Select Leagues, then click a tank to move them into it; nothing saves until Save,
    which draws every Entity again League by League, each League's Entities shuffled, and replaces the
    Draw. Every League has to be in a tank first. Leaving with changes asks first (UnsavedChanges).
-->
<script lang="ts">
    import { edit as editDraw, saveTankSetup } from "@/actions/App/Http/Controllers/Manage/DrawsController";
    import { index as overview } from "@/actions/App/Http/Controllers/Manage/HeatsAndDrawsController";
    import { confirm } from "@/lib/confirm";
    import { toastError } from "@/lib/toast.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import RemoveButton from "@/manage/orders/RemoveButton.svelte";
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

{#snippet league(id: number, wide = false)}
    {@const option = byId.get(id)}
    {#if option}
        <button
            type="button"
            class={["v2-chip", wide && "relative z-10 w-full justify-between"]}
            aria-pressed={selected.includes(id)}
            onclick={() => toggle(id)}
        >
            {option.name}
            <span class="tabular-nums opacity-70">{option.count}</span>
        </button>
    {/if}
{/snippet}

<PageHeading back={{ href: overview(comp).url, label: "Heats & Draws" }} title="Tank setup">
    {#snippet lead()}
        Select Leagues, then put them in a tank. Saving draws everyone again, League by League, in the
        <Link href={editDraw(comp).url} class="v2-link-inline">draw</Link>.
    {/snippet}
    {#snippet actions()}
        <button type="button" class="v2-btn v2-btn-primary" disabled={saving || !dirty} onclick={save}>Save</button>
    {/snippet}
</PageHeading>

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
            <!-- With Leagues selected, the Put here button's ::after covers the card, so the whole tank is its target;
                 the chips and remove button sit above it -->
            <div
                class={[
                    "relative flex flex-col rounded-md border border-v2-border bg-v2-sunk p-4",
                    selected.length > 0 && "has-[[data-put]:hover]:border-v2-teal has-[[data-put]:hover]:bg-v2-teal-soft",
                ]}
            >
                <div class="flex items-start gap-2">
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold">Tank {index + 1}</h4>
                        <p class="text-sm text-v2-muted tabular-nums">{entries(tank)} {entries(tank) === 1 ? "entry" : "entries"}</p>
                    </div>
                    <div class="relative z-10">
                        <RemoveButton label="Remove Tank {index + 1}" title="Remove tank" onclick={() => removeTank(index)} />
                    </div>
                </div>
                {#if tank.length > 0}
                    <div class="mt-3 grid gap-2">
                        {#each tank as id (id)}{@render league(id, true)}{/each}
                    </div>
                {/if}
                {#if selected.length > 0}
                    <button
                        type="button"
                        data-put
                        class="mt-3 cursor-pointer rounded-md border border-dashed border-v2-muted py-2 text-sm font-semibold text-v2-muted after:absolute after:inset-0 after:rounded-md hover:border-v2-teal hover:text-v2-teal-text"
                        aria-label="Put the selected Leagues in Tank {index + 1}"
                        onclick={() => putIn(index)}
                    >
                        Put here
                    </button>
                {:else if tank.length === 0}
                    <p class="mt-3 text-sm text-v2-muted">Empty.</p>
                {/if}
            </div>
        {/each}
    </div>
    {#if assigned.length === 0}
        <EmptyState class="mt-3">No tanks yet. Add one to start.</EmptyState>
    {/if}
</section>
