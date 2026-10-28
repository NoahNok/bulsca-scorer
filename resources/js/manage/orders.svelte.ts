/*
 * What the heat and draw editors share. Each change shows on the page as soon as it's made: the page
 * changes its own copy of the Heats or Draw, highlights what moved, then saves. The props that come back
 * replace its copy either way: the saved state, or after an error the state before it, which puts the
 * change back, with why in a toast. A save that gets no props back (not found, no connection) reloads them.
 */
import { toastError } from "@/lib/toast.svelte";
import { router } from "@inertiajs/svelte";
import type { FormDataConvertible } from "@inertiajs/core";

/** An editor's save in flight: one at a time, so a second change can't land on a page still catching up */
export class ChangeSaver {
    busy = $state(false);
    /** What the last change moved (cell or Draw keys), lit up for a moment */
    changed = $state<Set<string>>(new Set());
    #timer: ReturnType<typeof setTimeout> | undefined;

    /** Lights up `keys` and saves the change the page already shows */
    save(url: string, data: Record<string, FormDataConvertible>, keys: string[] = []) {
        this.busy = true;
        this.highlight(keys);

        router.post(url, data, {
            preserveScroll: true,
            preserveState: true,
            onError: (errors) => this.#failed(Object.values(errors)[0]),
            // Not found (moved or removed elsewhere) or no answer: no props come back, so fetch the saved state
            onHttpException: () => {
                this.#failed("That change couldn't be saved. Showing the latest saved version.");
                router.reload();
                return false;
            },
            onNetworkError: () => {
                this.#failed("That change couldn't be saved. Check your connection.");
                router.reload();
                return false;
            },
            onFinish: () => (this.busy = false),
        });
    }

    #failed(message?: string) {
        this.changed = new Set();
        toastError(message ?? "That change couldn't be saved.");
    }

    highlight(keys: string[]) {
        clearTimeout(this.#timer);
        this.changed = new Set(keys);
        this.#timer = setTimeout(() => (this.changed = new Set()), 1200);
    }
}
