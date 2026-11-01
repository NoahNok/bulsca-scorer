<!--
    A competition's Activity (manage.competitions.activity), in CompetitionLayout, Organisers only: the
    ActivityFilters bar, then the ActivityList, with "Load older" appending the next page. Filters live in
    the url (`related`, `by`, `activity`, each |-separated) as on the Admin Activity Monitor, and changing
    one visits the page again, keeping the scroll.
-->
<script lang="ts">
    import ActivityFilters from "@/manage/activity/ActivityFilters.svelte";
    import ActivityList from "@/manage/activity/ActivityList.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import type {
        ActivityChipGroup,
        ActivityEntry,
        ActivityFilters as Filters,
        ActivityPicker,
        CompetitionHeader,
    } from "@/manage/types";
    import { router } from "@inertiajs/svelte";

    let {
        competition,
        overviewUrl,
        url,
        entries,
        olderPage,
        filters,
        chipGroups,
        pickers,
        history,
    }: {
        competition: CompetitionHeader;
        overviewUrl: string;
        url: string;
        entries: ActivityEntry[];
        /** The next page to load, null on the last */
        olderPage: number | null;
        filters: Filters;
        chipGroups: ActivityChipGroup[];
        pickers: ActivityPicker[];
        /** The History shortcut: the Mark, Time and Place types */
        history: { value: string[]; active: boolean };
    } = $props();

    let loadingOlder = $state(false);

    function query(next: Filters) {
        return Object.fromEntries(
            Object.entries(next)
                .filter(([, values]) => values.length > 0)
                .map(([param, values]) => [param, values.join("|")]),
        );
    }

    /** Shows the Activity for these filters, from the newest */
    function visit(next: Filters) {
        router.get(url, query(next), { preserveState: true, preserveScroll: true });
    }

    function toggleRelated(value: string) {
        const related = filters.related.includes(value) ? filters.related.filter((v) => v !== value) : [...filters.related, value];
        visit({ ...filters, related });
    }

    function loadOlder() {
        if (olderPage === null) return;
        router.get(url, { ...query(filters), page: olderPage }, {
            only: ["entries", "olderPage"],
            preserveState: true,
            preserveScroll: true,
            onStart: () => (loadingOlder = true),
            onFinish: () => (loadingOlder = false),
        });
    }
</script>

<svelte:head>
    <title>Activity - {competition.name} - Scoring.Events</title>
</svelte:head>

<PageHeading back={{ href: overviewUrl, label: "Overview" }} title="Activity" lead="Everything done on this competition, newest first." />

<ActivityFilters {filters} {chipGroups} {pickers} {history} onchange={visit} />

<ActivityList {entries} onrelated={toggleRelated} older={olderPage === null ? null : { loading: loadingOlder, load: loadOlder }} />
