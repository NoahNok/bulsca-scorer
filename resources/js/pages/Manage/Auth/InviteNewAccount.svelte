<!--
    An Invite for someone without an account: accepting it creates their account and makes them an
    Organiser of the competition or organisation. Someone who already has one gets account/invite.blade.php.
-->
<script lang="ts">
    import { acceptNewAccount, declineNewAccount } from "@/actions/App/Http/Controllers/AccountInviteController";
    import Field from "@/manage/components/Field.svelte";
    import LegalNotice from "@/manage/components/LegalNotice.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import { Form, router } from "@inertiajs/svelte";

    let {
        invite,
    }: {
        invite: { id: string; email: string; name: string; to: "competition" | "organisation" };
    } = $props();

    const args = $derived({ invite: invite.id, email: invite.email });

    let dialog = $state<HTMLDialogElement>();
    let declining = $state(false);

    function decline() {
        router.post(declineNewAccount(args), {}, {
            onStart: () => (declining = true),
            onFinish: () => (declining = false),
        });
    }
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
    Not for you? <button type="button" class="v2-link i-decline" onclick={() => dialog?.showModal()}>Decline invite</button>
</p>

<!-- A native dialog: it traps focus, closes on Escape and focuses the first button, the safe one -->
<dialog
    bind:this={dialog}
    class="v2-modal i-dialog"
    aria-labelledby="decline-title"
    onclick={(e) => e.target === dialog && dialog.close()}
>
    <h2 id="decline-title">Decline this invite?</h2>
    <p>This deletes the invite. The Organiser will need to send a new one if you change your mind.</p>
    <div class="v2-modal-actions">
        <button type="button" class="v2-btn" onclick={() => dialog?.close()}>Keep invite</button>
        <button type="button" class="v2-btn v2-btn-danger" disabled={declining} onclick={decline}>Decline invite</button>
    </div>
</dialog>

<style>
    .i-decline {
        cursor: pointer;
    }

    .i-dialog {
        animation: i-dialog-in 200ms ease-out;
    }

    @keyframes i-dialog-in {
        from {
            opacity: 0;
            transform: translateY(0.5rem);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .i-dialog {
            animation: none;
        }
    }
</style>
