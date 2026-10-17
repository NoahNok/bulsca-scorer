import type { ProfilePhoto } from "./presence";

/** A message from a Referee to every Official on the competition. */
export type Announcement = {
    id: number;
    text: string;
    sender: { id: number; name: string; photo: ProfilePhoto | null } | null;
    // ISO
    at: string;
    // only Referees get withdrawn Announcements
    withdrawn: { by: { id: number; name: string } | null; at: string } | null;
};

export const ANNOUNCEMENT_MAX_LENGTH = 280;

export function isWithdrawn(announcement: Announcement): boolean {
    return !!announcement.withdrawn;
}

/** The sheet's card: voided once withdrawn, like a rejected submission. */
export function announcementCardClass(
    announcement: Announcement,
    unread = false,
): string {
    if (isWithdrawn(announcement)) return "bg-white opacity-60";
    return unread ? "bg-se/5" : "bg-white";
}

/** The card's text, greyed once withdrawn. */
export function announcementTextClass(announcement: Announcement): string {
    return isWithdrawn(announcement) ? "text-gray-400" : "text-gray-900";
}

/** The banner pinned under the header: amber, it needs attention now. */
export const announcementBannerClass =
    "border-amber-200 bg-amber-50 text-amber-900";
