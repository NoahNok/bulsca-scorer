<!--
    New organisation: a name and an optional logo (picked or dropped, previewed round as it shows on the
    site). You become its first Organiser, and it lands on the organisation's page.
-->
<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/Manage/OrganisationCreateController";
    import Field from "@/manage/components/Field.svelte";
    import LogoField from "@/manage/components/LogoField.svelte";
    import FormPage from "@/manage/components/FormPage.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import { home } from "@/routes/manage";
    import { Form, Link } from "@inertiajs/svelte";
</script>

<svelte:head>
    <title>New organisation - Scoring.Events</title>
</svelte:head>

<FormPage title="New organisation" lead="You'll be its first Organiser, and can invite others from its page.">
    <Form action={store()}>
        {#snippet children({ errors, processing })}
            <Field id="name" name="name" label="Name" required maxlength={255} error={errors.name} />

            <LogoField error={errors.logo} />

            <div class="p-actions">
                <SubmitButton {processing}>Create organisation</SubmitButton>
                <Link href={home()} class="v2-link-muted">Cancel</Link>
            </div>
        {/snippet}
    </Form>
</FormPage>
