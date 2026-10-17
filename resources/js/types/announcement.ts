import type { ProfilePhoto } from "./presence";

/** A message from a Referee to every Official on the competition. */
export type Announcement = {
    id: number;
    text: string;
    sender: { id: number; name: string; photo: ProfilePhoto | null } | null;
    // ISO
    at: string;
};

export const ANNOUNCEMENT_MAX_LENGTH = 280;

/** The sheet's card, tinted while it's new. */
export function announcementCardClass(unread: boolean): string {
    return unread ? "bg-se/5" : "bg-white";
}

/** The banner pinned under the header: amber, it needs attention now. */
export const announcementBannerClass =
    "border-amber-200 bg-amber-50 text-amber-900";
