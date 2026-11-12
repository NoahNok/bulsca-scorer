<!--
    New user (manage.admin.users.create), in AdminLayout: makes an account that's verified from the start, so
    its email doesn't have to be real, for temporary or shared accounts that sign in with a sign-in QR code.
    Name, email, an optional password, and a Full account switch (off: official-only, Digital Judge only).
    Nothing is emailed. Saving opens the new user's page.
-->
<script lang="ts">
    import Field from "@/manage/components/Field.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import { index, store } from "@/routes/manage/admin/users";
    import { Form, Link } from "@inertiajs/svelte";
</script>

<svelte:head>
    <title>New user - Users - Admin - Scoring.Events</title>
</svelte:head>

<PageHeading
    back={{ href: index().url, label: "Users" }}
    title="New user"
    lead="The account is verified straight away, so the email doesn't have to be a real one, and nothing is emailed to it."
/>

<Form action={store()} class="flex max-w-lg flex-col gap-5">
    {#snippet children({ errors, processing })}
        <Field id="nu-name" name="name" label="Name" required maxlength={255} autocomplete="off" error={errors.name} />
        <Field id="nu-email" name="email" label="Email" type="email" required maxlength={255} autocomplete="off" error={errors.email} />
        <Field
            id="nu-password"
            name="password"
            label="Password (optional)"
            type="password"
            autocomplete="new-password"
            hint="Leave blank for an account that only signs in with a sign-in QR code."
            error={errors.password}
        />
        <Switch
            name="full_account"
            label="Full account"
            hint="Can also sign in here to run competitions and organisations. Off, it's official-only: Digital Judge only."
        />
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-full sm:w-48"><SubmitButton {processing} variant="accent">Add user</SubmitButton></div>
            <Link href={index().url} class="v2-link-muted">Cancel</Link>
        </div>
    {/snippet}
</Form>
