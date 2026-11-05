<!--
    A search box and button over a list that's searched on the server (Admin's Competitions and Users):
    submitting visits `url` with the search as `q`, or without it when the box is empty, which also
    goes back to the first page. `q` is the url's search, so the box starts with it. A list sent
    whole is filtered in the browser instead, with a bare search input (an organisation's Officials).
-->
<script lang="ts">
    import { router } from "@inertiajs/svelte";

    let {
        url,
        q,
        placeholder,
        label,
    }: {
        url: string;
        q: string;
        placeholder: string;
        /** Names the input for screen readers, e.g. "Search users" */
        label: string;
    } = $props();

    // Seeded from the url's search, then the box's own
    // svelte-ignore state_referenced_locally
    let query = $state(q);

    function search(event: SubmitEvent) {
        event.preventDefault();
        router.get(url, query.trim() ? { q: query.trim() } : {}, { preserveState: true });
    }
</script>

<form role="search" class="mb-4 flex gap-2" onsubmit={search}>
    <input type="search" class="v2-input w-full" {placeholder} aria-label={label} bind:value={query} />
    <button type="submit" class="v2-btn">Search</button>
</form>
