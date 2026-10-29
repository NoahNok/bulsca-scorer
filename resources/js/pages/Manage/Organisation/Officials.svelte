<!--
    An organisation's Officials (manage.organisations.officials, Organisers only), in OrganisationLayout:
    everyone who has officiated at one of its competitions, with their roles, how many competitions and
    the last one. Search and the role chips filter here, and Export CSV takes the same filters. Add to a
    competition opens a dialog to put one of them on an upcoming competition, which emails them.
-->
<script lang="ts">
    import { exportMethod, store } from "@/actions/App/Http/Controllers/Manage/OrganisationOfficialsController";
    import { shortDate } from "@/lib/dates";
    import ChipGroup from "@/manage/components/ChipGroup.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import OfficialRoleBadge from "@/manage/components/OfficialRoleBadge.svelte";
    import type { OrganisationHeader } from "@/manage/types";
    import { closeOnBackdrop } from "@/manage/ui";
    import { Form, Link } from "@inertiajs/svelte";
    import { tick } from "svelte";

    type Official = {
        id: number;
        name: string;
        email: string;
        /** Every role they've held here, Official before Referee */
        roles: string[];
        count: number;
        last: { name: string; url: string; when: string | null };
    };

    let {
        organisation,
        officials,
        roles,
        upcomingCompetitions,
    }: {
        organisation: OrganisationHeader;
        officials: Official[];
        /** Competition::$officialRoles */
        roles: Record<string, string>;
        /** Ones you can add officials to, soonest first */
        upcomingCompetitions: { id: number; name: string; when: string }[];
    } = $props();

    let query = $state("");
    let role = $state<string | null>(null);

    const filtering = $derived(query.trim() !== "" || role !== null);
    const shown = $derived.by(() => {
        const term = query.trim().toLowerCase();
        return officials.filter(
            (o) =>
                (role === null || o.roles.includes(role)) &&
                (!term || o.name.toLowerCase().includes(term) || o.email.toLowerCase().includes(term)),
        );
    });

    const exportUrl = $derived(
        exportMethod(organisation.id, { query: { q: query.trim() || undefined, role: role ?? undefined } }).url,
    );

    let adding = $state<Official | null>(null);
    let addDialog: HTMLDialogElement;

    async function add(official: Official) {
        adding = official;
        await tick();
        addDialog.showModal();
    }

    const plural = (n: number, word: string) => `${n} ${word}${n === 1 ? "" : "s"}`;
</script>

<svelte:head>
    <title>Officials - {organisation.name} - Scoring.Events</title>
</svelte:head>

<section aria-labelledby="of-heading">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 id="of-heading" class="text-lg font-extrabold uppercase">
            Officials <span class="text-v2-muted tabular-nums">{officials.length}</span>
        </h2>
        {#if officials.length > 0}
            <!-- A download, not a page -->
            <a href={exportUrl} class="v2-btn v2-btn-sm">Export CSV</a>
        {/if}
    </div>
    <p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">Everyone who has officiated at one of {organisation.name}'s competitions.</p>

    {#if officials.length === 0}
        <EmptyState>No one has officiated for {organisation.name} yet. Add officials from a competition's Officials tab.</EmptyState>
    {:else}
        <div class="flex flex-col gap-3">
            <ChipGroup
                label="Filter by role"
                all="All roles"
                options={Object.entries(roles).map(([id, name]) => ({ id, name }))}
                value={role}
                onpick={(picked) => (role = picked)}
            />
            <input type="search" class="v2-input w-full" placeholder="Search by name or email" aria-label="Search officials" bind:value={query} />
        </div>

        {#if filtering}
            <p class="mt-3 text-sm text-v2-muted" role="status">Showing {shown.length} of {officials.length}</p>
        {/if}

        <ul class="mt-4 border-t border-v2-border">
            {#each shown as official (official.id)}
                <li class="flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-v2-border px-2 py-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-bold wrap-anywhere">{official.name}</p>
                        <p class="text-sm text-v2-muted wrap-anywhere">{official.email}</p>
                        <p class="mt-1 text-[0.8125rem] text-v2-muted">
                            {plural(official.count, "competition")} · Last:
                            <Link href={official.last.url} class="v2-link-inline">{official.last.name}</Link>{#if official.last.when}, {shortDate(official.last.when)}{/if}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-1">
                        {#each official.roles as held (held)}
                            <OfficialRoleBadge role={held} {roles} />
                        {/each}
                    </div>
                    {#if upcomingCompetitions.length > 0}
                        <button type="button" class="v2-btn v2-btn-sm" onclick={() => add(official)}>
                            Add to a competition<span class="sr-only">: {official.name}</span>
                        </button>
                    {/if}
                </li>
            {/each}
        </ul>
        {#if shown.length === 0}
            <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No officials match your search.</p>
        {/if}
    {/if}
</section>

<dialog bind:this={addDialog} class="v2-modal" aria-labelledby="of-add-title" onclick={closeOnBackdrop}>
    {#if adding}
        {@const official = adding}
        <h2 id="of-add-title">Add to a competition</h2>
        <p>Adding <strong>{official.name}</strong>. We'll email them to let them know.</p>

        {#key official.id}
            <Form action={store(organisation.id)} options={{ preserveScroll: true }} onSuccess={() => addDialog.close()}>
                {#snippet children({ errors, processing })}
                    <input type="hidden" name="user_id" value={official.id} />
                    {#if errors.user_id}<p class="v2-error mt-4" role="alert">{errors.user_id}</p>{/if}
                    <div class="v2-field">
                        <label for="of-add-competition">Competition</label>
                        <select
                            id="of-add-competition"
                            name="competition_id"
                            class="v2-input w-full"
                            aria-invalid={errors.competition_id ? "true" : undefined}
                            aria-describedby={errors.competition_id ? "of-add-competition-error" : undefined}
                        >
                            {#each upcomingCompetitions as comp (comp.id)}
                                <option value={comp.id}>{comp.name}, {shortDate(comp.when)}</option>
                            {/each}
                        </select>
                        {#if errors.competition_id}<span id="of-add-competition-error" class="v2-error">{errors.competition_id}</span>{/if}
                    </div>
                    <div class="v2-field">
                        <label for="of-add-role">Role</label>
                        <select id="of-add-role" name="role" class="v2-input w-full" value="official">
                            {#each Object.entries(roles) as [key, label] (key)}
                                <option value={key}>{label}</option>
                            {/each}
                        </select>
                        {#if errors.role}<span class="v2-error">{errors.role}</span>{/if}
                    </div>
                    <div class="v2-modal-actions">
                        <button type="button" class="v2-btn" onclick={() => addDialog.close()}>Cancel</button>
                        <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Add</button>
                    </div>
                {/snippet}
            </Form>
        {/key}
    {/if}
</dialog>
