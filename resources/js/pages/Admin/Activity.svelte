<!--
    The Admin Activity Monitor (manage.admin.activity), in AdminLayout: every competition's Activity in an
    ActivityFeedView, newest first. Each entry names its competition, which filters by it or opens it.
    There are no "Add …" pickers: they name things in one competition.
-->
<script lang="ts">
    import ActivityFeedView from "@/manage/activity/ActivityFeedView.svelte";
    import type { ActivityChipGroup, ActivityEntry, ActivityFilters, ActivityPicker } from "@/manage/types";

    let feed: {
        url: string;
        entries: ActivityEntry[];
        olderPage: number | null;
        filters: ActivityFilters;
        chipGroups: ActivityChipGroup[];
        pickers: ActivityPicker[];
        history: { value: string[]; active: boolean };
    } = $props();
</script>

<svelte:head>
    <title>Activity - Admin - Scoring.Events</title>
</svelte:head>

<h2 class="text-lg font-extrabold uppercase">Activity</h2>
<p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">Everything done on every competition, newest first.</p>

<ActivityFeedView {...feed} />
