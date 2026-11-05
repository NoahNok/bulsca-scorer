<!--
    Choose a new password, from the link in the reset email. A bad or expired link comes back as an error on
    the email field. Success lands on sign in with a status flash.
-->
<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/Auth/NewPasswordController";
    import Field from "@/manage/components/Field.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import password from "@/routes/password";
    import { Form, Link } from "@inertiajs/svelte";

    let { token, email }: { token: string; email: string | null } = $props();
</script>

<svelte:head>
    <title>Reset password - Scoring.Events</title>
</svelte:head>

<h1 class="a-title">Choose a new password</h1>
<p class="a-lead">Resetting it signs you out on your other devices.</p>

<Form action={store()} resetOnError={["password", "password_confirmation"]} class="a-form">
    {#snippet children({ errors, processing })}
        <input type="hidden" name="token" value={token} />

        <Field
            id="email"
            name="email"
            type="email"
            label="Email"
            value={email ?? undefined}
            autocomplete="username"
            required
            error={errors.email}
        />
        <Field
            id="password"
            name="password"
            type="password"
            label="New password"
            autocomplete="new-password"
            required
            minlength={8}
            hint="At least 8 characters."
            error={errors.password}
        />
        <Field
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            label="Confirm new password"
            autocomplete="new-password"
            required
        />

        <SubmitButton {processing}>Reset password</SubmitButton>
    {/snippet}
</Form>

<p class="a-aside">
    Link expired? <Link href={password.request()} class="v2-link">Send a new one</Link>
</p>
