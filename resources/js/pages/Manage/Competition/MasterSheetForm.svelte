<!--
    New Master sheet (manage.competitions.results.master.create) and Edit sheet (….edit), one full-width
    page in CompetitionLayout: its name, whether it sums places or points (and what a Club missing from a
    sheet counts as), the Results sheets it combines, and Clubs to leave out. It always ranks Clubs.
-->
<script lang="ts">
    import { show, store, update } from "@/actions/App/Http/Controllers/Manage/MasterSheetsController";
    import { index } from "@/actions/App/Http/Controllers/Manage/ResultSheetsController";
    import Alert from "@/manage/components/Alert.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { CompetitionHeader } from "@/manage/types";
    import { Link, useForm } from "@inertiajs/svelte";

    let {
        competition,
        master,
        sheets,
        clubs,
        published,
    }: {
        competition: CompetitionHeader;
        /** Null for a new one */
        master: { id: number; name: string; sum_over: "position" | "totalPoints"; default_value: number; sheets: number[]; exclude: number[] } | null;
        /** Every Results sheet in the competition */
        sheets: { id: number; name: string }[];
        clubs: { id: number; name: string }[];
        published: boolean;
    } = $props();

    const comp = $derived(competition.id);
    const backUrl = $derived(master ? show({ comp, master: master.id }).url : index(comp).url);

    // svelte-ignore state_referenced_locally
    const form = useForm({
        name: master?.name ?? "",
        sum_over: master?.sum_over ?? "position",
        default_value: master?.default_value ?? 0,
        sheets: [...(master?.sheets ?? [])],
        exclude: [...(master?.exclude ?? [])],
    });

    function toggle(list: "sheets" | "exclude", id: number) {
        form[list] = form[list].includes(id) ? form[list].filter((picked) => picked !== id) : [...form[list], id];
    }

    function save(submit: SubmitEvent) {
        submit.preventDefault();
        if (master) form.post(update({ comp, master: master.id }).url, { onSuccess: () => form.defaults() });
        else form.post(store(comp).url);
    }

    const errors = $derived(form.errors as Record<string, string>);
    const sheetsError = $derived(Object.entries(errors).find(([key]) => key === "sheets" || key.startsWith("sheets."))?.[1]);
    const heading = "text-lg font-extrabold uppercase";
    const lead = "mt-[0.35rem] mb-4 text-[0.9375rem]/[1.6] text-v2-muted";
    const section = "border-t border-v2-border pt-6";
</script>

<svelte:head>
    <title>{master ? `Edit ${master.name}` : "New Master sheet"} - {competition.name} - Scoring.Events</title>
</svelte:head>

<PageHeading back={{ href: backUrl, label: master ? master.name : "Results" }} title={master ? "Edit sheet" : "New Master sheet"} />

{#if master && published}
    <div class="mt-5">
        <Alert tone="info" title="Results are published">
            <p class="mt-1">Your changes show in the public results as soon as you save.</p>
        </Alert>
    </div>
{/if}

<form onsubmit={save} class="mt-6 flex max-w-4xl flex-col gap-8">
    {#if master}<UnsavedChanges dirty={form.isDirty} />{/if}

    <section aria-labelledby="mf-details" class="flex flex-col gap-4">
        <h2 id="mf-details" class={heading}>Details</h2>
        <p class="-mt-2 text-[0.9375rem]/[1.6] text-v2-muted">A Master sheet ranks Clubs.</p>
        <div class="grid gap-4 *:mt-0 sm:grid-cols-3">
            <div class={["v2-field", form.errors.name && "is-invalid"]}>
                <label for="mf-name">Name</label>
                <input
                    id="mf-name"
                    class="v2-input w-full"
                    bind:value={form.name}
                    autocomplete="off"
                    aria-invalid={form.errors.name ? "true" : undefined}
                    aria-describedby={form.errors.name ? "mf-name-error" : undefined}
                />
                {#if form.errors.name}<span id="mf-name-error" class="v2-error">{form.errors.name}</span>{/if}
            </div>
            <div class="v2-field">
                <label for="mf-sum">Sums</label>
                <select id="mf-sum" class="v2-input w-full" bind:value={form.sum_over}>
                    <option value="position">Places, lowest wins</option>
                    <option value="totalPoints">Points, highest wins</option>
                </select>
            </div>
            <div class={["v2-field", form.errors.default_value && "is-invalid"]}>
                <label for="mf-default">Missing from a sheet counts as</label>
                <input
                    id="mf-default"
                    type="number"
                    step="any"
                    class="v2-input w-full"
                    bind:value={form.default_value}
                    aria-invalid={form.errors.default_value ? "true" : undefined}
                    aria-describedby={form.errors.default_value ? "mf-default-error" : undefined}
                />
                {#if form.errors.default_value}<span id="mf-default-error" class="v2-error">{form.errors.default_value}</span>{/if}
            </div>
        </div>
    </section>

    <section aria-labelledby="mf-sheets" class={section}>
        <h2 id="mf-sheets" class={heading}>Results sheets</h2>
        <p class={lead}>Pick the sheets it combines.</p>
        {#if sheets.length === 0}
            <p class="text-[0.9375rem] text-v2-muted">There are no Results sheets to combine yet.</p>
        {:else}
            <ul class="flex flex-wrap gap-2" role="group" aria-label="Results sheets">
                {#each sheets as sheet (sheet.id)}
                    <li><button type="button" class="v2-chip" aria-pressed={form.sheets.includes(sheet.id)} onclick={() => toggle("sheets", sheet.id)}>{sheet.name}</button></li>
                {/each}
            </ul>
        {/if}
        {#if sheetsError}<span class="v2-error mt-2 block">{sheetsError}</span>{/if}
    </section>

    <section aria-labelledby="mf-exclude" class={section}>
        <h2 id="mf-exclude" class={heading}>Leave out</h2>
        <p class={lead}>Clubs picked here aren't ranked.</p>
        {#if clubs.length === 0}
            <p class="text-[0.9375rem] text-v2-muted">This competition has no Clubs yet.</p>
        {:else}
            <ul class="flex flex-wrap gap-2" role="group" aria-label="Clubs to leave out">
                {#each clubs as club (club.id)}
                    <li><button type="button" class="v2-chip" aria-pressed={form.exclude.includes(club.id)} onclick={() => toggle("exclude", club.id)}>{club.name}</button></li>
                {/each}
            </ul>
        {/if}
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-4 border-t border-v2-border bg-v2-bg py-4">
        {#if errors.sheet}
            <span class="mr-auto text-sm font-semibold text-v2-danger-text">{errors.sheet}</span>
        {:else if form.hasErrors}
            <span class="mr-auto text-sm font-semibold text-v2-danger-text">Check the fields above.</span>
        {/if}
        {#if master && form.isDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
        <Link href={backUrl} class="v2-link-muted text-sm">Cancel</Link>
        <button type="submit" class="v2-btn v2-btn-primary" disabled={form.processing}>{master ? "Save sheet" : "Make sheet"}</button>
    </div>
</form>
