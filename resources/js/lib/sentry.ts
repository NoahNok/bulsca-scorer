import { router } from "@inertiajs/svelte";
import * as Sentry from "@sentry/svelte";

// Error tracking for the Digital Judge and Management apps. See docs/error-tracking.md.

const stripQuery = (url: string) => url.split(/[?#]/)[0];

export function initSentry(app: "judge" | "manage") {
    const dsn = import.meta.env.VITE_SENTRY_DSN;

    // No DSN in local development, and app.ts is also loaded by the dev SSR renderer
    if (!dsn || typeof window === "undefined") return;

    Sentry.init({
        dsn,
        environment: import.meta.env.MODE,
        release: import.meta.env.VITE_SENTRY_RELEASE,
        // The browser and device, which help with bugs on judges' phones, and nothing else about the visit
        dataCollection: {
            userInfo: false,
            cookies: false,
            httpHeaders: { request: { allow: ["User-Agent"] }, response: false },
            httpBodies: [],
            urlQueryParams: false,
        },
        // Console output can hold page data, and isn't needed to follow what led to an error
        integrations: (defaults) => defaults.filter((integration) => integration.name !== "Console"),
        // Only our own bundles (or the dev server's files), not browser extensions and other scripts
        allowUrls: [/\/build\/assets\//, /\/resources\/js\//],
        // A phone losing signal at the pool isn't a bug. Exact matches, to keep the failed page chunks that a
        // deploy can cause ("Failed to fetch dynamically imported module")
        ignoreErrors: [
            /^(TypeError: )?Failed to fetch$/,
            /^(TypeError: )?Load failed$/,
            /^(TypeError: )?NetworkError when attempting to fetch resource\.?$/,
            /^ResizeObserver loop/,
        ],
        // Query strings can hold emails (password reset links)
        beforeSend(event) {
            if (event.request?.url) event.request.url = stripQuery(event.request.url);
            delete event.request?.query_string;
            return event;
        },
        beforeBreadcrumb(breadcrumb) {
            for (const key of ["url", "from", "to"]) {
                const value = breadcrumb.data?.[key];
                if (typeof value === "string") breadcrumb.data![key] = stripQuery(value);
            }
            return breadcrumb;
        },
    });

    Sentry.setTag("app", app);

    // Also fires for the first page. IDs only, as on the backend (app/Support/ErrorContext.php)
    router.on("navigate", (event) => {
        const { component, props } = event.detail.page;
        const { auth, competition, manage } = props as {
            auth?: { user?: { id?: number } | null };
            competition?: { id?: number };
            manage?: { isAdmin?: boolean };
        };
        const userId = auth?.user?.id;

        Sentry.setTag("page", component);
        Sentry.setTag("competition", competition?.id);
        Sentry.setTag("user_role", manage?.isAdmin ? "admin" : userId ? "user" : "guest");
        Sentry.setUser(userId ? { id: userId } : null);
    });
}
