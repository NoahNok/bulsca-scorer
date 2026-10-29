<!--
    Why Results sheets and Master sheets can't be made or edited yet: a danger alert linking each
    Digitally Judged event still waiting for the Referee to confirm it.
-->
<script lang="ts">
    import Alert from "@/manage/components/Alert.svelte";
    import type { UnconfirmedEvent } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

    let { unconfirmed }: { unconfirmed: UnconfirmedEvent[] } = $props();
</script>

<Alert tone="danger" title="Waiting for the Referee">
    <p class="mt-1">
        Sheets can't be made or edited until these Digitally Judged events are confirmed:
        {#each unconfirmed as event, i (event.url)}{i > 0 ? ", " : ""}<Link href={event.url} class="v2-link-inline">{event.name}</Link>{/each}.
    </p>
</Alert>
