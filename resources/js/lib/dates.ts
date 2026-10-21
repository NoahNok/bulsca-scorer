/**
 * A Y-m-d date as the v2 pages show it: "27 Sep", with the year added when it isn't this year
 * ("27 Sep 2025"). Read as a local date, so it doesn't shift a day either side of UTC.
 */
export function shortDate(ymd: string): string {
    const date = new Date(`${ymd}T00:00`);
    const sameYear = date.getFullYear() === new Date().getFullYear();

    return date.toLocaleDateString("en-GB", { day: "numeric", month: "short", year: sameYear ? undefined : "numeric" });
}
