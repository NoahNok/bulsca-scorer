<!--
    A competition's Settings (manage.competitions.settings, Organisers only), in CompetitionLayout. A
    SideMenu picks one part at a time, each saved on its own: Details, Setup, Heats, Names and Status (all
    through updateCompetitionSettings, which takes any of its fields), then the Digital Judge events, the
    accounts that can manage it, and deleting it. Leaving a form with unsaved changes asks first.
-->
<script lang="ts">
    import {
        deleteComp,
        deleteCompetitionAccount,
        editCompetitionAccount,
        inviteCompetitionAccount,
        updateCompetitionSettings,
    } from "@/actions/App/Http/Controllers/CompetitionController";
    import { settingsPost } from "@/actions/App/Http/Controllers/DigitalJudge/DigitalJudgeController";
    import { confirm } from "@/lib/confirm";
    import Field from "@/manage/components/Field.svelte";
    import RichText from "@/manage/components/RichText.svelte";
    import SideMenu, { type SideMenuItem } from "@/manage/components/SideMenu.svelte";
    import SideMenuPart from "@/manage/components/SideMenuPart.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import type { CompetitionAccount, CompetitionEvent, CompetitionHeader } from "@/manage/types";
    import { search } from "@/routes/accounts";
    import { Form, router } from "@inertiajs/svelte";
    import { tick } from "svelte";

    let {
        competition,
        settings,
        digitalJudge,
        accounts,
        accessTypes,
    }: {
        competition: CompetitionHeader;
        settings: {
            name: string;
            when: string;
            where: string;
            max_lanes: number;
            /** ISO, in UTC */
            serc_start_time: string | null;
            show_competition: boolean;
            can_be_live: boolean;
            select_league_first: boolean;
            use_tanks: boolean;
            use_seeds: boolean;
            heats_per_event: boolean;
            seed_per_event: boolean;
            team_format: string;
            competitor_format: string;
            status_message: string | null;
        };
        digitalJudge: { enabled: boolean; showTeams: boolean; events: (CompetitionEvent & { enabled: boolean })[] };
        accounts: CompetitionAccount[];
        /** Access key to its name */
        accessTypes: Record<string, string>;
    } = $props();

    const SECTIONS = [
        { id: "details", label: "Details" },
        { id: "setup", label: "Setup" },
        { id: "heats", label: "Heats" },
        { id: "names", label: "Names" },
        { id: "status", label: "Status" },
    ];

    // The menu's parts, each its own form: SECTIONS (all saved by updateCompetitionSettings), then a rule,
    // then the rest
    const PARTS: SideMenuItem[] = [
        ...SECTIONS,
        { id: "digital-judge", label: "Digital Judge", rule: true },
        { id: "accounts", label: "Accounts" },
        { id: "delete", label: "Delete", danger: true },
    ];

    // The live examples under Names, kept outside its form, so discarding its changes resets them too
    // svelte-ignore state_referenced_locally
    let teamFormat = $state(settings.team_format);
    // svelte-ignore state_referenced_locally
    let competitorFormat = $state(settings.competitor_format);

    function resetNamePreviews() {
        teamFormat = settings.team_format;
        competitorFormat = settings.competitor_format;
    }

    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    const sercStart = $derived(localDateTime(settings.serc_start_time));

    function localDateTime(iso: string | null): string {
        if (!iso) return "";
        const date = new Date(iso);
        const pad = (n: number) => String(n).padStart(2, "0");
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    }

    function teamExample(format: string) {
        return format.replace(/:C/g, "Club").replace(/:L/g, "Youth").replace(/:N/g, "Team").replace(/:S/g, "Alex, Sam");
    }

    function competitorExample(format: string) {
        return format.replace(/:C/g, "Club").replace(/:L/g, "Youth").replace(/:N/g, "Alex").replace(/:T/g, "Team");
    }

    function focusFirstError(errors: Record<string, string>) {
        const field = Object.keys(errors)[0];
        if (field) document.getElementById(`s-${field.replace(":", "-")}`)?.focus();
    }

    function accessSummary(account: CompetitionAccount) {
        if (account.owner) return "Owner";
        if (account.access.includes("admin")) return "Everything";
        return account.access.map((key) => accessTypes[key] ?? key).join(", ") || "None";
    }

    // Accounts: one dialog to edit an account's access, one to invite someone

    let editing = $state<CompetitionAccount | null>(null);
    let editDialog: HTMLDialogElement;
    let inviteDialog: HTMLDialogElement;
    let removeError = $state<string | null>(null);

    async function edit(account: CompetitionAccount) {
        editing = account;
        removeError = null;
        await tick();
        editDialog.showModal();
    }

    async function remove(account: CompetitionAccount) {
        const ok = await confirm({
            title: `Remove ${account.name}?`,
            description: "They won't be able to manage this competition any more.",
            confirmLabel: "Remove",
            confirmVariant: "danger",
        });
        if (!ok) return;

        router.delete(deleteCompetitionAccount({ comp: competition.id, account: account.id }).url, {
            preserveScroll: true,
            onSuccess: () => editDialog.close(),
            onError: (errors) => (removeError = errors.account ?? "They couldn't be removed."),
        });
    }

    // Existing accounts as you type an email, as the browser's own suggestions
    let suggestions = $state<{ name: string; email: string }[]>([]);
    let searchTimer: ReturnType<typeof setTimeout>;

    function suggest(event: Event) {
        const email = (event.currentTarget as HTMLInputElement).value.trim();
        clearTimeout(searchTimer);
        if (email.length < 3) {
            suggestions = [];
            return;
        }
        searchTimer = setTimeout(async () => {
            const response = await fetch(search(email).url, { headers: { Accept: "application/json" } });
            suggestions = response.ok ? await response.json() : [];
        }, 200);
    }

    // Deleting needs the name typed out
    let confirmName = $state("");

    // Classes several parts of the page share
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
    // Two columns from sm; the grid spaces the fields, not .v2-field's own margin
    const fieldGrid = "grid gap-4 *:mt-0 sm:grid-cols-2";
    const heading = "text-lg font-extrabold uppercase";
    const fieldset = "mb-5 flex flex-col gap-[0.6rem] *:mt-0";
    const accessGrid = "grid grid-cols-2 gap-x-4 gap-y-[0.6rem] *:mt-0";

    // Close a dialog on a click on its backdrop (the dialog itself, outside its content)
    function onBackdrop(event: MouseEvent) {
        if (event.target === event.currentTarget) (event.currentTarget as HTMLDialogElement).close();
    }
