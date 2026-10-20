<!--
    Sign in to Management (and Admin), on app. Officials judging on the day sign in to the Digital Judge instead.
-->
<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController";
    import Alert from "@/manage/components/Alert.svelte";
    import Field from "@/manage/components/Field.svelte";
    import LegalNotice from "@/manage/components/LegalNotice.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import judge from "@/routes/judge";
    import password from "@/routes/password";
    import { Form } from "@inertiajs/svelte";
</script>

<svelte:head>
    <title>Sign in - Scoring.Events</title>
</svelte:head>

<h1 class="a-title">Sign in</h1>
<p class="a-lead">Run your competitions and organisations.</p>

<Form action={store()} resetOnError={["password"]} class="a-form">
    {#snippet children({ errors, processing })}
        {#if errors.auth}
            <Alert tone="danger" title="Couldn't sign you in">{errors.auth}</Alert>
        {/if}

        <Field id="email" name="email" type="email" label="Email" autocomplete="username" required error={errors.email} />
        <Field
            id="password"
            name="password"
            type="password"
            label="Password"
            autocomplete="current-password"
            required
            error={errors.password}
        />

        <div class="s-row">
            <label class="v2-check"><input type="checkbox" name="remember" value="1" /> Remember me</label>
            <!-- Still Blade -->
            <a href={password.request().url} class="v2-link">Forgot password?</a>
        </div>

        <SubmitButton {processing}>Sign in</SubmitButton>
        <LegalNotice />
    {/snippet}
</Form>

<p class="a-aside">
    Judging today? <a href={judge.login().url} class="v2-link">Go to the Digital Judge</a>
</p>

<style>
    .s-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.875rem;
    }
</style>
