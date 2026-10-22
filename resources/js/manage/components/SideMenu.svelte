<!--
    A menu beside a page's content that shows one part at a time (competition Settings): a sticky column
    of links from 56rem, and a row that scrolls sideways above the content on phones. The current part
    follows the url's hash, so a link can open one (/settings#accounts), and the first item has no hash.

    The page renders every part in `children` as a SideMenuPart, which hides itself unless it's the
    current one. Give each part its own form with UnsavedChanges in it: switching away from one with
    unsaved changes asks whether to discard them first.
-->
<script lang="ts" module>
    import { createContext } from "svelte";

    /** The current part's id, for SideMenuPart */
    export const [getSideMenu, setSideMenu] = createContext<{ readonly value: string }>();

    export type SideMenuItem = {
        id: string;
        label: string;
        /** A rule above it, splitting the menu into groups */
        rule?: boolean;
        /** Danger red, for deleting */
        danger?: boolean;
    };
</script>

<script lang="ts">
    import { leaveUnsaved } from "@/manage/unsaved";
    import type { Snippet } from "svelte";

    let {
        items,
        value = $bindable(),
        label,
        children,
    }: {
        items: SideMenuItem[];
        /** The current part's id. Starts from the url's hash, or the first item */
        value?: string;
        /** The nav's accessible name */
        label: string;
        children: Snippet;
    } = $props();

    function fromHash() {
        const hash = typeof location === "undefined" ? "" : location.hash.slice(1);
        return items.some((item) => item.id === hash) ? hash : items[0].id;
    }

    // svelte-ignore state_referenced_locally
    value = fromHash();

    setSideMenu({
        get value() {
            return value ?? items[0].id;
        },
    });

    async function open(event: MouseEvent, id: string) {
        event.preventDefault();
        if (id === value || !(await leaveUnsaved())) return;
        value = id;
        history.replaceState(history.state, "", id === items[0].id ? location.pathname + location.search : `#${id}`);
    }

    // The current item sits on --v2-sunk
    const item =
        "block rounded-lg px-3 py-2 text-[0.9375rem] font-semibold whitespace-nowrap transition-colors hover:bg-v2-row-hover aria-[current=true]:bg-v2-sunk motion-reduce:transition-none";
</script>

<svelte:window onhashchange={() => (value = fromHash())} />

<!-- minmax(0, 1fr) on phones, so the menu's row scrolls instead of widening the page -->
<div class="grid grid-cols-[minmax(0,1fr)] gap-6 min-[56rem]:grid-cols-[11rem_minmax(0,44rem)] min-[56rem]:gap-12">
    <nav aria-label={label} class="min-w-0 min-[56rem]:sticky min-[56rem]:top-6 min-[56rem]:self-start">
        <ul class="-mx-1 flex gap-1 overflow-x-auto px-1 min-[56rem]:mx-0 min-[56rem]:flex-col min-[56rem]:overflow-visible min-[56rem]:px-0">
            {#each items as menuItem (menuItem.id)}
                <li
                    class={[
                        "shrink-0",
                        menuItem.rule &&
                            "ml-2 border-l border-v2-border pl-2 min-[56rem]:mt-2 min-[56rem]:ml-0 min-[56rem]:border-t min-[56rem]:border-l-0 min-[56rem]:pt-2 min-[56rem]:pl-0",
                    ]}
                >
                    <a
                        href="#{menuItem.id}"
                        class={[item, menuItem.danger ? "text-v2-danger-text" : "text-v2-muted hover:text-v2-ink aria-[current=true]:text-v2-ink"]}
                        aria-current={value === menuItem.id}
                        onclick={(event) => open(event, menuItem.id)}>{menuItem.label}</a
                    >
                </li>
            {/each}
        </ul>
    </nav>

    <div class="min-w-0">
        {@render children()}
    </div>
</div>
