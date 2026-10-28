<!--
    A SERC's Settings (manage.competitions.events.sercs.settings), in CompetitionLayout. A link back to the
    SERC, then a SideMenu like competition Settings, each part saved on its own: Scoring, and deleting it
    with its name typed out. Its setup has its own page.
-->
<script lang="ts">
    import { destroy, saveScoring, show } from "@/actions/App/Http/Controllers/Manage/SercEventController";
    import Field from "@/manage/components/Field.svelte";
    import SideMenu, { type SideMenuItem } from "@/manage/components/SideMenu.svelte";
    import SideMenuPart from "@/manage/components/SideMenuPart.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import ScoringSchemaEditor from "@/manage/events/ScoringSchemaEditor.svelte";
    import type { CompetitionHeader, EventScoring, SercEventHeader } from "@/manage/types";
    import { Form } from "@inertiajs/svelte";

    let {
        competition,
        event,
        scoring,
    }: {
        competition: CompetitionHeader;
        event: SercEventHeader;
        scoring: EventScoring;
    } = $props();

    const PARTS: SideMenuItem[] = [
        { id: "scoring", label: "Scoring" },
        { id: "delete", label: "Delete", rule: true, danger: true },
    ];

    const comp = $derived(competition.id);

    // Deleting needs the name typed out
    let confirmName = $state("");

    const heading = "text-lg font-extrabold uppercase";
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
</script>

<svelte:head>
    <title>Settings - {event.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

<PageHeading back={{ href: show({ comp, serc: event.id }).url, label: event.name }} title="Settings" />

<SideMenu items={PARTS} label="{event.name} settings">
    <SideMenuPart id="scoring" labelledby="se-scoring">
        <h2 id="se-scoring" class={heading}>Scoring</h2>
        <p class={lead}>How marks become points and positions.</p>
        <ScoringSchemaEditor {scoring} url={saveScoring({ comp, serc: event.id }).url} />
    </SideMenuPart>

    <SideMenuPart id="delete" labelledby="se-delete">
        <h2 id="se-delete" class={heading}>Delete SERC</h2>
        <p class={lead}>This removes {event.name} with its Objectives, marks and scoring. It can't be undone.</p>

        <Form action={destroy({ comp, serc: event.id })} class="[&_.v2-field]:mb-4 [&_.v2-field]:max-w-sm">
            {#snippet children({ errors, processing })}
                <Field
                    id="se-delete-name"
                    name="name"
                    label="Type its name to confirm"
                    placeholder={event.name}
                    autocomplete="off"
                    error={errors.name}
                    oninput={(input) => (confirmName = input.currentTarget.value)}
                />
                <div>
                    <button type="submit" class="v2-btn v2-btn-danger" disabled={processing || confirmName !== event.name}>
                        Delete SERC
                    </button>
                </div>
            {/snippet}
        </Form>
    </SideMenuPart>
</SideMenu>
