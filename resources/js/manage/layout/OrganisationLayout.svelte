<!--
    Every organisation page in the Management app (pages/Manage/Organisation/*), inside ManageLayout: the
    organisation's name and logo, and its page tabs. It reads the page's `organisation` prop
    (App\Helpers\OrganisationHeader::for), so a page only has to send that.
-->
<script lang="ts">
    import PageTabs from "@/manage/components/PageTabs.svelte";
    import type { OrganisationHeader } from "@/manage/types";
    import { page } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    let { children }: { children?: Snippet } = $props();

    const org = $derived(page.props.organisation as OrganisationHeader);
</script>

<header class="flex items-center justify-between gap-6">
    <h1 class="min-w-0 text-[clamp(1.75rem,3.5vw,2.5rem)] leading-[1.1] font-extrabold wrap-anywhere uppercase">{org.name}</h1>
    {#if org.logo}
        <img
            src={org.logo}
            alt="{org.name}'s logo"
            class="size-14 shrink-0 rounded-full border border-v2-border object-contain max-sm:size-11"
        />
    {/if}
</header>

{#key org.id}
    <PageTabs tabs={org.tabs} label="Organisation" />
{/key}

<div class="mt-8">
    {@render children?.()}
</div>
