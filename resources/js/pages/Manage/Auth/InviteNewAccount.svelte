<!--
    An Invite for someone without an account: accepting it creates their account and makes them an
    Organiser of the competition or organisation. Someone who already has one gets InviteExistingAccount.
-->
<script lang="ts">
    import { acceptNewAccount, declineNewAccount } from "@/actions/App/Http/Controllers/AccountInviteController";
    import DeclineInviteDialog from "@/manage/components/DeclineInviteDialog.svelte";
    import Field from "@/manage/components/Field.svelte";
    import LegalNotice from "@/manage/components/LegalNotice.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import { Form } from "@inertiajs/svelte";

    let {
        invite,
    }: {
        invite: { id: string; email: string; name: string; to: "competition" | "organisation" };
    } = $props();

    const args = $derived({ invite: invite.id, email: invite.email });

    let declineDialog = $state<DeclineInviteDialog>();
</script>

<svelte:head>
    <title>Join {invite.name} - Scoring.Events</title>
</svelte:head>

<h1 class="a-title">Join {invite.name}</h1>
<p class="a-lead">
    You've been invited as an Organiser of this {invite.to}. Choose a name and password for your new account.
</p>

<Form action={acceptNewAccount(args)} resetOnError={["password", "password_confirmation"]} class="a-form">
    {#snippet children({ errors, processing })}
        <Field
            id="email"
            label="Email"
            type="email"
            value={invite.email}
            autocomplete="username"
            readonly
            hint="Your invite was sent here."
        />
        <Field id="name" name="name" label="Name" autocomplete="name" required minlength={3} error={errors.name} />
        <Field
            id="password"
            name="password"
            type="password"
            label="Password"
            autocomplete="new-password"
            required
            minlength={6}
            hint="At least 6 characters."
            error={errors.password}
        />
        <Field
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            label="Confirm password"
            autocomplete="new-password"
            required
        />

        <SubmitButton {processing}>Accept and create account</SubmitButton>
        <LegalNotice />
    {/snippet}
</Form>

<p class="a-aside">
    Not for you? <button type="button" class="v2-link cursor-pointer" onclick={() => declineDialog?.open()}>Decline invite</button>
</p>

<DeclineInviteDialog bind:this={declineDialog} action={declineNewAccount(args)} />
