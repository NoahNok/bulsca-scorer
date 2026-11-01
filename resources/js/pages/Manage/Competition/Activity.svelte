<!--
    A competition's Activity (manage.competitions.activity), in CompetitionLayout, Organisers only: its
    ActivityFeedView, filtered to this competition, with pickers for its Events, teams and people.
-->
<script lang="ts">
    import ActivityFeedView from "@/manage/activity/ActivityFeedView.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import type { ActivityChipGroup, ActivityEntry, ActivityFilters, ActivityPicker, CompetitionHeader } from "@/manage/types";

    let {
        competition,
        overviewUrl,
        ...feed
    }: {
        competition: CompetitionHeader;
        overviewUrl: string;
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
    <title>Activity - {competition.name} - Scoring.Events</title>
</svelte:head>

<PageHeading back={{ href: overviewUrl, label: "Overview" }} title="Activity" lead="Everything done on this competition, newest first." />

<ActivityFeedView {...feed} />
