// public/assets/js/firebase/firebase-messaging-sw.js
importScripts("https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js");


// Initialize the Firebase app in the service worker
firebase.initializeApp({
    apiKey: "AIzaSyDwH1kfiDSLvjI4V4UxLqZQnIyGH87MBzw",
    authDomain: "waddini-ccbc7.firebaseapp.com",
    databaseURL: "https://waddini-ccbc7-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "waddini-ccbc7",
    storageBucket: "waddini-ccbc7.firebasestorage.app",
    messagingSenderId: "823593320488",
    appId: "1:823593320488:web:d17d3a8ec271fd6a85b51d",
    measurementId: "G-8KDJ0T6YXF"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
    console.log('Background message:', payload);

    // Forward to all clients (tabs)
    self.clients.matchAll().then(clients => {
        clients.forEach(client => {
            client.postMessage({
                type: 'FIREBASE_MESSAGE',
                payload: payload
            });
        });
    });

    // Show browser notification
    const notification = payload.notification || {};
    return self.registration.showNotification(
        notification.title || "New Notification",
        {
            body: notification.body || "",
            icon: "/firebase-logo.png",
            data: payload.data
        }
    );
});
