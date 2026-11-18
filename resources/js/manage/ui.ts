/*
 * Small helpers the Management app's pages share.
 */
import type { EventTarget } from "@/manage/types";

/** What each kind of Entity an event scores is called, as a plural ("Teams") */
export const TARGET_LABELS: Record<EventTarget, string> = { club: "Clubs", team: "Teams", competitor: "Competitors" };

/** Closes a <dialog class="v2-modal"> on a click on its backdrop (the dialog itself, outside its content) */
export function closeOnBackdrop(click: MouseEvent) {
    if (click.target === click.currentTarget) (click.currentTarget as HTMLDialogElement).close();
}

/**
 * For a <table class="v2-lead-table">: keeps --v2-lead-width at its first column's width, as the name sticks beside
 * it, and makes a click anywhere in a tr.v2-row follow that row's a[data-row-link] (the name)
 */
export function leadTable(table: HTMLTableElement) {
    const first = table.rows[0].cells[0];
    const observer = new ResizeObserver(() => table.style.setProperty("--v2-lead-width", `${first.getBoundingClientRect().width}px`));
    observer.observe(first);

    // Whole-row links by a click on the table rather than a .v2-row-link's ::after: an ::after on every row,
    // with the sticky cells, ran a long table (a big SERC's draw order) out of memory on iPhones
    function click(event: MouseEvent) {
        const target = event.target as Element;
        if (target.closest("a, button, input, select, textarea, label, summary")) return;
        const link = target.closest("tr.v2-row")?.querySelector<HTMLAnchorElement>("a[data-row-link]");
        if (!link || window.getSelection()?.toString()) return;
        link.dispatchEvent(new MouseEvent("click", event));
    }
    table.addEventListener("click", click);

    return {
        destroy: () => {
            observer.disconnect();
            table.removeEventListener("click", click);
        },
    };
}

/** An input's oninput for codes (DQ101, P100), which are capitals however they're typed */
export function upperCase(input: Event & { currentTarget: HTMLInputElement }) {
    const field = input.currentTarget;
    const at = field.selectionStart;
    field.value = field.value.toUpperCase();
    field.setSelectionRange(at, at);
}
