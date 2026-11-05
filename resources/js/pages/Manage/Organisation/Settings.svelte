<!--
    An organisation's Settings (manage.organisations.settings, Organisers only), in OrganisationLayout: a
    SideMenu like competition Settings, so later parts slot in beside it. For now one part, Details: its
    name and logo. Leaving with unsaved changes asks first.
-->
<script lang="ts">
    import { update } from "@/actions/App/Http/Controllers/Manage/OrganisationController";
    import Field from "@/manage/components/Field.svelte";
    import LogoField from "@/manage/components/LogoField.svelte";
    import SideMenu, { type SideMenuItem } from "@/manage/components/SideMenu.svelte";
    import SideMenuPart from "@/manage/components/SideMenuPart.svelte";
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

    const PARTS: SideMenuItem[] = [{ id: "details", label: "Details" }];

    // A picked logo isn't in the form's own dirty check
    let logoPicked = $state(false);

    const heading = "text-lg font-extrabold uppercase";
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
</script>

<svelte:head>
    <title>Settings - {organisation.name} - Scoring.Events</title>
</svelte:head>

<SideMenu items={PARTS} label="Settings">
    <SideMenuPart id="details" labelledby="s-details" ondiscard={() => (logoPicked = false)}>
        <h2 id="s-details" class={heading}>Details</h2>
        <p class={lead}>Its name and logo, shown on its competitions' pages.</p>

        <!-- A file only goes in a POST, so the PATCH is spoofed. Keyed on the logo, so a saved one starts afresh -->
        {#key settings.logo}
            <Form
                action={update(organisation.id).url}
                method="post"
                options={{ preserveScroll: true }}
                setDefaultsOnSuccess
                onSuccess={() => (logoPicked = false)}
            >
                {#snippet children({ errors, processing, isDirty })}
                    <input type="hidden" name="_method" value="PATCH" />
                    <div class="max-w-lg">
                        <Field id="name" name="name" label="Name" required maxlength={255} value={settings.name} error={errors.name} />
                        <LogoField error={errors.logo} current={settings.logo} onpick={(picked) => (logoPicked = picked)} />
                    </div>

                    <UnsavedChanges dirty={isDirty || logoPicked} />
                    <div class="mt-6 flex items-center justify-end gap-4">
                        {#if isDirty || logoPicked}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
                        <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Save</button>
                    </div>
                {/snippet}
            </Form>
        {/key}
    </SideMenuPart>
</SideMenu>
