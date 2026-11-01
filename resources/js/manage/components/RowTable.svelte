<!--
    A simple list as a table of whole-row links, each ending in a RowIcon chevron. `columns` names the
    cells `row` renders, for screen readers (the header row is sr-only). Give the row's own link or button
    `rowLink` (or .v2-row-link plus its own type), and anything else clickable in it .v2-over-row.
-->
<script lang="ts" module>
    /** The name that is a row's link: bold, teal while the row is hovered or focused */
    export const rowLink = "v2-row-link font-bold wrap-anywhere group-hover:text-v2-teal-text group-focus-within:text-v2-teal-text";
</script>

<script lang="ts" generics="T extends { id: string | number }">
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import type { Snippet } from "svelte";

    let { rows, columns, row }: { rows: T[]; columns: string[]; row: Snippet<[T]> } = $props();
</script>

<table class="w-full border-collapse">
    <thead class="sr-only">
        <tr>
            {#each columns as column (column)}<th>{column}</th>{/each}
            <th></th>
        </tr>
    </thead>
    <tbody>
        {#each rows as item (item.id)}
            <!-- Cells inset at the ends, so a hovered row's shading doesn't sit flush with its text -->
            <tr class="v2-row group *:border-t *:border-v2-border *:py-3.5 *:pr-4 *:align-middle *:first:pl-3 *:last:pr-3">
                {@render row(item)}
                <td class="w-px"><RowIcon /></td>
            </tr>
        {/each}
    </tbody>
</table>
