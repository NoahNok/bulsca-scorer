/*
 * Unsaved changes in the Management app's forms. A form opts in with <UnsavedChanges dirty={isDirty} />
 * inside its snippet; then leaving with changes asks first: SideMenu switching parts (leaveUnsaved), an
 * Inertia visit to another page, and closing or reloading the tab (guardUnsaved, which ManageLayout
 * installs once). Discarding in a SideMenuPart remounts the part, so every control is back to its saved
 * value, Switch and RichText included.
 */
import { confirm } from "@/lib/confirm";
import { router } from "@inertiajs/svelte";

type Entry = { dirty: boolean; discard: () => void };

/** The context key for how the part around a form discards its changes (SideMenuPart remounts itself) */
export const DISCARD = Symbol("discard");

const entries = new Set<Entry>();

export function trackUnsaved(entry: Entry) {
    entries.add(entry);
    return () => entries.delete(entry);
}

function unsaved() {
    return [...entries].filter((entry) => entry.dirty);
}

/** True when there's nothing unsaved, or you chose to discard it */
export async function leaveUnsaved(): Promise<boolean> {
    const dirty = unsaved();
    if (dirty.length === 0) return true;

    const ok = await confirm({
        title: "Discard unsaved changes?",
        description: "You've changed things here that aren't saved yet.",
        confirmLabel: "Discard changes",
        cancelLabel: "Keep editing",
        confirmVariant: "danger",
    });
    // Outside a SideMenuPart nothing remounts, so stop counting the form as unsaved, or the visit
    // carrying on would be stopped again
    if (ok)
        dirty.forEach((entry) => {
            entry.dirty = false;
            entry.discard();
        });
    return ok;
}

/** Asks before an Inertia visit to another page or leaving the site. Returns a function that stops it */
export function guardUnsaved() {
    // Only page visits: a form's own save is a POST and goes ahead
    const stopVisits = router.on("before", (event) => {
        const visit = event.detail.visit;
        if (visit.method !== "get" || visit.url.pathname === location.pathname || unsaved().length === 0) return;

        event.preventDefault();
        leaveUnsaved().then((ok) => {
            if (ok) router.visit(visit.url.href, { preserveScroll: visit.preserveScroll, replace: visit.replace });
        });
    });

    // Plain links, the Blade tabs, reloading and closing: the browser's own prompt
    const onBeforeUnload = (event: BeforeUnloadEvent) => {
        if (unsaved().length > 0) event.preventDefault();
    };
    window.addEventListener("beforeunload", onBeforeUnload);

    return () => {
        stopVisits();
        window.removeEventListener("beforeunload", onBeforeUnload);
    };
}
