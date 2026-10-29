<!--
    An Invite for someone who already has an account, signed in to it: accepting it makes them an Organiser of
    the competition or organisation. Someone without one gets InviteNewAccount.
-->
<script lang="ts">
    import { accept, decline } from "@/actions/App/Http/Controllers/AccountInviteController";
    import DeclineInviteDialog from "@/manage/components/DeclineInviteDialog.svelte";
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
    You've been invited as an Organiser of this {invite.to}. Accepting adds it to your account, {invite.email}.
</p>

<Form action={accept(args)} class="a-form">
    {#snippet children({ processing })}
        <SubmitButton {processing}>Accept invite</SubmitButton>
    {/snippet}
</Form>

<p class="a-aside">
    Not for you? <button type="button" class="v2-link cursor-pointer" onclick={() => declineDialog?.open()}>Decline invite</button>
</p>

<DeclineInviteDialog bind:this={declineDialog} action={decline(args)} />
