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

self.addEventListener("notificationclick", function (event) {
    event.notification.close(); // Close the notification

    if (event.action === "url") {
        clients.openWindow(event.notification.data.url); // Open the specific URL
    } else {
        // Handle other actions or do nothing for dismiss
    }
});

function subscribeUser() {
    navigator.serviceWorker.ready
        .then((registration) => {
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    "BPoYue6lS_kql4tTqsJKYEaQxjF6tO8AM6lTHKfQWBPnmSh4TJ03fTAHAJaPs0AvomJ6avT-6M8hJl8mhg6tbm8"
                ),
            };

            return registration.pushManager.subscribe(subscribeOptions);
        })
        .then((pushSubscription) => {
            console.log(
                "Received PushSubscription: ",
                JSON.stringify(pushSubscription)
            );
            storePushSubscription(pushSubscription);
        });
}

self.addEventListener(
    "pushsubscriptionchange",
    (event) => {
        const conv = (val) =>
            self.btoa(String.fromCharCode.apply(null, new Uint8Array(val)));
        const getPayload = (subscription) => ({
            endpoint: subscription.endpoint,
            publicKey: conv(subscription.getKey("p256dh")),
            authToken: conv(subscription.getKey("auth")),
        });

        const subscription = self.registration.pushManager
            .subscribe(event.oldSubscription.options)
            .then((subscription) => storePushSubscription(subscription));
        event.waitUntil(subscription);
    },
    false
);

/**
 * send PushSubscription to server with AJAX.
 * @param {object} pushSubscription
 */
function storePushSubscription(pushSubscription) {
    const token = document
        .querySelector("meta[name=csrf-token]")
        .getAttribute("content");

    fetch("/push", {
        method: "POST",
        body: JSON.stringify(pushSubscription),
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-CSRF-Token": token,
        },
    })
        .then((res) => {
            return res.json();
        })
        .then((res) => {
            console.log(res);
        })
        .catch((err) => {
            console.log(err);
        });
}

/**
 * urlBase64ToUint8Array
 *
 * @param {string} base64String a public vapid key
 */
function urlBase64ToUint8Array(base64String) {
    var padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, "+")
        .replace(/_/g, "/");

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}
