import inertia from "@inertiajs/vite";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig, loadEnv } from "vite";

export default defineConfig(({ mode }) => {
    // With APP_SUBDOMAIN_BASE=<lan-ip>.sslip.io, other devices on the network can open the app, so
    // they need the dev server's LAN IP (their localhost is themselves), and every subdomain allowed.
    const subdomainBase = loadEnv(mode, process.cwd(), "").APP_SUBDOMAIN_BASE ?? "";
    const lanIp = subdomainBase.match(/^(\d+\.\d+\.\d+\.\d+)\.(?:sslip|nip)\.io$/)?.[1];

    return {
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
            wayfinder()

        ],
    };
});
