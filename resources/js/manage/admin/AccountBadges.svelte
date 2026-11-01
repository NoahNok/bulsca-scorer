<!--
    An account's type as badges, on Admin's Users list and a user's page: Full account or Official-only
    account (amber: it can't use Management), and Admin in red.
-->
<script lang="ts">
    let { officialOnly, admin }: { officialOnly: boolean; admin: boolean } = $props();
</script>

<span class="inline-flex flex-wrap items-center gap-1">
    {#if officialOnly}
        <span class="v2-badge is-provisional whitespace-nowrap">Official-only account</span>
    {:else}
        <span class="v2-badge whitespace-nowrap">Full account</span>
    {/if}
    {#if admin}<span class="v2-badge is-danger whitespace-nowrap">Admin</span>{/if}
</span>
