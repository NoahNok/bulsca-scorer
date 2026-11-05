<!--
    One part of a SideMenu: a section with the menu item's `id`, hidden unless it's the current part, so
    every part stays mounted. Give each part its own form with <UnsavedChanges dirty={isDirty} /> in it:
    leaving a part with unsaved changes asks first, and discarding them remounts the part, back to its
    saved values. `ondiscard` resets anything the page keeps outside the form (a live preview).
-->
<script lang="ts">
    import { DISCARD } from "@/manage/unsaved";
    import { setContext, type Snippet } from "svelte";
    import { getSideMenu } from "./SideMenu.svelte";

    let {
        id,
        labelledby,
        ondiscard,
        children,
    }: {
        /** The menu item's id */
        id: string;
        /** The id of the part's heading */
        labelledby?: string;
        ondiscard?: () => void;
        children: Snippet;
    } = $props();

    const menu = getSideMenu();
    let generation = $state(0);

    setContext(DISCARD, () => {
        generation++;
        ondiscard?.();
    });
</script>

<section {id} aria-labelledby={labelledby} hidden={menu.value !== id}>
    {#key generation}
        {@render children()}
    {/key}
</section>
