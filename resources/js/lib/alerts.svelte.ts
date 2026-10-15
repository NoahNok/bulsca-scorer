import { view } from "@/actions/App/Http/Controllers/DigitalJudge/Violation/ViolationController";
import { getEcho } from "@/lib/echo";
import { toast, type ToastVariant } from "@/lib/toast.svelte";
import {
    statusLabels,
    type ViolationAlert,
    type ViolationStatus,
} from "@/types/violation";
import { page, router } from "@inertiajs/svelte";

const MAX_ALERTS = 50;
const TOAST_DURATION = 8000;

type Me = { id: number; name: string };
type Payload = Omit<ViolationAlert, "kind" | "read">;

/**
 * Live DQ/Penalty alerts for the competition the judge is in. Started by
 * `JudgeLayout`, read by the Alerts tab and `AlertsSheet`.
 *
 * Judges hear about their own submissions changing status on their user
 * channel, referees also hear about new and resubmitted ones on
 * `judge.referees.{id}`. Nothing is stored server side, so an alert only
 * arrives if you're online. The history is kept in this device's
 * localStorage so it survives a reload.
 */
class Alerts {
    items = $state<ViolationAlert[]>([]);
    sheetOpen = $state(false);

    unread = $derived(this.items.filter((a) => !a.read).length);

    #competitionId: number | null = null;
    #me: Me | null = null;
    #channels: string[] = [];

    start(competitionId: number, me: Me, isReferee: boolean) {
        const echo = getEcho();
        if (!echo) return;

        this.stop();
        this.#competitionId = competitionId;
        this.#me = me;
        this.items = this.#load();

        const userChannel = `App.Models.User.${me.id}`;
        echo.private(userChannel).listen(
            ".violation.status-changed",
            (e: Payload) => this.#receive(e, "status-changed"),
        );
        this.#channels.push(userChannel);

        if (isReferee) {
            const refereeChannel = `judge.referees.${competitionId}`;
            echo.private(refereeChannel).listen(
                ".violation.submitted",
                (e: Payload) => this.#receive(e, "submitted"),
            );
            this.#channels.push(refereeChannel);
        }
    }

    stop() {
        for (const name of this.#channels) getEcho()?.leave(name);
        this.#channels = [];
        this.#competitionId = null;
        this.#me = null;
        this.items = [];
        this.sheetOpen = false;
    }

    markAllRead() {
        if (!this.unread) return;
        for (const alert of this.items) alert.read = true;
        this.#save();
    }

    markRead(alert: ViolationAlert) {
        const item = this.items.find((a) => alertKey(a) === alertKey(alert));
        if (!item || item.read) return;
        item.read = true;
        this.#save();
    }

    clear() {
        this.items = [];
        this.#save();
    }

    /** Where the alert's "View" link goes. */
    href(alert: ViolationAlert): string {
        return view.url({
            competition: alert.competition_id,
            submission: alert.submission_id,
        });
    }

    #receive(payload: Payload, kind: ViolationAlert["kind"]) {
        // the user channel isn't per competition, and referees don't need
        // telling about their own submissions
        if (payload.competition_id !== this.#competitionId) return;
        if (kind === "submitted" && payload.submitter?.id === this.#me?.id) {
            return;
        }

        // already looking at it: the submission page announces the change
        // itself (toast and highlight), so don't toast it twice
        const onThisSubmission = this.#isViewing(payload.submission_id);

        const alert: ViolationAlert = {
            ...payload,
            kind,
            read: this.sheetOpen || onThisSubmission,
        };
        this.items = [alert, ...this.items].slice(0, MAX_ALERTS);
        this.#save();

        this.#refreshPage(alert);

        if (onThisSubmission) return;

        toast({
            title: alertTitle(alert),
            description: escapeHtml(alertDetail(alert)),
            variant: alertVariant(alert),
            action: { label: "View", href: this.href(alert) },
            onClick: () => this.markRead(alert),
            durationMs: TOAST_DURATION,
            manualClose: false,
        });
    }

    // keep the submissions list, or the submission itself, up to date if
    // that's what's on screen
    #refreshPage(alert: ViolationAlert) {
        if (page.component === "Judge/Competition/Violation/Submissions") {
            router.reload({ only: ["submissions"] });
        } else if (this.#isViewing(alert.submission_id)) {
            router.reload({ only: ["submission", "timeline"] });
        }
    }

    #isViewing(submissionId: string): boolean {
        return (
            page.component === "Judge/Competition/Violation/Submission" &&
            (page.props as { submission?: { id: string } }).submission?.id ===
                submissionId
        );
    }

    #storageKey(): string | null {
        if (!this.#competitionId || !this.#me) return null;
        return `dj-alerts:${this.#competitionId}:${this.#me.id}`;
    }

    #load(): ViolationAlert[] {
        const key = this.#storageKey();
        if (!key) return [];
        try {
            const stored = JSON.parse(localStorage.getItem(key) ?? "[]");
            return Array.isArray(stored) ? stored : [];
        } catch {
            return [];
        }
    }

    #save() {
        const key = this.#storageKey();
        if (!key) return;
        try {
            localStorage.setItem(key, JSON.stringify(this.items));
        } catch {
            // private mode or storage full, the history just won't survive a reload
        }
    }
}

/** Identifies one alert; a submission can have several (each status change). */
export function alertKey(alert: ViolationAlert): string {
    return `${alert.submission_id}:${alert.kind}:${alert.at}`;
}

const statusVerb: Record<ViolationStatus, string> = {
    SUBMITTED: "is pending again",
    ACCEPTED: "was accepted",
    REJECTED: "was rejected",
    APPEALED: "was marked as appealed",
    REMOVED: "was removed",
};

/** Short headline, e.g. "DQ12 accepted" or "New P3 to review". */
export function alertTitle(alert: ViolationAlert): string {
    if (alert.kind === "submitted") {
        return alert.from
            ? `${alert.code} resubmitted for review`
            : `New ${alert.code} to review`;
    }

    return `${alert.code} ${statusLabels[alert.status].toLowerCase()}`;
}

/** Who and where, e.g. "Team A in SERC 1 · by Sam Smith". */
export function alertDetail(alert: ViolationAlert): string {
    const where = `${alert.entity} in ${alert.event}`;
    return alert.kind === "submitted" && alert.submitter
        ? `${where} · by ${alert.submitter.name}`
        : where;
}

/** One sentence for the history, e.g. "DQ12 for Team A in SERC 1 was accepted". */
export function alertText(alert: ViolationAlert): string {
    const what = `${alert.code} for ${alert.entity} in ${alert.event}`;

    if (alert.kind === "submitted") {
        return alert.from ? `${what} was resubmitted` : `${what} was submitted`;
    }

    return `${what} ${statusVerb[alert.status]}`;
}

function alertVariant(alert: ViolationAlert): ToastVariant {
    if (alert.kind === "submitted") return "info";
    if (alert.status === "ACCEPTED") return "success";
    if (alert.status === "REJECTED" || alert.status === "REMOVED") return "error";
    return "info";
}

// toast descriptions are rendered as HTML, and team/event names are typed in
// by organisers
function escapeHtml(text: string): string {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

export const alerts = new Alerts();
