import Echo from "laravel-echo";
import Pusher from "pusher-js";

let echo: Echo<"reverb"> | null | undefined;

/**
 * Whether Reverb is configured for this build. Without it there's no
 * websocket, so live features (like the Online tab) stay hidden.
 */
export const echoEnabled = !!(
    import.meta.env.VITE_REVERB_APP_KEY && import.meta.env.VITE_REVERB_HOST
);

function xsrfToken(): string {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/);
    return match ? decodeURIComponent(match[1]) : "";
}

/**
 * The shared Echo client, connected on first use. Null when Reverb isn't
 * configured.
 */
export function getEcho(): Echo<"reverb"> | null {
    if (echo !== undefined) return echo;
    if (!echoEnabled) return (echo = null);

    const port = Number(import.meta.env.VITE_REVERB_PORT) || undefined;

    echo = new Echo({
        broadcaster: "reverb",
        Pusher,
        key: import.meta.env.VITE_REVERB_APP_KEY as string,
        wsHost: import.meta.env.VITE_REVERB_HOST as string,
        wsPort: port ?? 80,
        wssPort: port ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
        enabledTransports: ["ws", "wss"],
        // The Inertia app has no csrf meta tag, and the token changes on
        // login without a page load, so read the XSRF cookie per request
        channelAuthorization: {
            endpoint: "/broadcasting/auth",
            transport: "ajax",
            customHandler: (
                { socketId, channelName }: { socketId: string; channelName: string },
                callback: (error: Error | null, data: any) => void,
            ) => {
                fetch("/broadcasting/auth", {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-XSRF-TOKEN": xsrfToken(),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({
                        socket_id: socketId,
                        channel_name: channelName,
                    }),
                })
                    .then(async (response) => {
                        if (!response.ok) {
                            throw new Error(
                                `Channel auth failed (${response.status})`,
                            );
                        }
                        callback(null, await response.json());
                    })
                    .catch((error) => callback(error, null));
            },
        },
    });

    return echo;
}
