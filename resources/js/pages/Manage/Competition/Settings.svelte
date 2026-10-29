<!--
    A competition's Settings (manage.competitions.settings, Organisers only), in CompetitionLayout. A
    SideMenu picks one part at a time, each saved on its own: Details, Setup, Heats, Names and Status (all
    through updateCompetitionSettings, which takes any of its fields), then the Digital Judge events, the
    accounts that can manage it, and deleting it. Leaving a form with unsaved changes asks first.
-->
<script lang="ts">
    import {
        cancelCompetitionInvite,
        deleteComp,
        deleteCompetitionAccount,
        editCompetitionAccount,
        inviteCompetitionAccount,
        updateCompetitionSettings,
    } from "@/actions/App/Http/Controllers/CompetitionController";
    import { settingsPost } from "@/actions/App/Http/Controllers/DigitalJudge/DigitalJudgeController";
    import type { PendingVisit } from "@inertiajs/core";
    import { confirm } from "@/lib/confirm";
    import Accounts from "@/manage/components/Accounts.svelte";
    import Field from "@/manage/components/Field.svelte";
    import RichText from "@/manage/components/RichText.svelte";
    import SideMenu, { type SideMenuItem } from "@/manage/components/SideMenu.svelte";
    import SideMenuPart from "@/manage/components/SideMenuPart.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import type { Account, CompetitionEvent, CompetitionHeader, PendingInvite } from "@/manage/types";
    import { Form } from "@inertiajs/svelte";

    let {
        competition,
        settings,
        digitalJudge,
        accounts,
        invites,
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
            use_pools: boolean;
            /** Heat entries in pools, which turning pools off deletes */
            pooled_heats: number;
            team_format: string;
            competitor_format: string;
            status_message: string | null;
        };
        digitalJudge: { enabled: boolean; showTeams: boolean; events: (CompetitionEvent & { enabled: boolean })[] };
        accounts: Account[];
        invites: PendingInvite[];
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

    // Turning pools off deletes the heats already in pools, so Heats asks first, then saves again with
    // clear_pooled_heats. Without it the save is refused
    const forms: Record<string, { submit: () => void }> = {};
    let clearPooledHeats = false;

    function confirmClearingPools(visit: PendingVisit) {
        const turningOff = settings.use_pools && String((visit.data as Record<string, unknown>).use_pools) === "0";
        if (!turningOff || settings.pooled_heats === 0 || clearPooledHeats) return;

        confirm({
            title: "Turn pools off?",
            description: `This deletes all ${settings.pooled_heats} heat entries in pools, with their order of finish and DQ submissions. It can't be undone.`,
            confirmLabel: "Delete heats and save",
            confirmVariant: "danger",
        }).then((ok) => {
            if (!ok) return;
            clearPooledHeats = true;
            forms.heats.submit();
        });
        return false;
    }

    function focusFirstError(errors: Record<string, string>) {
        const field = Object.keys(errors)[0];
        if (field) document.getElementById(`s-${field.replace(":", "-")}`)?.focus();
    }

    // Deleting needs the name typed out
    let confirmName = $state("");

    // Classes several parts of the page share
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
    // Two columns from sm; the grid spaces the fields, not .v2-field's own margin
    const fieldGrid = "grid gap-4 *:mt-0 sm:grid-cols-2";
    const heading = "text-lg font-extrabold uppercase";
    const fieldset = "mb-5 flex flex-col gap-[0.6rem] *:mt-0";

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
                bind:this={forms[id]}
                action={updateCompetitionSettings(competition.id)}
                options={{ preserveScroll: true }}
                setDefaultsOnSuccess
                onError={focusFirstError}
                onBefore={id === "heats" ? confirmClearingPools : undefined}
                transform={(data) => (clearPooledHeats ? { ...data, clear_pooled_heats: 1 } : data)}
                onFinish={() => (clearPooledHeats = false)}
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
                            <Switch
                                name="use_pools"
                                label="Pools"
                                checked={settings.use_pools}
                                hint="Heats swim in more than one pool. Not with heats per event."
                            />
                            {#if errors.use_pools || errors.heats_per_event}
                                <p class="v2-error mt-4" role="alert">{errors.use_pools ?? errors.heats_per_event}</p>
                            {/if}
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
        <Accounts
            {accounts}
            {invites}
            accessGroups={[{ label: null, types: accessTypes }]}
            of="this competition"
            adminHint="Admin gives them everything."
            routes={{
                invite: inviteCompetitionAccount(competition.id),
                update: (account) => editCompetitionAccount({ comp: competition.id, account }),
                remove: (account) => deleteCompetitionAccount({ comp: competition.id, account }).url,
                cancelInvite: (invite) => cancelCompetitionInvite({ comp: competition.id, invite }).url,
            }}
            headingId="s-accounts"
        />
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
