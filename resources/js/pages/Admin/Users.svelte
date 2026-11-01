<!--
    Admin's Users (manage.admin.users.index), in AdminLayout: every account, by name, 25 to a page, with
    its type, how many competitions it can manage (directly or through an organisation) and officiate at,
    and when it joined. Search matches name or email on the server, so it covers every page.
-->
<script lang="ts">
    import { shortDate } from "@/lib/dates";
    import AccountBadges from "@/manage/admin/AccountBadges.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import Pagination from "@/manage/components/Pagination.svelte";
    import SearchForm from "@/manage/components/SearchForm.svelte";
    import RowTable, { rowLink } from "@/manage/components/RowTable.svelte";
    import type { Pagination as Page } from "@/manage/types";
    import { index } from "@/routes/manage/admin/users";
    import { Link } from "@inertiajs/svelte";

    type User = {
        id: number;
        name: string;
        email: string;
        officialOnly: boolean;
        admin: boolean;
        competitions: number;
        officiating: number;
        /** Y-m-d */
        joined: string | null;
        url: string;
    };

    let { q, users, pagination }: { q: string; users: User[]; pagination: Page } = $props();
</script>

<svelte:head>
    <title>Users - Admin - Scoring.Events</title>
</svelte:head>

<section aria-labelledby="au-heading">
    <h2 id="au-heading" class="text-lg font-extrabold uppercase">
        Users <span class="text-v2-muted tabular-nums">{pagination.total}</span>
    </h2>
    <p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">Every account on the site, by name.</p>

    <SearchForm url={index().url} {q} placeholder="Search by name or email" label="Search users" />

    {#if users.length > 0}
        <RowTable rows={users} columns={["User", "Account", "Competitions", "Officiating", "Joined"]}>
            {#snippet row(user)}
                <td>
                    <Link href={user.url} class={rowLink}>{user.name}</Link>
                    <span class="block text-sm text-v2-muted wrap-anywhere">{user.email}</span>
                </td>
                <td class="max-sm:hidden"><AccountBadges officialOnly={user.officialOnly} admin={user.admin} /></td>
                <td class="text-sm whitespace-nowrap text-v2-muted tabular-nums max-md:hidden">{user.competitions} competitions</td>
                <td class="text-sm whitespace-nowrap text-v2-muted tabular-nums max-md:hidden">{user.officiating} officiating</td>
                <td class="text-sm whitespace-nowrap text-v2-muted max-lg:hidden">{user.joined ? `Joined ${shortDate(user.joined)}` : ""}</td>
            {/snippet}
        </RowTable>
        <Pagination {pagination} label="Users pages" />
    {:else if q}
        <EmptyState>No users match “{q}”.</EmptyState>
    {:else}
        <EmptyState>No users yet.</EmptyState>
    {/if}
</section>
