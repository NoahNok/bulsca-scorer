// Digital Judge service worker. Its only job is the offline screen: nothing else is cached, as the
// server decides what can be marked. Bump the version when judge-offline.html changes.
const CACHE = "judge-offline-v1";
const OFFLINE_URL = "/judge-offline.html";

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches
            .open(CACHE)
            .then((cache) => cache.add(OFFLINE_URL))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((keys) => Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))))
            .then(() => self.clients.claim()),
    );
});

// Only full page loads. Inertia visits, form posts, assets and websockets are left to the browser
self.addEventListener("fetch", (event) => {
    if (event.request.mode !== "navigate") return;

    event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE_URL)));
});
