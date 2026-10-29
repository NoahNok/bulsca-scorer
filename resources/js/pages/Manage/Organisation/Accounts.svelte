<!--
    An organisation's Accounts (manage.organisations.accounts, Organisers only), in OrganisationLayout: the
    shared Accounts list, with access in two groups, the organisation's own Admin and what they can open
    in its competitions. Invites start with Overview ticked.
-->
<script lang="ts">
    import { cancelInvite, destroy, invite, update } from "@/actions/App/Http/Controllers/Manage/OrganisationAccountsController";
    import Accounts from "@/manage/components/Accounts.svelte";
    import type { Account, OrganisationHeader, PendingInvite } from "@/manage/types";

    let {
        organisation,
        accounts,
        invites,
        accessTypes,
    }: {
        organisation: OrganisationHeader;
        accounts: Account[];
        invites: PendingInvite[];
        /** Group name to its access keys and names */
        accessTypes: Record<string, Record<string, string>>;
    } = $props();

    const orgId = $derived(organisation.id);
</script>

<svelte:head>
    <title>Accounts - {organisation.name} - Scoring.Events</title>
</svelte:head>

<section aria-labelledby="a-accounts">
    <Accounts
        {accounts}
        {invites}
        accessGroups={Object.entries(accessTypes).map(([label, types]) => ({ label, types }))}
        of="this organisation"
        adminHint="Admin makes them a full Organiser of every competition."
        inviteDefaults={["view"]}
        routes={{
            invite: invite(orgId),
            update: (account) => update({ organisation: orgId, account }),
            remove: (account) => destroy({ organisation: orgId, account }).url,
            cancelInvite: (id) => cancelInvite({ organisation: orgId, invite: id }).url,
        }}
        headingId="a-accounts"
    />
</section>
