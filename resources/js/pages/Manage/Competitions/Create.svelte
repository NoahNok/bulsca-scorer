<!--
    New competition: who it's under (an organisation you're a full Organiser of, or a Personal Competition),
    then its name, date, venue and lanes. Everything else is set on the competition once it's made, which is
    where this lands.
-->
<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/Manage/CompetitionCreateController";
    import Field from "@/manage/components/Field.svelte";
    import FormPage from "@/manage/components/FormPage.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import { home } from "@/routes/manage";
    import { Form, Link } from "@inertiajs/svelte";

    let {
        organisations,
        selected,
    }: {
        /** Ones you can add a competition to */
        organisations: { id: number; name: string }[];
        /** From ?org= */
        selected: number | null;
    } = $props();

    const owners = ["organisation", "personal"] as const;
    const labels = { organisation: "Organisation", personal: "Personal" };

    // Most competitions are an organisation's, so start there when you can add to one
    // svelte-ignore state_referenced_locally
    let owner = $state<(typeof owners)[number]>(organisations.length > 0 ? "organisation" : "personal");
    let tabs: HTMLButtonElement[] = [];

    const personalNote = "only you and the people you invite can manage it.";

    // Arrow keys move between the tabs, as in any tablist
    function onTabKey(event: KeyboardEvent) {
        if (event.key !== "ArrowLeft" && event.key !== "ArrowRight") return;
        event.preventDefault();
        owner = owner === "organisation" ? "personal" : "organisation";
        tabs[owners.indexOf(owner)].focus();
    }
</script>

<svelte:head>
    <title>New competition - Scoring.Events</title>
</svelte:head>

<FormPage title="New competition" lead="You can add events, teams and Officials once it's made.">
    <Form action={store()}>
        {#snippet children({ errors, processing })}
            {#if organisations.length > 0}
                <div class="c-owner">
                    <span id="c-owner-label" class="c-label">Who's running it?</span>
                    <div class="v2-tabs" role="tablist" aria-labelledby="c-owner-label" style="--v2-tabs: 2">
                        <span
                            class="v2-tabs-pill"
                            aria-hidden="true"
                            style="transform: translateX({owners.indexOf(owner) * 100}%)"
                        ></span>
                        {#each owners as option, i (option)}
                            <button
                                bind:this={tabs[i]}
                                type="button"
                                role="tab"
                                id="c-tab-{option}"
                                aria-selected={owner === option}
                                aria-controls="c-panel-{option}"
                                tabindex={owner === option ? 0 : -1}
                                onclick={() => (owner = option)}
                                onkeydown={onTabKey}>{labels[option]}</button
                            >
                        {/each}
                    </div>

                    {#if owner === "organisation"}
                        <div id="c-panel-organisation" role="tabpanel" aria-labelledby="c-tab-organisation" class="c-panel">
                            <div class="v2-field">
                                <label for="organisation">Organisation</label>
                                <select
                                    id="organisation"
                                    name="organisation"
                                    class={["v2-input", errors.organisation && "is-invalid"]}
                                    aria-invalid={errors.organisation ? "true" : undefined}
                                    aria-describedby="organisation-hint{errors.organisation ? ' organisation-error' : ''}"
                                    required
                                >
                                    {#if organisations.length > 1 && !selected}
                                        <option value="" selected disabled>Choose an organisation</option>
                                    {/if}
                                    {#each organisations as org (org.id)}
                                        <option value={org.id} selected={org.id === selected}>{org.name}</option>
                                    {/each}
                                </select>
                                <span id="organisation-hint" class="v2-hint">Its Organisers can manage it too.</span>
                                {#if errors.organisation}
                                    <span id="organisation-error" class="v2-error">{errors.organisation}</span>
                                {/if}
                            </div>
                        </div>
                    {:else}
                        <div id="c-panel-personal" role="tabpanel" aria-labelledby="c-tab-personal" class="c-panel">
                            <p class="c-note">A Personal Competition: {personalNote}</p>
                        </div>
                    {/if}
                </div>
            {:else}
                <p class="c-note">This will be a Personal Competition: {personalNote}</p>
            {/if}

            <Field id="name" name="name" label="Name" required minlength={3} maxlength={255} error={errors.name} />
            <Field id="when" name="when" type="date" label="Date" required error={errors.when} />
            <Field
                id="where"
                name="where"
                label="Venue"
                required
                minlength={3}
                maxlength={255}
                hint="The pool, e.g. Cardiff International Pool."
                error={errors.where}
            />
            <Field
                id="lanes"
                name="lanes"
                type="number"
                label="Lanes"
                value="8"
                required
                min={1}
                max={10}
                inputmode="numeric"
                error={errors.lanes}
            />

            <div class="p-actions">
                <SubmitButton {processing}>Create competition</SubmitButton>
                <Link href={home()} class="v2-link-muted">Cancel</Link>
            </div>
        {/snippet}
    </Form>
</FormPage>

<style>
    .c-owner {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .c-label {
        font-size: 0.875rem;
        font-weight: 600;
    }

    .c-panel {
        margin-top: 0.5rem;
    }

    .c-note {
        color: var(--v2-muted);
        font-size: 0.875rem;
        line-height: 1.6;
    }
</style>
