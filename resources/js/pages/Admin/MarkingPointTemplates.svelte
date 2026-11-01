<!--
    Admin's marking point templates (manage.admin.marking-point-templates.index), in AdminLayout: every
    template Organisers can pick for a marking point, by name, with whether it's a Range or Choices, the
    one new marking points start on (Default), and how many marking points use it. Each opens its editor.
-->
<script lang="ts">
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import RowTable, { rowLink } from "@/manage/components/RowTable.svelte";
    import { Link } from "@inertiajs/svelte";

    let {
        templates,
        createUrl,
    }: {
        templates: { id: string; name: string; kind: "range" | "choices"; default: boolean; uses: number; url: string }[];
        createUrl: string;
    } = $props();
</script>

<svelte:head>
    <title>Marking point templates - Admin - Scoring.Events</title>
</svelte:head>

<section aria-labelledby="mpt-heading">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 id="mpt-heading" class="text-lg font-extrabold uppercase">
            Marking point templates <span class="text-v2-muted tabular-nums">{templates.length}</span>
        </h2>
        <Link href={createUrl} class="v2-btn v2-btn-accent v2-btn-sm">New template</Link>
    </div>
    <p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">How a marking point is marked. Organisers pick one for each marking point in a SERC.</p>

    {#if templates.length > 0}
        <RowTable rows={templates} columns={["Template", "Kind", "Used by"]}>
            {#snippet row(template)}
                <td>
                    <Link href={template.url} class={rowLink}>{template.name}</Link>
                    {#if template.default}<span class="v2-badge ml-2">Default</span>{/if}
                </td>
                <td class="text-sm whitespace-nowrap text-v2-muted">{template.kind === "range" ? "Range" : "Choices"}</td>
                <td class="text-right text-sm whitespace-nowrap text-v2-muted tabular-nums max-sm:hidden">
                    {template.uses} marking {template.uses === 1 ? "point" : "points"}
                </td>
            {/snippet}
        </RowTable>
    {:else}
        <EmptyState>No templates yet. Organisers need one before they can add marking points.</EmptyState>
    {/if}
</section>
