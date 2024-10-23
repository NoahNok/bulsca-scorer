self.addEventListener("install", (event) => {
    console.log("Service worker installing...");
    // Optional: Force service worker to take control immediately
    event.waitUntil(self.skipWaiting());
});

self.addEventListener("activate", (event) => {
    console.log("Service worker activating...");
    // Optional: Claim clients immediately after activation
    event.waitUntil(self.clients.claim());
});

self.addEventListener("push", function (e) {
    if (!(self.Notification && self.Notification.permission === "granted")) {
        //notifications aren't supported or permission not granted!
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        console.log(msg);
        e.waitUntil(self.registration.showNotification(msg.title, msg));
    }
});
