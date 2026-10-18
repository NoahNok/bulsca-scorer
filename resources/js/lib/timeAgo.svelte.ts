import { createSubscriber } from "svelte/reactivity";

const TICK = 30_000;

let now = Date.now();

// ticks while anything on screen reads `timeAgo`, so "2 min ago" stays current
const subscribe = createSubscriber((update) => {
    now = Date.now();
    const timer = setInterval(() => {
        now = Date.now();
        update();
    }, TICK);
    return () => clearInterval(timer);
});

/** "Just now", "5 min ago", "2 hr ago", or the date. */
export function timeAgo(iso: string): string {
    subscribe();

    const seconds = Math.max(0, (now - new Date(iso).getTime()) / 1000);
    if (seconds < 60) return "Just now";
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes} min ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours} hr ago`;
    return new Date(iso).toLocaleDateString();
}
