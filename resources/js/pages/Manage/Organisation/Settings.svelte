<!--
    An organisation's Settings (manage.organisations.settings, Organisers only), in OrganisationLayout: its
    name and logo, in one form. Leaving with unsaved changes asks first.
-->
<script lang="ts">
    import { update } from "@/actions/App/Http/Controllers/Manage/OrganisationController";
    import Field from "@/manage/components/Field.svelte";
    import LogoField from "@/manage/components/LogoField.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { OrganisationHeader } from "@/manage/types";
    import { Form } from "@inertiajs/svelte";

    let {
        organisation,
        settings,
    }: {
        organisation: OrganisationHeader;
        settings: { name: string; logo: string | null };
    } = $props();

    let logoPicked = $state(false);
</script>

<svelte:head>
    <title>Settings - {organisation.name} - Scoring.Events</title>
</svelte:head>

<section aria-labelledby="s-heading" class="max-w-lg">
    <h2 id="s-heading" class="mb-4 text-lg font-extrabold uppercase">Settings</h2>

    <!-- A file only goes in a POST, so the PATCH is spoofed. Keyed on the logo, so a saved one starts afresh -->
    {#key settings.logo}
        <Form action={update(organisation.id).url} method="post" options={{ preserveScroll: true }} setDefaultsOnSuccess onSuccess={() => (logoPicked = false)}>
            {#snippet children({ errors, processing, isDirty })}
                <input type="hidden" name="_method" value="PATCH" />
                <Field id="name" name="name" label="Name" required maxlength={255} value={settings.name} error={errors.name} />
                <LogoField error={errors.logo} current={settings.logo} onpick={(picked) => (logoPicked = picked)} />

                <UnsavedChanges dirty={isDirty || logoPicked} />
                <div class="mt-6 flex items-center justify-end gap-4">
                    {#if isDirty || logoPicked}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
                    <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Save</button>
                </div>
            {/snippet}
        </Form>
    {/key}
</section>
