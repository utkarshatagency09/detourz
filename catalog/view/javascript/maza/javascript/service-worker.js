self.addEventListener('push', function (event) {
    if (event.data) {
        const data = event.data.json();
        event.waitUntil(
            self.registration.showNotification(data.title, data)
        );
    }
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    var notificationData = event.notification.data;
    var url = notificationData.url;

    if (url) {
        event.waitUntil(
            clients.openWindow(url)
        );
    }
});
