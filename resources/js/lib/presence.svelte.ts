import { getEcho } from "@/lib/echo";
import type { PresenceMember, PresenceStatus } from "@/types/presence";

// no input for this long (or the tab hidden) and you show as idle
const IDLE_AFTER = 60 * 1000;
const POKE_COOLDOWN = 3000;

type Me = { id: number; name: string };
type Channel = NonNullable<ReturnType<typeof getEcho>> extends {
    join(name: string): infer C;
}
    ? C
    : never;

/**
 * Who's online for the competition the judge is in. Joined by `JudgeLayout`,
 * read by the Online tab, `OfficialsSheet` and `PokeAnimation`.
 *
 * Membership comes from the presence channel. Status and pokes are whispers
 * (client events), so nothing goes through the server or is stored.
 */
class Presence {
    members = $state<PresenceMember[]>([]);
    connected = $state(false);
    sheetOpen = $state(false);
    // bumped on each poke for this device, `PokeAnimation` plays on change
    incomingPoke = $state<{ fromName: string; key: number } | null>(null);

    me = $state<Me | null>(null);

    sorted = $derived(
        [...this.members].sort((a, b) => {
            if (a.id === this.me?.id) return -1;
            if (b.id === this.me?.id) return 1;
            return a.name.localeCompare(b.name);
        }),
    );
    count = $derived(this.members.length);

    #channelName: string | null = null;
    #channel: Channel | null = null;
    #status: PresenceStatus = "active";
    #idleTimer: ReturnType<typeof setTimeout> | undefined;
    #lastPoke = 0;

    join(competitionId: number, me: Me) {
        const echo = getEcho();
        if (!echo) return;

        this.leave();
        this.me = me;
        this.#channelName = `judge.officials.${competitionId}`;

        this.#channel = echo
            .join(this.#channelName)
            .here((users: Omit<PresenceMember, "status">[]) => {
                this.connected = true;
                this.members = users.map((u) => ({ ...u, status: "active" }));
                this.#setStatus(this.#currentStatus(), true);
            })
            .joining((user: Omit<PresenceMember, "status">) => {
                if (!this.members.some((m) => m.id === user.id)) {
                    this.members.push({ ...user, status: "active" });
                }
                // let the newcomer know how we're doing
                this.#whisper("status", { id: me.id, status: this.#status });
            })
            .leaving((user: { id: number }) => {
                this.members = this.members.filter((m) => m.id !== user.id);
            })
            .listenForWhisper(
                "status",
                (e: { id: number; status: PresenceStatus }) => {
                    const member = this.members.find((m) => m.id === e.id);
                    if (member) member.status = e.status;
                },
            )
            .listenForWhisper(
                "poke",
                (e: { to: number; fromName: string }) => {
                    if (e.to !== me.id) return;
                    this.incomingPoke = { fromName: e.fromName, key: Date.now() };
                },
            )
            .error(() => {
                this.connected = false;
            });

        this.#watchActivity();
    }

    leave() {
        if (this.#channelName) getEcho()?.leave(this.#channelName);
        this.#unwatchActivity();
        this.#channelName = null;
        this.#channel = null;
        this.members = [];
        this.connected = false;
        this.sheetOpen = false;
        this.me = null;
    }

    /** Send someone a poke. False if it's yourself or too soon after the last one. */
    poke(userId: number): boolean {
        if (!this.me || userId === this.me.id) return false;
        if (Date.now() - this.#lastPoke < POKE_COOLDOWN) return false;

        this.#lastPoke = Date.now();
        this.#whisper("poke", { to: userId, fromName: this.me.name });
        return true;
    }

    #whisper(event: string, data: Record<string, unknown>) {
        this.#channel?.whisper(event, data);
    }

    #currentStatus(): PresenceStatus {
        return document.visibilityState === "visible" ? "active" : "idle";
    }

    #setStatus(status: PresenceStatus, force = false) {
        if (!this.me) return;
        if (status === this.#status && !force) return;

        this.#status = status;
        const mine = this.members.find((m) => m.id === this.me!.id);
        if (mine) mine.status = status;
        this.#whisper("status", { id: this.me.id, status });
    }

    #onActivity = () => {
        clearTimeout(this.#idleTimer);
        this.#idleTimer = setTimeout(() => this.#setStatus("idle"), IDLE_AFTER);
        if (document.visibilityState === "visible") this.#setStatus("active");
    };

    #onVisibility = () => {
        if (document.visibilityState === "visible") {
            this.#onActivity();
        } else {
            clearTimeout(this.#idleTimer);
            this.#setStatus("idle");
        }
    };

    #watchActivity() {
        for (const e of ["pointerdown", "keydown", "scroll"]) {
            window.addEventListener(e, this.#onActivity, { passive: true });
        }
        document.addEventListener("visibilitychange", this.#onVisibility);
        this.#onActivity();
    }

    #unwatchActivity() {
        clearTimeout(this.#idleTimer);
        for (const e of ["pointerdown", "keydown", "scroll"]) {
            window.removeEventListener(e, this.#onActivity);
        }
        document.removeEventListener("visibilitychange", this.#onVisibility);
    }
}

export const presence = new Presence();
