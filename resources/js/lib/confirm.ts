import { writable } from "svelte/store";

type ConfirmOptions = {
    title?: string;
    description?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    confirmVariant?: "primary" | "secondary" | "success" | "danger";
};

type ConfirmState = ConfirmOptions & {
    open: boolean;
    resolve?: (value: boolean) => void;
};

const confirmStore = writable<ConfirmState>({ open: false });

export const confirm = (opts: ConfirmOptions = {}) => {

    return new Promise<boolean>((resolve) => {
        confirmStore.set({ ...opts, open: true, resolve });
    });
};

export default confirmStore;
