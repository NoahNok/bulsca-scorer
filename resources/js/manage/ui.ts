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

/** An input's oninput for codes (DQ101, P100), which are capitals however they're typed */
export function upperCase(input: Event & { currentTarget: HTMLInputElement }) {
    const field = input.currentTarget;
    const at = field.selectionStart;
    field.value = field.value.toUpperCase();
    field.setSelectionRange(at, at);
}
