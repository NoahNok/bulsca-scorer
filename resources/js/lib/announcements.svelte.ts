import { index } from "@/actions/App/Http/Controllers/DigitalJudge/AnnouncementController";
import { getEcho } from "@/lib/echo";
import type { Announcement } from "@/types/announcement";
import { SvelteSet } from "svelte/reactivity";

type Me = { id: number; name: string };
type Channel = ReturnType<NonNullable<ReturnType<typeof getEcho>>["join"]>;

// what this device remembers per competition and user
type Stored = {
    // the newest id this device has heard about, so a reload doesn't buzz again
    known: number;
    read: number[];
    dismissed: number[];
};

/**
 * Announcements from the competition's Referees. Started by `JudgeLayout`,
 * read by `AnnouncementBanner`, `AlertsSheet` and the Alerts tab's badge.
 *
 * They're stored on the server, so the list is fetched on start and every
 * time the presence channel (re)subscribes, which covers anything sent or
 * withdrawn (deleted) while this device was offline. New and withdrawn ones arrive live
 * on that same channel. Read and dismissed are per device (localStorage).
 */
class Announcements {
    items = $state<Announcement[]>([]);

    #me = $state<Me | null>(null);
    #read = new SvelteSet<number>();
    #dismissed = new SvelteSet<number>();

    unread = $derived(this.items.filter((a) => !this.isRead(a)).length);

    /** The banner: newest one this device hasn't dismissed. */
    banner = $derived(
        this.items.find(
            (a) => !this.#isMine(a) && !this.#dismissed.has(a.id),
        ) ?? null,
    );

    #competitionId: number | null = null;
    #channel: Channel | null = null;
    #known = 0;
    // read/dismissed are only pruned against a fetched list, never a partial one
    #loaded = false;
    // bumped by every live event, so a fetch that started before one is discarded
    #liveVersion = 0;
    #fetching = false;
    #fetchAgain = false;

    start(competitionId: number, me: Me) {
        this.stop();
        this.#competitionId = competitionId;
        this.#me = me;
        this.#load();

        void this.refresh();

        const echo = getEcho();
        if (!echo) return;

        // the presence channel `presence` joins; joining again returns the
        // same channel, so only stop listening when leaving, never leave it.
        // Subscribing again after a reconnect refetches, since Echo doesn't
        // replay what was missed
        this.#channel = echo
            .join(`judge.officials.${competitionId}`)
            .listen(".pusher:subscription_succeeded", this.#onSubscribed)
            .listen(".announcement.sent", this.#onSent)
            .listen(".announcement.withdrawn", this.#onWithdrawn);
    }

    stop() {
        // `presence` may have left the channel already, so don't join it again
        this.#channel
            ?.stopListening(".pusher:subscription_succeeded", this.#onSubscribed)
            .stopListening(".announcement.sent", this.#onSent)
            .stopListening(".announcement.withdrawn", this.#onWithdrawn);
        this.#channel = null;
        this.#competitionId = null;
        this.#me = null;
        this.items = [];
        this.#read.clear();
        this.#dismissed.clear();
        this.#known = 0;
        this.#loaded = false;
    }

    /**
     * Fetch the current list and buzz once if something new turned up. Called
     * while a fetch is running, it fetches once more afterwards; a response
     * that a live event overtook is thrown away and fetched again.
     *
     * A plain fetch rather than `useHttp`, which needs a component.
     */
    async refresh() {
        if (this.#fetching) {
            this.#fetchAgain = true;
            return;
        }

        this.#fetching = true;
        try {
            do {
                this.#fetchAgain = false;
                await this.#fetch();
            } while (this.#fetchAgain);
        } finally {
            this.#fetching = false;
        }
    }

    async #fetch() {
        const competitionId = this.#competitionId;
        if (!competitionId) return;
        const version = this.#liveVersion;

        try {
            const response = await fetch(index(competitionId).url, {
                credentials: "same-origin",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });
            if (!response.ok) return;
            const items: Announcement[] = await response.json();

            // left or switched competition while this was loading
            if (competitionId !== this.#competitionId) return;

            // something arrived live meanwhile, so this list may be stale
            if (version !== this.#liveVersion) {
                this.#fetchAgain = true;
                return;
            }

            this.items = items;
            this.#loaded = true;
            this.#heardAbout(items);
            this.#save();
        } catch {
            // offline: keep what we had, the next (re)subscribe tries again
        }
    }

    isRead(announcement: Announcement): boolean {
        return this.#isMine(announcement) || this.#read.has(announcement.id);
    }

    markAllRead() {
        if (!this.unread) return;
        for (const a of this.items) this.#read.add(a.id);
        this.#save();
    }

    dismiss(announcement: Announcement) {
        this.#dismissed.add(announcement.id);
        this.#read.add(announcement.id);
        this.#save();
    }

    /** One the Referee on this device just sent, before the event comes back. */
    sent(announcement: Announcement) {
        this.#add(announcement);
        this.#known = Math.max(this.#known, announcement.id);
        this.#save();
    }

    /**
     * One a Referee withdrew, from the live event or this device's own withdraw
     * request. It's deleted, so it goes for everyone.
     */
    withdrawn(id: number) {
        this.#liveVersion++;
        this.items = this.items.filter((a) => a.id !== id);
    }

    #onSubscribed = () => void this.refresh();

    #onSent = (announcement: Announcement) => {
        this.#add(announcement);
        this.#heardAbout([announcement]);
    };

    #onWithdrawn = (e: { id: number }) => this.withdrawn(e.id);

    #add(announcement: Announcement) {
        this.#liveVersion++;
        if (this.items.some((a) => a.id === announcement.id)) return;
        this.items = [announcement, ...this.items];
    }

    // buzz once for anything newer than this device has heard about, unless
    // it's this user's own
    #heardAbout(items: Announcement[]) {
        const fresh = items.filter(
            (a) => a.id > this.#known && !this.#isMine(a),
        );
        const newest = Math.max(this.#known, ...items.map((a) => a.id));
        if (newest === this.#known) return;

        this.#known = newest;
        this.#save();

        if (fresh.length) navigator.vibrate?.(200);
    }

    #isMine(announcement: Announcement): boolean {
        return !!this.#me && announcement.sender?.id === this.#me.id;
    }

    #storageKey(): string | null {
        if (!this.#competitionId || !this.#me) return null;
        return `dj-announcements:${this.#competitionId}:${this.#me.id}`;
    }

    #load() {
        const key = this.#storageKey();
        if (!key) return;
        try {
            const stored: Partial<Stored> = JSON.parse(
                localStorage.getItem(key) ?? "{}",
            );
            this.#known = Number(stored.known) || 0;
            for (const id of stored.read ?? []) this.#read.add(id);
            for (const id of stored.dismissed ?? []) this.#dismissed.add(id);
        } catch {
            // unreadable, start afresh
        }
    }

    #save() {
        const key = this.#storageKey();
        if (!key) return;

        // once there's a fetched list, only remember ids that still exist, so
        // the lists can't grow forever
        const ids = new Set(this.items.map((a) => a.id));
        const keep = (id: number) => !this.#loaded || ids.has(id);
        const stored: Stored = {
            known: this.#known,
            read: [...this.#read].filter(keep),
            dismissed: [...this.#dismissed].filter(keep),
        };
        try {
            localStorage.setItem(key, JSON.stringify(stored));
        } catch {
            // private mode or storage full, it just won't survive a reload
        }
    }
}

export const announcements = new Announcements();
