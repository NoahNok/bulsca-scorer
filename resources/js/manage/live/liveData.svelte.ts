/**
 * The live pages' one poll of a competition's SERC progress and status message (LiveController::liveData)
 * every 5s, and a clock ticking every second. LiveLayout's status pill and the page's SERC order read the same one, so
 * calling follow() again with the same url doesn't start a second poll.
 */
import type { LiveTeam } from "@/manage/types";

const POLL_MS = 5000;

class LiveData {
    /** null until the first answer, then whether the last poll worked */
    connected = $state<boolean | null>(null);
    teams = $state<Record<number, LiveTeam>>({});
    /** The Organisers' status message as HTML, undefined until the first poll answers */
    statusMessage = $state<string | null | undefined>(undefined);
    now = $state(new Date());

    constructor(private url: string) {
        this.poll();
        setInterval(() => this.poll(), POLL_MS);
        setInterval(() => (this.now = new Date()), 1000);
    }

    private async poll() {
        try {
            const response = await fetch(this.url, {
                headers: { Accept: "application/json" },
            });
            if (!response.ok) throw new Error(String(response.status));
            const data = await response.json();
            this.teams = data.teams ?? {};
            this.statusMessage = data.statusMessage ?? null;
            this.connected = true;
        } catch {
            this.connected = false;
        }
    }

    /** "waiting", "part" (finished some SERCs, not all) or "done" */
    state(id: number, sercCount: number): "waiting" | "part" | "done" {
        const finished = this.teams[id]?.finished ?? 0;
        if (finished == 0) return "waiting";
        return finished >= sercCount ? "done" : "part";
    }

    /** What a Team's tile says under its name */
    label(id: number, sercCount: number): string {
        const state = this.state(id, sercCount);
        if (state == "done") return sercCount > 1 ? "Finished (Both)" : "Finished";
        if (state == "part") return "Finished (Dry)";

        const start = this.teams[id]?.start;
        if (this.connected === null || start == null) return "Est: –";
        // The server counts on from now each poll; between polls a passed estimate is due now too
        return start * 1000 <= this.now.getTime() ? "Due now" : `Est: ${clock(new Date(start * 1000))}`;
    }
}

const following = new Map<string, LiveData>();

export function follow(url: string): LiveData {
    if (!following.has(url)) following.set(url, new LiveData(url));
    return following.get(url)!;
}

/** "14:05" */
export function clock(date: Date): string {
    return date.toLocaleTimeString("en-GB", {
        hour: "2-digit",
        minute: "2-digit",
    });
}
