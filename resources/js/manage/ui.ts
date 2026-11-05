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

/** For a <table class="v2-lead-table">: keeps --v2-lead-width at its first column's width, as the name sticks beside it */
export function leadTable(table: HTMLTableElement) {
    const first = table.rows[0].cells[0];
    const observer = new ResizeObserver(() => table.style.setProperty("--v2-lead-width", `${first.getBoundingClientRect().width}px`));
    observer.observe(first);
    return { destroy: () => observer.disconnect() };
}

/** An input's oninput for codes (DQ101, P100), which are capitals however they're typed */
export function upperCase(input: Event & { currentTarget: HTMLInputElement }) {
    const field = input.currentTarget;
    const at = field.selectionStart;
    field.value = field.value.toUpperCase();
    field.setSelectionRange(at, at);
}
