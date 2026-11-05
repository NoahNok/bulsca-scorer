import { execSync } from "node:child_process";
import inertia from "@inertiajs/vite";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import { sentryVitePlugin } from "@sentry/vite-plugin";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig, loadEnv } from "vite";

export default defineConfig(({ command, mode }) => {
    // With APP_SUBDOMAIN_BASE=<lan-ip>.sslip.io, other devices on the network can open the app, so
    // they need the dev server's LAN IP (their localhost is themselves), and every subdomain allowed.
    const env = loadEnv(mode, process.cwd(), "");
    const subdomainBase = env.APP_SUBDOMAIN_BASE ?? "";
    const lanIp = subdomainBase.match(/^(\d+\.\d+\.\d+\.\d+)\.(?:sslip|nip)\.io$/)?.[1];

    // The same commit config/sentry.php reports as the release, so frontend and backend errors line up
    const release = execSync("git rev-parse --short HEAD").toString().trim();

    // Source maps are only built to be uploaded, then deleted, so they're never served publicly
    const uploadSourceMaps = command === "build" && Boolean(env.SENTRY_AUTH_TOKEN);

    return {
        define: {
            "import.meta.env.VITE_SENTRY_RELEASE": JSON.stringify(release),
        },
        build: {
            sourcemap: uploadSourceMaps ? "hidden" : false,
        },
        server: {
            host: "0.0.0.0",
            strictPort: true,
            hmr: {
                host: lanIp ?? "localhost"
            },
            ...(lanIp && {
                cors: { origin: new RegExp(`^https?://(?:.+\\.)?${subdomainBase.replaceAll(".", "\\.")}(?::\\d+)?$`) },
            }),
        },
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/css/v2.css", "resources/css/manage.css", "resources/js/app.ts", "resources/js/manage.ts", "resources/js/legacy-app.js", "resources/js/basic-app.js"],
                refresh: true,
            }),
            inertia(),
            tailwindcss(),
            svelte(),
            wayfinder(),
            ...(uploadSourceMaps
                ? [
                      sentryVitePlugin({
                          org: env.SENTRY_ORG,
                          project: env.SENTRY_PROJECT,
                          authToken: env.SENTRY_AUTH_TOKEN,
                          release: { name: release },
                          sourcemaps: { filesToDeleteAfterUpload: ["./public/build/assets/**/*.map"] },
                          telemetry: false,
                      }),
                  ]
                : []),
        ],
    };
});
