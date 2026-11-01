<!--
    A user's Admin page (manage.admin.users.show), in AdminLayout: their name, account badges, email and
    when they joined, verified and were last active, a line of their Judge submissions when they have
    any, then Tabs between their Competitions (directly or through an organisation), Organisations and
    Officiating, each a RowTable. It opens on the first with anything in it.
-->
<script lang="ts">
    import { shortDate } from "@/lib/dates";
    import AccountBadges from "@/manage/admin/AccountBadges.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import OfficialRoleBadge from "@/manage/components/OfficialRoleBadge.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import RowTable, { rowLink } from "@/manage/components/RowTable.svelte";
    import Tabs from "@/manage/components/Tabs.svelte";
    import { index } from "@/routes/manage/admin/users";
    import { Link } from "@inertiajs/svelte";

    type Competition = {
        id: number;
        name: string;
        /** Y-m-d */
        when: string | null;
        url: string;
        /** Their own access to it, by name */
        access: string[];
        /** "Via BULSCA (Overview)" for each organisation that gives them access */
        via: string[];
    };

    type Organisation = { id: number; name: string; url: string; access: string[] };

    type Officiating = {
        id: number;
        name: string;
        /** Y-m-d */
        when: string | null;
        url: string;
        role: string;
        roleName: string;
        /** Y-m-d */
        added: string | null;
    };

    let {
        user,
        competitions,
        organisations,
        officiating,
        submissions,
    }: {
        user: {
            id: number;
            name: string;
            email: string;
            officialOnly: boolean;
            admin: boolean;
            /** Y-m-d */
            joined: string | null;
            /** Y-m-d */
            verified: string | null;
            neverSignedIn: boolean;
            /** "3 days ago" */
            lastActive: string | null;
        };
        competitions: Competition[];
        organisations: Organisation[];
        officiating: Officiating[];
        /** Null when they've never submitted one */
        submissions: { pending: number; accepted: number; rejected: number } | null;
    } = $props();

    const lists = $derived([
        { id: "competitions", label: `Competitions ${competitions.length}`, count: competitions.length },
        { id: "organisations", label: `Organisations ${organisations.length}`, count: organisations.length },
        { id: "officiating", label: `Officiating ${officiating.length}`, count: officiating.length },
    ]);

    // The first list with anything in it
    // svelte-ignore state_referenced_locally
    let tab = $state(lists.find((list) => list.count > 0)?.id ?? "competitions");

    const meta = $derived(
        [
            user.joined ? `Joined ${shortDate(user.joined)}` : null,
            user.neverSignedIn ? "Hasn't signed in yet" : user.verified ? `Verified ${shortDate(user.verified)}` : "Email not verified",
            user.lastActive ? `Last active ${user.lastActive}` : null,
        ]
            .filter(Boolean)
            .join(" · "),
    );
</script>

<svelte:head>
    <title>{user.name} - Users - Admin - Scoring.Events</title>
</svelte:head>

<PageHeading back={{ href: index().url, label: "Users" }} title={user.name}>
    {#snippet badges()}<AccountBadges officialOnly={user.officialOnly} admin={user.admin} />{/snippet}
    {#snippet lead()}
        <span class="block wrap-anywhere">{user.email}</span>
        <span class="block text-sm">{meta}</span>
        {#if submissions}
            <span class="block text-sm tabular-nums">
                Judge submissions: {submissions.pending} pending · {submissions.accepted} accepted · {submissions.rejected} rejected
            </span>
        {/if}
    {/snippet}
</PageHeading>

<Tabs tabs={lists} bind:value={tab} id="au" label="{user.name}'s access">
    {#snippet panel(id)}
        <div class="mt-4">
            {#if id === "competitions"}
                {#if competitions.length > 0}
                    <RowTable rows={competitions} columns={["Date", "Competition", "Access"]}>
                        {#snippet row(comp)}
                            <td class="w-28 text-sm font-semibold whitespace-nowrap text-v2-muted max-sm:w-22">{comp.when ? shortDate(comp.when) : ""}</td>
                            <td>
                                <Link href={comp.url} class={rowLink}>{comp.name}</Link>
                                {#if comp.via.length > 0}<span class="block text-sm text-v2-muted">{comp.via.join(" · ")}</span>{/if}
                            </td>
                            <td>
                                <span class="flex flex-wrap justify-end gap-1">
                                    {#each comp.access as access (access)}<span class="v2-badge whitespace-nowrap">{access}</span>{/each}
                                    {#if comp.via.length > 0}<span class="v2-badge whitespace-nowrap">Organisation</span>{/if}
                                </span>
                            </td>
                        {/snippet}
                    </RowTable>
                {:else}
                    <EmptyState>No access to any competitions.</EmptyState>
                {/if}
            {:else if id === "organisations"}
                {#if organisations.length > 0}
                    <RowTable rows={organisations} columns={["Organisation", "Access"]}>
                        {#snippet row(org)}
                            <td><Link href={org.url} class={rowLink}>{org.name}</Link></td>
                            <td>
                                <span class="flex flex-wrap justify-end gap-1">
                                    {#each org.access as access (access)}<span class="v2-badge whitespace-nowrap">{access}</span>{/each}
                                </span>
                            </td>
                        {/snippet}
                    </RowTable>
                {:else}
                    <EmptyState>Not a member of any organisations.</EmptyState>
                {/if}
            {:else if officiating.length > 0}
                <RowTable rows={officiating} columns={["Date", "Competition", "Role"]}>
                    {#snippet row(comp)}
                        <td class="w-28 text-sm font-semibold whitespace-nowrap text-v2-muted max-sm:w-22">{comp.when ? shortDate(comp.when) : ""}</td>
                        <td>
                            <Link href={comp.url} class={rowLink}>{comp.name}</Link>
                            {#if comp.added}<span class="block text-sm text-v2-muted">Added {shortDate(comp.added)}</span>{/if}
                        </td>
                        <td class="text-right"><OfficialRoleBadge role={comp.role} roles={{ [comp.role]: comp.roleName }} /></td>
                    {/snippet}
                </RowTable>
            {:else}
                <EmptyState>Not an official at any competitions.</EmptyState>
            {/if}
        </div>
    {/snippet}
</Tabs>
