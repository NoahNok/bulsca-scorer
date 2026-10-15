export type PresenceStatus = "active" | "idle";

/** An official in a competition's presence channel (`judge.officials.{id}`). */
export type PresenceMember = {
    id: number;
    name: string;
    // "Official", "Referee" or "Admin"
    role: string;
    status: PresenceStatus;
};

/** Someone officiating the competition, online or not. */
export type Official = {
    id: number;
    name: string;
    role: string;
};

export const presenceStatusLabel: Record<PresenceStatus, string> = {
    active: "Active",
    idle: "Idle",
};

export function presenceStatusBadgeClass(status: PresenceStatus): string {
    return status === "active"
        ? "bg-se/20 text-teal-800 ring-se/40"
        : "bg-orange-100 text-orange-700 ring-orange-300";
}

/** The dot on an online official's avatar: teal when active, orange when idle. */
export function presenceStatusDotClass(status: PresenceStatus): string {
    return status === "active" ? "bg-se" : "bg-orange-400";
}
