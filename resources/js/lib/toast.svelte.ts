export type ToastVariant = 'success' | 'error' | 'info';

export type ToastOptions = {
    title: string;
    description?: string;
    variant?: ToastVariant;
    durationMs?: number;
    manualClose: Boolean;
};

export type ToastItem = {
    id: string;
    title: string;
    description?: string;
    variant: ToastVariant;
};

const state = $state<ToastItem[]>([]);

export const toastState = state;

function makeId(): string {
    if (typeof crypto !== 'undefined' && 'randomUUID' in crypto) {
        return crypto.randomUUID();
    }

    return `${Date.now()}-${Math.random().toString(16).slice(2)}`;
}

export function dismissToast(id: string): void {
    const index = state.findIndex((toast) => toast.id === id);

    if (index === -1) {
        return;
    }

    state.splice(index, 1);
}

export function toast(options: ToastOptions): string {
    const id = makeId();
    const variant = options.variant ?? 'info';
    const durationMs = options.durationMs ?? 3000;

    state.push({
        id,
        title: options.title,
        description: options.description,
        variant,
    });

    if (!options.manualClose) {
        window.setTimeout(() => {
            dismissToast(id);
        }, durationMs);
    }

    return id;
}

export function toastSuccess(title: string, description?: string, manualClose: boolean = false): string {
    return toast({ title, description, variant: 'success', manualClose: manualClose });
}

export function toastError(title: string, description?: string, manualClose: boolean = false): string {
    return toast({ title, description, variant: 'error', durationMs: 5000, manualClose: manualClose });
}

export function toastInfo(title: string, description?: string, manualClose: boolean = false): string {
    return toast({ title, description, variant: 'info', manualClose: manualClose });
}

export function fakeToast(toast: ToastItem) {
    state.push(toast);
}
