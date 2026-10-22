<!--
    Open sign-up is off: accounts come from an Invite, sent by an Organiser. Officials make their own
    account in the Digital Judge.
-->
<script lang="ts">
    import { login } from "@/routes";
    import judge from "@/routes/judge";
    import { Link, page } from "@inertiajs/svelte";
</script>

<svelte:head>
    <title>Accounts are by invite - Scoring.Events</title>
</svelte:head>

<h1 class="a-title">Accounts are by invite</h1>
<p class="a-lead">
    An Organiser adds you to their competition or organisation by email. The invite has a link that sets up your
    account.
</p>
<p class="a-lead">
    Want to run a competition on Scoring.Events? Email
    <a href="mailto:{page.props.legal.contactEmail}" class="v2-link-inline">{page.props.legal.contactEmail}</a>.
</p>

<div class="mt-7">
    <Link href={login()} class="v2-btn v2-btn-primary">Sign in</Link>
</div>

<p class="a-aside">
    Officiating? <a href={judge.register().url} class="v2-link">Create an account in the Digital Judge</a>
</p>
