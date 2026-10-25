<!--
    A speed event's Settings (manage.competitions.events.speeds.settings), in CompetitionLayout. A link back
    to the event, then a SideMenu like competition Settings, each part saved on its own: Setup (what it
    scores and whether it takes penalties), Scoring, and deleting it with its name typed out.
-->
<script lang="ts">
    import { destroy, saveScoring, saveSetup, show } from "@/actions/App/Http/Controllers/Manage/SpeedEventController";
    import { confirm } from "@/lib/confirm";
    import Field from "@/manage/components/Field.svelte";
    import SideMenu, { type SideMenuItem } from "@/manage/components/SideMenu.svelte";
    import SideMenuPart from "@/manage/components/SideMenuPart.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import ScoringSchemaEditor from "@/manage/events/ScoringSchemaEditor.svelte";
    import type { CompetitionHeader, EventScoring, EventTarget, SpeedEventHeader } from "@/manage/types";
    import { Form, Link, router } from "@inertiajs/svelte";
    import { TARGET_LABELS } from "@/manage/ui";

    let {
        competition,
        event,
        scoring,
    }: {
        competition: CompetitionHeader;
        event: SpeedEventHeader;
        scoring: EventScoring;
    } = $props();

    const PARTS: SideMenuItem[] = [
        { id: "setup", label: "Setup" },
        { id: "scoring", label: "Scoring" },
        { id: "delete", label: "Delete", rule: true, danger: true },
    ];

    const comp = $derived(competition.id);

    // Setup: changing what it scores starts its results again, so ask first
    // svelte-ignore state_referenced_locally
    let target = $state(event.target);
    // svelte-ignore state_referenced_locally
    let hasPenalties = $state(event.hasPenalties);
    let setupErrors = $state<Record<string, string>>({});
    let setupSaving = $state(false);
    const setupDirty = $derived(target !== event.target || hasPenalties !== event.hasPenalties);

    function resetSetup() {
        target = event.target;
        hasPenalties = event.hasPenalties;
        setupErrors = {};
    }

    async function saveSetupPart(submit: SubmitEvent) {
        submit.preventDefault();
        if (target !== event.target) {
            const ok = await confirm({
                title: `Score ${TARGET_LABELS[target].toLowerCase()} instead?`,
                description: `This deletes all of ${event.name}'s results, and starts an empty one for each of the ${TARGET_LABELS[target].toLowerCase()}.`,
                confirmLabel: "Delete results and save",
                confirmVariant: "danger",
            });
            if (!ok) return;
        }
        router.post(saveSetup({ comp, event: event.id }).url, { target_entity: target, has_penalties: hasPenalties }, {
            preserveScroll: true,
            onStart: () => (setupSaving = true),
            onFinish: () => (setupSaving = false),
            onSuccess: () => (setupErrors = {}),
            onError: (errors) => (setupErrors = errors),
        });
    }

    // Deleting needs the name typed out
    let confirmName = $state("");

    const heading = "text-lg font-extrabold uppercase";
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
</script>

<svelte:head>
    <title>Settings - {event.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

<div class="mb-6">
    <Link href={show({ comp, event: event.id }).url} class="v2-link-muted text-sm wrap-anywhere">← {event.name}</Link>
    <h2 class="mt-1 text-2xl font-extrabold uppercase">Settings</h2>
</div>

<SideMenu items={PARTS} label="{event.name} settings">
    <SideMenuPart id="setup" labelledby="sp-setup" ondiscard={resetSetup}>
        <h2 id="sp-setup" class={heading}>Setup</h2>
        <p class={lead}>What {event.name} scores, and whether it takes penalties.</p>
        <form onsubmit={saveSetupPart}>
            <div class="v2-field mt-0 max-w-sm">
                <label for="sp-target">Scores</label>
                <select id="sp-target" class="v2-input w-full" bind:value={target}>
                    {#each Object.entries(TARGET_LABELS) as [value, label] (value)}
                        <option {value}>{label}</option>
                    {/each}
                </select>
                {#if target !== event.target}
                    <span class="v2-hint text-v2-danger-text">Saving this deletes all its results.</span>
                {/if}
                {#if setupErrors.target_entity}<span class="v2-error">{setupErrors.target_entity}</span>{/if}
            </div>
            <div class="mt-5">
                <Switch name="has_penalties" label="Has penalties" bind:checked={hasPenalties} hint="Penalties can be entered, and show in its results." />
            </div>

            <UnsavedChanges dirty={setupDirty} />
            <div class="mt-6 flex items-center justify-end gap-4">
                {#if setupDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
                <button type="submit" class="v2-btn v2-btn-primary" disabled={setupSaving}>Save setup</button>
            </div>
        </form>
    </SideMenuPart>

    <SideMenuPart id="scoring" labelledby="sp-scoring">
        <h2 id="sp-scoring" class={heading}>Scoring</h2>
        <p class={lead}>How results become points and positions.</p>
        <ScoringSchemaEditor {scoring} url={saveScoring({ comp, event: event.id }).url} />
    </SideMenuPart>

    <SideMenuPart id="delete" labelledby="sp-delete">
        <h2 id="sp-delete" class={heading}>Delete event</h2>
        <p class={lead}>
            This removes {event.name} with its {event.heats > 0 ? "heats, " : ""}results and scoring. It can't be undone.
        </p>

        <Form action={destroy({ comp, event: event.id })} class="[&_.v2-field]:mb-4 [&_.v2-field]:max-w-sm">
            {#snippet children({ errors, processing })}
                <Field
                    id="sp-delete-name"
                    name="name"
                    label="Type its name to confirm"
                    placeholder={event.name}
                    autocomplete="off"
                    error={errors.name}
                    oninput={(input) => (confirmName = input.currentTarget.value)}
                />
                <div>
                    <button type="submit" class="v2-btn v2-btn-danger" disabled={processing || confirmName !== event.name}>
                        Delete event
                    </button>
                </div>
            {/snippet}
        </Form>
    </SideMenuPart>
</SideMenu>