</script>

<svelte:head>
    <title>Settings - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet saveRow(label: string, processing: boolean, isDirty: boolean)}
    <UnsavedChanges dirty={isDirty} />
    <div class="mt-6 flex items-center justify-end gap-4">
        {#if isDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
        <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>{label}</button>
    </div>
{/snippet}

<SideMenu items={PARTS} label="Settings">
    {#each SECTIONS as { id, label } (id)}
        <SideMenuPart {id} labelledby="s-h-{id}" ondiscard={id === "names" ? resetNamePreviews : undefined}>
            <h2 id="s-h-{id}" class="mb-4 {heading}">{label}</h2>
            <Form
                action={updateCompetitionSettings(competition.id)}
                options={{ preserveScroll: true }}
                setDefaultsOnSuccess
                onError={focusFirstError}
            >
                {#snippet children({ errors, processing, isDirty })}
                    {#if id === "details"}
                        <div class={fieldGrid}>
                            <Field id="s-name" name="name" label="Name" value={settings.name} required minlength={3} maxlength={255} error={errors.name} />
                            <Field id="s-when" name="when" type="date" label="Date" value={settings.when} required error={errors.when} />
                            <Field
                                id="s-where"
                                name="where"
                                label="Venue"
                                value={settings.where}
                                required
                                minlength={3}
                                maxlength={255}
                                error={errors.where}
                            />
                        </div>
                    {:else if id === "setup"}
                        <input type="hidden" name="timezone" value={timezone} />
                        <div class={fieldGrid}>
                            <Field
                                id="s-max_lanes"
                                name="max_lanes"
                                type="number"
                                label="Lanes"
                                value={String(settings.max_lanes)}
                                required
                                min={1}
                                inputmode="numeric"
                                error={errors.max_lanes}
                            />
                            <Field
                                id="s-serc_start_time"
                                name="serc_start_time"
                                type="datetime-local"
                                label="SERCs start"
                                value={sercStart}
                                hint="In your time zone, {timezone}."
                                error={errors.serc_start_time}
                            />
                        </div>
                        <div class="mt-6 border-t border-v2-border pt-6">
                            <Switch
                                name="show_competition"
                                label="Public"
                                checked={settings.show_competition}
                                hint="It shows up in Explore and search."
                            />
                            <Switch
                                name="can_be_live"
                                label="Live results"
                                checked={settings.can_be_live}
                                hint="People can follow it on the live site on the day."
                            />
                            <Switch
                                name="select_league_first"
                                label="Choose a league first"
                                checked={settings.select_league_first}
                                hint="People pick a league before they see any results."
                            />
                        </div>
                    {:else if id === "heats"}
                        <div>
                            <Switch name="ss:use_tanks" label="Tanks" checked={settings.use_tanks} />
                            <Switch name="use_seeds" label="Seed times" checked={settings.use_seeds} />
                            <Switch name="heats_per_event" label="Heats per event" checked={settings.heats_per_event} />
                            <Switch name="seed_per_event" label="Seed per event" checked={settings.seed_per_event} />
                        </div>
                    {:else if id === "names"}
                        <p class={lead}>
                            How team and competitor names are shown. The codes are swapped for each one's details.
                        </p>
                        <div class={fieldGrid}>
                            <div>
                                <Field
                                    id="s-team_format"
                                    name="team_format"
                                    label="Team names"
                                    value={settings.team_format}
                                    required
                                    hint=":C club, :L league, :N name, :S competitors"
                                    error={errors.team_format}
                                    oninput={(event) => (teamFormat = event.currentTarget.value)}
                                />
                                <p class="mt-2 text-sm font-semibold">{teamExample(teamFormat)}</p>
                            </div>
                            <div>
                                <Field
                                    id="s-competitor_format"
                                    name="competitor_format"
                                    label="Competitor names"
                                    value={settings.competitor_format}
                                    required
                                    hint=":C club, :L league, :N name, :T team"
                                    error={errors.competitor_format}
                                    oninput={(event) => (competitorFormat = event.currentTarget.value)}
                                />
                                <p class="mt-2 text-sm font-semibold">{competitorExample(competitorFormat)}</p>
                            </div>
                        </div>
                    {:else}
                        <div class="v2-field">
                            <span id="s-status-label" class="text-sm font-semibold">Status message</span>
                            <span class="v2-hint">Shown to Organisers above every page of this competition. Leave it empty to hide it.</span>
                            <RichText
                                id="s-status_message"
                                name="status_message"
                                labelledby="s-status-label"
                                value={settings.status_message}
                                placeholder="e.g. Warm-up moved to 9:30"
                            />
                            {#if errors.status_message}<span class="v2-error">{errors.status_message}</span>{/if}
                        </div>
                    {/if}

                    {@render saveRow("Save changes", processing, isDirty)}
                {/snippet}
            </Form>
        </SideMenuPart>
    {/each}

    <SideMenuPart id="digital-judge" labelledby="s-judge">
        <h2 id="s-judge" class={heading}>Digital Judge</h2>
        <p class={lead}>
            {digitalJudge.enabled
                ? "Officials can judge the events you tick here on their phones."
                : "Digital Judge is off. Turn it on from the Overview, and these events can be judged on officials' phones."}
        </p>

        <Form action={settingsPost(competition.id)} options={{ preserveScroll: true }} setDefaultsOnSuccess>
            {#snippet children({ processing, isDirty })}
                {#if digitalJudge.events.length > 0}
                    <fieldset class={fieldset}>
                        <legend class="mb-[0.6rem] text-sm font-semibold">Events</legend>
                        {#each digitalJudge.events as event (event.key)}
                            <label class="v2-check">
                                <input type="checkbox" name={event.key} checked={event.enabled} />
                                {event.name}
                                {#if event.serc}<span class="v2-badge">SERC</span>{/if}
                            </label>
                        {/each}
                    </fieldset>
                {:else}
                    <p class="text-sm text-v2-muted">There are no events yet.</p>
                {/if}

                <label class="v2-check items-start">
                    <input type="checkbox" name="show_teams_to_judges" checked={digitalJudge.showTeams} class="mt-[0.2rem]" />
                    <span>
                        Show team names
                        <span class="v2-hint block">Judges see each team's name when they mark, not just its number.</span>
                    </span>
                </label>

                {@render saveRow("Save Digital Judge", processing, isDirty)}
            {/snippet}
        </Form>
    </SideMenuPart>

    <SideMenuPart id="accounts" labelledby="s-accounts">
        <div class="flex items-center justify-between gap-4">
            <h2 id="s-accounts" class={heading}>Accounts</h2>
            <button type="button" class="v2-btn v2-btn-sm" onclick={() => inviteDialog.showModal()}>Invite someone</button>
        </div>
        <p class={lead}>People who can manage this competition, and what they can open.</p>

        <table class="w-full border-t border-v2-border">
            <thead class="sr-only">
                <tr>
                    <th>Name</th>
                    <th>Access</th>
                </tr>
            </thead>
            <tbody>
                {#each accounts as account (account.id)}
                    <tr class="v2-row *:border-b *:border-v2-border *:px-2 *:py-3 *:align-middle">
                        <td>
                            <button type="button" class="v2-row-link flex cursor-pointer flex-col items-start text-left focus-visible:outline-none" onclick={() => edit(account)}>
                                <span class="font-bold">{account.name}{account.you ? " (you)" : ""}</span>
                                <span class="text-sm text-v2-muted">{account.email}</span>
                            </button>
                        </td>
                        <td class="text-right text-sm text-v2-muted">{accessSummary(account)}</td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </SideMenuPart>

    <SideMenuPart id="delete" labelledby="s-delete">
        <h2 id="s-delete" class={heading}>Delete competition</h2>
        <p class={lead}>This removes {competition.name} with all its events, entries and results. It can't be undone.</p>

        <Form action={deleteComp(competition.id)} class="[&_.v2-field]:mb-4 [&_.v2-field]:max-w-sm">
            {#snippet children({ errors, processing })}
                <Field
                    id="s-delete-name"
                    name="name"
                    label="Type its name to confirm"
                    placeholder={competition.name}
                    autocomplete="off"
                    error={errors.name}
                    oninput={(event) => (confirmName = event.currentTarget.value)}
                />
                <div>
                    <button type="submit" class="v2-btn v2-btn-danger" disabled={processing || confirmName !== competition.name}>
                        Delete competition
                    </button>
                </div>
            {/snippet}
        </Form>
    </SideMenuPart>
</SideMenu>

<dialog bind:this={editDialog} class="v2-modal" aria-labelledby="s-edit-title" onclick={onBackdrop}>
    {#if editing}
        {@const account = editing}
        <h2 id="s-edit-title">{account.name}</h2>
        <p>{account.email}</p>

        {#if account.owner}
            <p class="mt-5">They own this competition, so they can do everything.</p>
            <div class="v2-modal-actions">
                <button type="button" class="v2-btn" onclick={() => editDialog.close()}>Close</button>
            </div>
        {:else}
            {#key account.id}
                <Form
                    action={editCompetitionAccount({ comp: competition.id, account: account.id })}
                    options={{ preserveScroll: true }}
                    onSuccess={() => editDialog.close()}
                >
                    {#snippet children({ errors, processing })}
                        <fieldset class="mt-5 {fieldset}">
                            <legend class="mb-[0.6rem] text-sm font-semibold">What they can open</legend>
                            <div class={accessGrid}>
                                {#each Object.entries(accessTypes) as [key, label] (key)}
                                    <label class="v2-check">
                                        <input type="checkbox" name="access[]" value={key} checked={account.access.includes(key)} />
                                        {label}
                                    </label>
                                {/each}
                            </div>
                            <span class="v2-hint">Admin gives them everything.</span>
                            {#if errors.access}<span class="v2-error">{errors.access}</span>{/if}
                            {#if removeError}<span class="v2-error">{removeError}</span>{/if}
                        </fieldset>
                        <div class="v2-modal-actions">
                            {#if !account.you}
                                <button type="button" class="v2-btn v2-btn-ghost mr-auto text-v2-danger-text" onclick={() => remove(account)}>Remove</button>
                            {/if}
                            <button type="button" class="v2-btn" onclick={() => editDialog.close()}>Cancel</button>
                            <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Save access</button>
                        </div>
                    {/snippet}
                </Form>
            {/key}
        {/if}
    {/if}
</dialog>

<dialog bind:this={inviteDialog} class="v2-modal" aria-labelledby="s-invite-title" onclick={onBackdrop}>
    <h2 id="s-invite-title">Invite someone</h2>
    <p>We'll email them a link. If they don't have an account yet, they'll make one.</p>

    <Form
        action={inviteCompetitionAccount(competition.id)}
        options={{ preserveScroll: true }}
        resetOnSuccess
        onSuccess={() => inviteDialog.close()}
    >
        {#snippet children({ errors, processing })}
            <div class="mt-5">
                <Field
                    id="s-invite-email"
                    name="email"
                    type="email"
                    label="Email"
                    required
                    autocomplete="off"
                    list="s-invite-suggestions"
                    error={errors.email}
                    oninput={suggest}
                />
                <datalist id="s-invite-suggestions">
                    {#each suggestions as suggestion (suggestion.email)}
                        <option value={suggestion.email}>{suggestion.name}</option>
                    {/each}
                </datalist>
            </div>
            <fieldset class="mt-5 {fieldset}">
                <legend class="mb-[0.6rem] text-sm font-semibold">What they can open</legend>
                <div class={accessGrid}>
                    {#each Object.entries(accessTypes) as [key, label] (key)}
                        <label class="v2-check">
                            <input type="checkbox" name="access[]" value={key} />
                            {label}
                        </label>
                    {/each}
                </div>
                <span class="v2-hint">Admin gives them everything.</span>
                {#if errors.access}<span class="v2-error">Choose what they can open.</span>{/if}
            </fieldset>
            <div class="v2-modal-actions">
                <button type="button" class="v2-btn" onclick={() => inviteDialog.close()}>Cancel</button>
                <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Send invite</button>
            </div>
        {/snippet}
    </Form>
</dialog>
