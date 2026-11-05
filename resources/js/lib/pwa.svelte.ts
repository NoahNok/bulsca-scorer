import { toastError } from "@/lib/toast.svelte";
import { router } from "@inertiajs/svelte";

const DISMISSED_KEY = "judge-install-dismissed";

// Chrome's install prompt event, which isn't in TypeScript's DOM types
type BeforeInstallPromptEvent = Event & {
    prompt: () => Promise<void>;
    userChoice: Promise<{ outcome: "accepted" | "dismissed" }>;
};

/**
 * Installing the Digital Judge as an app. Android and desktop Chrome hand us a prompt to show; iOS
 * has none, so people add it to their home screen by hand. Dismissed is per device (localStorage).
 */
class InstallState {
    private prompt = $state<BeforeInstallPromptEvent | null>(null);
    private installed = $state(false);
    dismissed = $state(false);

    get canPrompt() {
        return this.prompt !== null;
    }

    /** iPadOS reports itself as a Mac, so check for touch too */
    get isIos() {
        return (
            /iPhone|iPad|iPod/.test(navigator.userAgent) ||
            (navigator.platform === "MacIntel" && navigator.maxTouchPoints > 1)
        );
    }

    /** Already running as the installed app. iOS has its own flag for it */
    get isStandalone() {
        return (
            this.installed ||
            matchMedia("(display-mode: standalone)").matches ||
            (navigator as Navigator & { standalone?: boolean }).standalone === true
        );
    }

    start() {
        try {
            this.dismissed = localStorage.getItem(DISMISSED_KEY) !== null;
        } catch {
            // storage blocked: the card just comes back next visit
        }

        addEventListener("beforeinstallprompt", (event) => {
            // keep it for the Install button instead of the browser's own mini prompt
            event.preventDefault();
            this.prompt = event as BeforeInstallPromptEvent;
        });

        addEventListener("appinstalled", () => {
            this.installed = true;
            this.prompt = null;
        });
    }

    async install() {
        if (!this.prompt) return;

        // a prompt can only be shown once, and the browser fires a new event if it can be offered again
        const prompt = this.prompt;
        this.prompt = null;
        await prompt.prompt();
    }

    dismiss() {
        this.dismissed = true;
        try {
            localStorage.setItem(DISMISSED_KEY, "1");
        } catch {
            // storage blocked: dismissed for this visit only
        }
    }
}

export const install = new InstallState();

/**
 * Tell people when a navigation fails for lack of a connection: the installed app has no browser UI
 * to show it. Only for visits they started, or every usePoll would pop one while offline. The
 * networkError event doesn't say which visit failed, so track whether one of theirs is in flight.
 */
function toastFailedNavigations() {
    let navigating = false;

    router.on("start", ({ detail: { visit } }) => {
        if (!visit.async && !visit.prefetch) navigating = true;
    });
    router.on("finish", ({ detail: { visit } }) => {
        if (!visit.async && !visit.prefetch) navigating = false;
    });
    router.on("networkError", () => {
        if (navigating) toastError("You're offline", "Check your connection and try again.");
    });
}

/** Call once before the app mounts, so the install prompt isn't missed. */
export function initPwa() {
    install.start();
    toastFailedNavigations();

    // Vite's dev server isn't something to serve offline, so only real builds get the worker
    if (import.meta.env.PROD && "serviceWorker" in navigator) {
        navigator.serviceWorker.register("/judge-sw.js", { scope: "/v2" }).catch(() => {
            // not supported here (e.g. plain http): the app works the same without it
        });
    }
}
