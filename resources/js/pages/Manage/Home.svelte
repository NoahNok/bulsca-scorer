<!--
    Management home. A placeholder until the dashboard is redesigned and moves here
    (docs/adr/0002-public-blade-management-svelte.md). The Blade dashboard is still at / on app.
-->
<script lang="ts">
    import { dashboard } from "@/routes";
    import { page } from "@inertiajs/svelte";
</script>

<svelte:head>
    <title>Management - Scoring.Events</title>
</svelte:head>

<h1 class="m-title">Management</h1>
<p class="m-lead">Signed in as {page.props.auth.user?.name}.</p>

<div class="m-empty">
    <p>Your competitions and organisations appear here once their pages are redesigned.</p>
    <a href={dashboard().url} class="v2-btn v2-btn-primary">Go to your dashboard</a>
</div>

<style>
    .m-title {
        font-weight: 800;
        font-size: 1.75rem;
        text-transform: uppercase;
    }

    .m-lead {
        margin-top: 0.25rem;
        color: var(--v2-muted);
    }

    .m-empty {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
        margin-top: 1.5rem;
        padding: 1.5rem;
        border: 1px dashed var(--v2-border);
        border-radius: 0.75rem;
        color: var(--v2-muted);
    }
</style>
