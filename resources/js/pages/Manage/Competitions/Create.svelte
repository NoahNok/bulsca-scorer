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
    import Tabs from "@/manage/components/Tabs.svelte";
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
    let owner = $state<string>(organisations.length > 0 ? "organisation" : "personal");

    const personalNote = "only you and the people you invite can manage it.";
</script>

<svelte:head>
    <title>New competition - Scoring.Events</title>
</svelte:head>

<FormPage title="New competition" lead="You can add events, teams and Officials once it's made.">
    <Form action={store()}>
        {#snippet children({ errors, processing })}
            {#if organisations.length > 0}
                <div class="flex flex-col gap-2">
                    <span id="c-owner-label" class="text-sm font-semibold">Who's running it?</span>
                    <Tabs
                        id="c"
                        labelledby="c-owner-label"
                        tabs={owners.map((option) => ({ id: option, label: labels[option] }))}
                        bind:value={owner}
                    >
                        {#snippet panel(option)}
                            {#if option === "organisation"}
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
                            {:else}
                                <p class="text-sm/[1.6] text-v2-muted">A Personal Competition: {personalNote}</p>
                            {/if}
                        {/snippet}
                    </Tabs>
                </div>
            {:else}
                <p class="text-sm/[1.6] text-v2-muted">This will be a Personal Competition: {personalNote}</p>
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
