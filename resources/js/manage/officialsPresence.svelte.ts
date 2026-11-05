import { getEcho } from "@/lib/echo";
import { SvelteMap } from "svelte/reactivity";

/** Using Digital Judge now, or there with the tab hidden or untouched for a minute */
export type LiveStatus = "active" | "idle";

type Joined = { id: number; watching?: true };

/**
 * Who's on Digital Judge for a competition right now, for its Officials page. It joins the judge app's
 * presence channel (`judge.officials.{id}`) as a watcher: routes/channels.php marks an Organiser joining
 * from app. as one, and the judge app leaves watchers out of its Online list. Officials whisper their
 * Active/Idle status to everyone when someone joins and whenever it changes.
 */
export class OfficialsPresence {
    /** Officials online, by user id. Empty until connected */
    statuses = new SvelteMap<number, LiveStatus>();
    /** False without Reverb, before the channel answers, or after an error: use the server's status then */
    connected = $state(false);

    #channelName: string | null = null;

    join(competitionId: number) {
        const echo = getEcho();
        if (!echo) return;

        this.leave();
        this.#channelName = `judge.officials.${competitionId}`;

        echo.join(this.#channelName)
            .here((users: Joined[]) => {
                this.statuses.clear();
                for (const user of users) if (!user.watching) this.statuses.set(user.id, "active");
                this.connected = true;
            })
            .joining((user: Joined) => {
                if (!user.watching) this.statuses.set(user.id, "active");
            })
            .leaving((user: Joined) => {
                if (!user.watching) this.statuses.delete(user.id);
            })
            .listenForWhisper("status", (e: { id: number; status: LiveStatus }) => {
                if (this.statuses.has(e.id)) this.statuses.set(e.id, e.status);
            })
            .error(() => {
                this.connected = false;
            });
    }

    leave() {
        if (this.#channelName) getEcho()?.leave(this.#channelName);
        this.#channelName = null;
        this.statuses.clear();
        this.connected = false;
    }
}
