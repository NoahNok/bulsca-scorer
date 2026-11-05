<!--
    Puts a form under the unsaved changes guard (manage/unsaved.ts): render it inside the form's snippet
    as <UnsavedChanges dirty={isDirty} />. Then switching SideMenu parts, visiting another page or closing
    the tab asks before throwing its changes away.
-->
<script lang="ts">
    import { DISCARD, trackUnsaved } from "@/manage/unsaved";
    import { getContext } from "svelte";

    let { dirty }: { dirty: boolean } = $props();

    // Outside a SideMenuPart the page is going away anyway, so there's nothing to discard
    const discard = getContext<(() => void) | undefined>(DISCARD) ?? (() => {});
    const entry = { dirty: false, discard };

    $effect(() => {
        entry.dirty = dirty;
    });

    $effect(() => trackUnsaved(entry));
</script>
