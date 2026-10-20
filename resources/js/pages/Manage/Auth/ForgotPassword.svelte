<!--
    Asks for a password reset link. The "link sent" status comes back as a flash, which AuthLayout shows.
-->
<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/Auth/PasswordResetLinkController";
    import Field from "@/manage/components/Field.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import { login } from "@/routes";
    import { Form, Link } from "@inertiajs/svelte";
</script>

<svelte:head>
    <title>Forgot password - Scoring.Events</title>
</svelte:head>

<h1 class="a-title">Forgot your password?</h1>
<p class="a-lead">Enter your account's email and we'll send you a link to choose a new one.</p>

<Form action={store()} resetOnSuccess class="a-form">
    {#snippet children({ errors, processing })}
        <Field
            id="email"
            name="email"
            type="email"
            label="Email"
            autocomplete="username"
            required
            error={errors.email}
        />

        <SubmitButton {processing}>Email me a reset link</SubmitButton>
    {/snippet}
</Form>

<p class="a-aside">
    Remembered it? <Link href={login()} class="v2-link">Back to sign in</Link>
</p>
